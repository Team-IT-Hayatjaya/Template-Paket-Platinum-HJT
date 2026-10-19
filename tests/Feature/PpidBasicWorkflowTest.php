<?php

use App\Mail\PermohonanJawabanMail;
use App\Mail\PermohonanKonfirmasiMail;
use App\Models\DokumenInformasi;
use App\Models\Halaman;
use App\Models\KategoriInformasi;
use App\Models\PengaturanPpid;
use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use App\Models\User;
use App\Services\PpidService;
use App\Support\Package;
use Carbon\Carbon;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\PengaturanPpidSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusPermohonanSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(KategoriInformasiSeeder::class);
    $this->seed(StatusPermohonanSeeder::class);
    $this->seed(PengaturanPpidSeeder::class);
    $this->seed(HalamanSeeder::class);
    Storage::fake('private');
    Storage::fake('public');
});

/*
|--------------------------------------------------------------------------
| 1. UJI PEMBUATAN NOMOR REGISTRASI
|--------------------------------------------------------------------------
*/

test('nomor registrasi otomatis dibuat dengan format PPID/KODE-INSTANSI/TAHUN/URUT', function () {
    $pengaturan = PengaturanPpid::first();
    $kodeInstansi = $pengaturan->kode_instansi; // misal KEC-LK
    $tahun = date('Y');

    $permohonan1 = PermohonanInformasi::create([
        'nama_pemohon' => 'Ahmad Dahlan',
        'email' => 'ahmad@example.com',
        'telepon' => '081234567890',
        'alamat' => 'Jl. Pangeran Diponegoro No. 12',
        'pekerjaan' => 'Wiraswasta',
        'rincian_informasi' => 'Rincian anggaran pembangunan jembatan',
        'tujuan_penggunaan' => 'Kajian transparansi publik',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    expect($permohonan1->nomor_registrasi)->toBe("PPID/{$kodeInstansi}/{$tahun}/0001");

    $permohonan2 = PermohonanInformasi::create([
        'nama_pemohon' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'telepon' => '081298765432',
        'alamat' => 'Jl. Sudirman No. 5',
        'rincian_informasi' => 'Dokumen DPA Dinas Pendidikan',
        'tujuan_penggunaan' => 'Skripsi mahasiswa',
        'cara_memperoleh_informasi' => 'soft_copy',
        'cara_mendapat_salinan' => 'email',
    ]);

    expect($permohonan2->nomor_registrasi)->toBe("PPID/{$kodeInstansi}/{$tahun}/0002");
});

test('nomor registrasi unik dan tidak pernah duplikat meskipun ada nomor registrasi manual', function () {
    $tahun = date('Y');
    $pengaturan = PengaturanPpid::first();
    $kode = $pengaturan->kode_instansi;

    // Sengaja buat satu permohonan dengan nomor 0001
    PermohonanInformasi::create([
        'nomor_registrasi' => "PPID/{$kode}/{$tahun}/0001",
        'nama_pemohon' => 'User Pertama',
        'email' => 'user1@example.com',
        'telepon' => '0811111111',
        'alamat' => 'Alamat 1',
        'rincian_informasi' => 'Rincian 1',
        'tujuan_penggunaan' => 'Tujuan 1',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    // Permohonan berikutnya yang otomatis dibuat tidak boleh bentrok
    $nomorBaru = PermohonanInformasi::generateNomorRegistrasi($kode);
    expect($nomorBaru)->toBe("PPID/{$kode}/{$tahun}/0002");
});

/*
|--------------------------------------------------------------------------
| 2. UJI PENGHITUNGAN TENGGAT 10 HARI KERJA
|--------------------------------------------------------------------------
*/

test('penghitungan tenggat 10 hari kerja mengecualikan sabtu dan minggu', function () {
    // Tetapkan tanggal mulai: Senin 2026-10-05
    $startDate = Carbon::create(2026, 10, 5, 9, 0, 0); // Hari Senin
    $deadline = PermohonanInformasi::calculateDeadline($startDate, 10);

    // 10 hari kerja dari Senin 5 Okt:
    // Minggu 1: 6 (Selasa), 7 (Rabu), 8 (Kamis), 9 (Jumat) = 4 hari kerja
    // Akhir pekan 10-11 Okt dilewati
    // Minggu 2: 12 (Senin), 13 (Selasa), 14 (Rabu), 15 (Kamis), 16 (Jumat) = 5 hari kerja
    // Akhir pekan 17-18 Okt dilewati
    // Minggu 3: 19 (Senin) = hari kerja ke-10
    // Maka jatuh tempo tepat pada Senin 19 Okt 2026
    expect($deadline->toDateString())->toBe('2026-10-19')
        ->and($deadline->isWeekday())->toBeTrue();
});

test('permohonan yang diajukan hari jumat melompati akhir pekan', function () {
    // Mulai: Jumat 2026-10-02
    $startDate = Carbon::create(2026, 10, 2, 10, 0, 0);
    $deadline = PermohonanInformasi::calculateDeadline($startDate, 10);

    // 10 hari kerja:
    // 3, 4 dilewati (Sabtu, Minggu)
    // 5-9 Okt = 5 hari
    // 10, 11 dilewati (Sabtu, Minggu)
    // 12-16 Okt = 5 hari
    // Total 10 hari kerja -> berakhir 16 Okt 2026
    expect($deadline->toDateString())->toBe('2026-10-16');
});

test('status tenggat menampilkan aman, mendekati, terlewat, dan selesai dengan badge yang sesuai', function () {
    // 1. Status Aman (> 3 hari kerja)
    $permohonanAman = PermohonanInformasi::create([
        'nama_pemohon' => 'Warga Aman',
        'email' => 'aman@example.com',
        'telepon' => '0812345',
        'alamat' => 'Alamat',
        'rincian_informasi' => 'Rincian',
        'tujuan_penggunaan' => 'Tujuan',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
        'tenggat_jawab' => PermohonanInformasi::calculateDeadline(now(), 10),
    ]);
    expect($permohonanAman->tenggat_status)->toBe('aman')
        ->and($permohonanAman->tenggat_badge_color)->toBe('info')
        ->and($permohonanAman->tenggat_badge_label)->toContain('Aman');

    // 2. Status Mendekati (<= 3 hari kerja)
    $permohonanMendekati = PermohonanInformasi::create([
        'nama_pemohon' => 'Warga Mendekati',
        'email' => 'mendekati@example.com',
        'telepon' => '0812345',
        'alamat' => 'Alamat',
        'rincian_informasi' => 'Rincian',
        'tujuan_penggunaan' => 'Tujuan',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
        'tenggat_jawab' => PermohonanInformasi::calculateDeadline(now(), 2),
    ]);
    expect($permohonanMendekati->tenggat_status)->toBe('mendekati')
        ->and($permohonanMendekati->tenggat_badge_color)->toBe('warning')
        ->and($permohonanMendekati->tenggat_badge_label)->toContain('Mendekati');

    // 3. Status Terlewat (tenggat di masa lalu)
    $permohonanTerlewat = PermohonanInformasi::create([
        'nama_pemohon' => 'Warga Terlewat',
        'email' => 'terlewat@example.com',
        'telepon' => '0812345',
        'alamat' => 'Alamat',
        'rincian_informasi' => 'Rincian',
        'tujuan_penggunaan' => 'Tujuan',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
        'tenggat_jawab' => now()->subDays(3),
    ]);
    expect($permohonanTerlewat->tenggat_status)->toBe('terlewat')
        ->and($permohonanTerlewat->tenggat_badge_color)->toBe('danger')
        ->and($permohonanTerlewat->tenggat_badge_label)->toContain('Terlewat');

    // 4. Status Selesai (bila sudah dijawab)
    $statusDijawab = StatusPermohonan::where('slug', 'dijawab')->first();
    $permohonanTerlewat->update(['status_id' => $statusDijawab->id]);
    $permohonanTerlewat->refresh();
    expect($permohonanTerlewat->tenggat_status)->toBe('selesai')
        ->and($permohonanTerlewat->tenggat_badge_color)->toBe('success')
        ->and($permohonanTerlewat->tenggat_badge_label)->toBe('Selesai');
});

/*
|--------------------------------------------------------------------------
| 3. UJI PENYIMPANAN PRIVAT & AKSES FILE IDENTITAS
|--------------------------------------------------------------------------
*/

test('berkas identitas diunggah ke private storage dan tidak dapat diakses publik', function () {
    Mail::fake();

    $file = UploadedFile::fake()->create('ktp_pemohon.pdf', 200, 'application/pdf');

    $response = $this->post(route('ppid.kirim-permohonan'), [
        'nama_pemohon' => 'Dewi Sartika',
        'email' => 'dewi@example.com',
        'telepon' => '081399887766',
        'alamat' => 'Jl. Kartini No. 45',
        'pekerjaan' => 'Dosen',
        'rincian_informasi' => 'Data realisasi dana desa tahun 2025',
        'tujuan_penggunaan' => 'Penelitian akademis',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
        'identitas_file' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('ppid.permohonan'));

    $permohonan = PermohonanInformasi::where('email', 'dewi@example.com')->first();
    expect($permohonan)->not()->toBeNull()
        ->and($permohonan->identitas_file_path)->not()->toBeNull();

    // Pastikan tersimpan di disk private, BUKAN disk public
    Storage::disk('private')->assertExists($permohonan->identitas_file_path);
    Storage::disk('public')->assertMissing($permohonan->identitas_file_path);

    // Mencoba akses langsung ke public storage URL ditolak (tidak 200 OK)
    $publicPath = '/storage/' . $permohonan->identitas_file_path;
    $publicResponse = $this->get($publicPath);
    expect(in_array($publicResponse->status(), [403, 404]))->toBeTrue();
});

test('tamu dan admin konten dilarang mengakses berkas identitas privat', function () {
    $file = UploadedFile::fake()->create('ktp_rahasia.jpg', 150, 'image/jpeg');
    $ppidService = app(PpidService::class);
    $permohonan = $ppidService->submitPermohonan([
        'nama_pemohon' => 'Rahasia Warga',
        'email' => 'warga@example.com',
        'telepon' => '0812345678',
        'alamat' => 'Alamat Rahasia',
        'rincian_informasi' => 'Informasi penting',
        'tujuan_penggunaan' => 'Tujuan penting',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ], $file);

    // 1. Guest dilarang (redirect ke login admin)
    $this->get(route('ppid.download-identitas', $permohonan->id))
        ->assertRedirect('/admin/login');

    // 2. Admin Konten (tanpa role Petugas PPID atau Super Admin) ditolak 403
    $adminKonten = User::factory()->create();
    $adminKonten->assignRole('Admin Konten');

    $this->actingAs($adminKonten)
        ->get(route('ppid.download-identitas', $permohonan->id))
        ->assertStatus(403);
});

test('super admin dan petugas PPID dapat mengunduh berkas identitas privat', function () {
    $file = UploadedFile::fake()->create('ktp_resmi.pdf', 300, 'application/pdf');
    $ppidService = app(PpidService::class);
    $permohonan = $ppidService->submitPermohonan([
        'nama_pemohon' => 'Warga Sah',
        'email' => 'sah@example.com',
        'telepon' => '0812345678',
        'alamat' => 'Alamat Sah',
        'rincian_informasi' => 'Data profil ppid',
        'tujuan_penggunaan' => 'Kajian',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ], $file);

    // 1. Petugas PPID dapat mengakses
    $petugasPpid = User::factory()->create();
    $petugasPpid->assignRole('Petugas PPID');

    $responsePetugas = $this->actingAs($petugasPpid)
        ->get(route('ppid.download-identitas', $permohonan->id));

    $responsePetugas->assertStatus(200);

    // 2. Super Admin dapat mengakses
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $responseSuper = $this->actingAs($superAdmin)
        ->get(route('ppid.download-identitas', $permohonan->id));

    $responseSuper->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| 4. UJI PERLINDUNGAN DATA PRIBADI (NIK ENKRIPSI)
|--------------------------------------------------------------------------
*/

test('nik pemohon disimpan dalam bentuk terenkripsi di database dan terdekripsi di model', function () {
    $nikAsli = '6402011205900001';

    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Indra Wijaya',
        'nik' => $nikAsli,
        'email' => 'indra@example.com',
        'telepon' => '08123456789',
        'alamat' => 'Jl. Melati No. 10',
        'rincian_informasi' => 'Permintaan data fasilitas puskesmas',
        'tujuan_penggunaan' => 'Evaluasi layanan publik',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    // Ambil raw nilai dari database
    $rawRecord = DB::table('permohonan_informasi')->where('id', $permohonan->id)->first();

    // Di level database mentah, nilainya TIDAK BOLEH sama dengan NIK asli (harus terenkripsi)
    expect($rawRecord->nik)->not()->toBe($nikAsli)
        ->and(strlen($rawRecord->nik))->toBeGreaterThan(40);

    // Di level Eloquent Model, cast encrypted secara transparan mengembalikan nilai asli
    $loadedPermohonan = PermohonanInformasi::find($permohonan->id);
    expect($loadedPermohonan->nik)->toBe($nikAsli);
});

/*
|--------------------------------------------------------------------------
| 5. UJI PENGIRIMAN EMAIL KONFIRMASI DAN ACTIVITY LOG
|--------------------------------------------------------------------------
*/

test('pengiriman formulir permohonan memicu email konfirmasi dan mencatat activity log', function () {
    Mail::fake();

    $response = $this->post(route('ppid.kirim-permohonan'), [
        'nama_pemohon' => 'Siti Aminah',
        'email' => 'siti@example.com',
        'telepon' => '081122334455',
        'alamat' => 'Jl. Flamboyan No. 2',
        'pekerjaan' => 'PNS',
        'rincian_informasi' => 'Laporan Akuntabilitas Kinerja 2025',
        'tujuan_penggunaan' => 'Bahan seminar nasional',
        'cara_memperoleh_informasi' => 'soft_copy',
        'cara_mendapat_salinan' => 'email',
    ]);

    $response->assertSessionHasNoErrors();
    $permohonan = PermohonanInformasi::where('email', 'siti@example.com')->first();
    expect($permohonan)->not()->toBeNull();

    // Verifikasi email konfirmasi dikirim ke pemohon
    Mail::assertSent(PermohonanKonfirmasiMail::class, function ($mail) use ($permohonan) {
        return $mail->hasTo('siti@example.com') && $mail->permohonan->id === $permohonan->id;
    });

    // Verifikasi Spatie Activity Log mencatat kejadian
    $activity = Activity::where('log_name', 'ppid')
        ->where('subject_id', $permohonan->id)
        ->first();
    expect($activity)->not()->toBeNull();
});

/*
|--------------------------------------------------------------------------
| 6. UJI PAKET SILVER RESTRIKSI PELACAKAN PUBLIK
|--------------------------------------------------------------------------
*/

test('pada paket silver fitur pelacakan publik dinonaktifkan dengan 403', function () {
    // Pastikan paket silver aktif
    config(['packages.default' => 'silver']);

    expect(Package::has('ppid_tracking'))->toBeFalse();

    $response = $this->get(route('ppid.lacak'));
    $response->assertStatus(403);

    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

/*
|--------------------------------------------------------------------------
| 7. UJI HALAMAN PUBLIK DAFTAR INFORMASI PUBLIK & HALAMAN STATIS PPID
|--------------------------------------------------------------------------
*/

test('halaman publik daftar informasi publik menampilkan 4 klasifikasi resmi UU KIP', function () {
    $response = $this->get(route('ppid.daftar-informasi'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Informasi Publik (DIP)');
    $response->assertSee('Informasi Berkala');
    $response->assertSee('Informasi Serta Merta');
    $response->assertSee('Informasi Setiap Saat');
    $response->assertSee('Informasi Dikecualikan');
});

test('halaman profil pejabat, laporan keuangan, dan rencana strategis dapat diakses dan dikelola admin', function () {
    // Halaman Profil Pejabat
    $respPejabat = $this->get(route('halaman.show', 'profil-pejabat'));
    $respPejabat->assertStatus(200);
    $respPejabat->assertSee('Profil Pejabat Pengelola PPID');

    // Halaman Laporan Keuangan
    $respKeuangan = $this->get(route('halaman.show', 'laporan-keuangan'));
    $respKeuangan->assertStatus(200);
    $respKeuangan->assertSee('Laporan Keuangan');

    // Halaman Rencana Strategis
    $respRenstra = $this->get(route('halaman.show', 'rencana-strategis'));
    $respRenstra->assertStatus(200);
    $respRenstra->assertSee('Rencana Strategis');

    // Pastikan ketiga halaman tercatat pada tabel halaman dan dapat diedit
    expect(Halaman::where('slug', 'profil-pejabat')->exists())->toBeTrue()
        ->and(Halaman::where('slug', 'laporan-keuangan')->exists())->toBeTrue()
        ->and(Halaman::where('slug', 'rencana-strategis')->exists())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| 8. UJI PEMROSESAN JAWABAN OLEH PETUGAS PPID
|--------------------------------------------------------------------------
*/

test('petugas PPID dapat memproses jawaban dan mengirim email tanggapan resmi', function () {
    Mail::fake();

    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Rahmat Hidayat',
        'email' => 'rahmat@example.com',
        'telepon' => '0812999888',
        'alamat' => 'Jl. Mahakam',
        'rincian_informasi' => 'Data program bantuan sosial',
        'tujuan_penggunaan' => 'Monitoring',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $jawabanFile = UploadedFile::fake()->create('jawaban_resmi_ppid.pdf', 500, 'application/pdf');
    $statusDijawab = StatusPermohonan::where('slug', 'dijawab')->first();

    $ppidService = app(PpidService::class);
    $ppidService->prosesJawaban($permohonan, [
        'status_id' => $statusDijawab->id,
        'catatan_internal' => 'Dokumen telah diverifikasi dan siap diserahkan.',
        'jawaban_teks' => 'Permohonan Anda telah kami tindak lanjuti, berkas terlampir.',
    ], $jawabanFile);

    $permohonan->refresh();

    expect($permohonan->status_id)->toBe($statusDijawab->id)
        ->and($permohonan->jawaban_file_path)->not()->toBeNull()
        ->and($permohonan->tanggal_dijawab)->not()->toBeNull();

    // Verifikasi berkas jawaban tersimpan di private storage
    Storage::disk('private')->assertExists($permohonan->jawaban_file_path);

    // Verifikasi email jawaban dikirimkan
    Mail::assertSent(PermohonanJawabanMail::class, function ($mail) use ($permohonan) {
        return $mail->hasTo('rahmat@example.com') && $mail->permohonan->id === $permohonan->id;
    });
});
