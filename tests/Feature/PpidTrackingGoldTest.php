<?php

use App\Mail\PermohonanJawabanMail;
use App\Mail\StatusPermohonanUpdatedMail;
use App\Models\PengaturanPpid;
use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use App\Models\User;
use App\Services\PpidService;
use App\Support\Package;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\PengaturanPpidSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusPermohonanSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(KategoriInformasiSeeder::class);
    $this->seed(StatusPermohonanSeeder::class);
    $this->seed(PengaturanPpidSeeder::class);
    $this->seed(HalamanSeeder::class);
    Storage::fake('private');
    Storage::fake('public');
    config(['packages.default' => 'gold']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

test('halaman lacak terbuka sukses (HTTP 200) pada paket Gold', function () {
    expect(Package::has('ppid_tracking'))->toBeTrue();

    $response = $this->get(route('ppid.lacak'));

    $response->assertStatus(200);
    $response->assertSee('Pelacakan Status Permohonan Informasi');
    $response->assertSee('Nomor Registrasi Permohonan');
    $response->assertSee('Alamat Email Pemohon');
});

test('halaman lacak terblokir dengan HTTP 403 pada paket Silver', function () {
    config(['packages.default' => 'silver']);

    expect(Package::has('ppid_tracking'))->toBeFalse();

    $response = $this->get(route('ppid.lacak'));

    $response->assertStatus(403);
});

test('pencarian resi membutuhkan nomor registrasi dan email yang valid', function () {
    $response = $this->post(route('ppid.lacak'), [
        'nomor_registrasi' => '',
        'email' => '',
    ]);

    $response->assertSessionHasErrors(['nomor_registrasi', 'email']);
});

test('pencarian resi dengan kredensial salah menampilkan pesan tidak ditemukan', function () {
    $response = $this->post(route('ppid.lacak'), [
        'nomor_registrasi' => 'PPID/KEC-LK/2026/9999',
        'email' => 'tidakada@example.com',
    ]);

    $response->assertStatus(200);
    $response->assertSee('Data Permohonan Tidak Ditemukan');
});

test('pencarian dengan nomor registrasi dan email valid menampilkan detail, status, dan sisa tenggat', function () {
    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Kurnia Meiga',
        'email' => 'kurnia@example.com',
        'telepon' => '081234567890',
        'alamat' => 'Jl. Pahlawan No. 45',
        'rincian_informasi' => 'Data anggaran penanganan banjir 2026',
        'tujuan_penggunaan' => 'Riset publik',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $response = $this->post(route('ppid.lacak'), [
        'nomor_registrasi' => $permohonan->nomor_registrasi,
        'email' => 'kurnia@example.com',
    ]);

    $response->assertStatus(200);
    $response->assertSee($permohonan->nomor_registrasi);
    $response->assertSee('Data anggaran penanganan banjir 2026');
    $response->assertSee('Batas Waktu Respons');
    $response->assertSee('Linimasa Tindak Lanjut Permohonan');
});

test('pencarian juga dapat diakses lewat parameter GET untuk tautan langsung dari email', function () {
    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'telepon' => '081987654321',
        'alamat' => 'Jl. Mawar No. 10',
        'rincian_informasi' => 'Profil dokumen RKPD tahun berjalan',
        'tujuan_penggunaan' => 'Kajian akademis',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $response = $this->get(route('ppid.lacak', [
        'nomor_registrasi' => $permohonan->nomor_registrasi,
        'email' => 'siti@example.com',
    ]));

    $response->assertStatus(200);
    $response->assertSee($permohonan->nomor_registrasi);
    $response->assertSee('Profil dokumen RKPD tahun berjalan');
});

test('unduhan berkas jawaban resmi lewat signed URL berjalan sukses dan menolak URL tanpa signature sah', function () {
    // Siapkan berkas dummy di storage private
    $fakePdf = UploadedFile::fake()->create('dokumen_jawaban_resmi.pdf', 500, 'application/pdf');
    $path = 'jawaban/test-jawaban-gold.pdf';
    Storage::disk('private')->put($path, file_get_contents($fakePdf->getRealPath()));

    $statusDijawab = StatusPermohonan::where('slug', 'dijawab')->first();

    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Bambang Pamungkas',
        'email' => 'bambang@example.com',
        'telepon' => '081299990000',
        'alamat' => 'Jl. Sudirman No. 20',
        'rincian_informasi' => 'Laporan realisasi PAD',
        'tujuan_penggunaan' => 'Analisis publik',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
        'status_id' => $statusDijawab->id,
        'jawaban_teks' => 'Permohonan disetujui, berkas terlampir.',
        'jawaban_file_path' => $path,
        'tanggal_dijawab' => now(),
    ]);

    // 1. Uji akses tanpa signature -> 403 Forbidden (Invalid Signature)
    $unsignedUrl = route('ppid.download-jawaban', ['permohonan' => $permohonan->id]);
    $resUnsigned = $this->get($unsignedUrl);
    $resUnsigned->assertStatus(403);

    // 2. Uji akses dengan signature sah (Signed URL) -> 200 OK
    $signedUrl = URL::temporarySignedRoute(
        'ppid.download-jawaban',
        now()->addHours(24),
        ['permohonan' => $permohonan->id]
    );

    $resSigned = $this->get($signedUrl);
    $resSigned->assertStatus(200);
    expect($resSigned->headers->get('content-disposition'))->toContain('attachment;');
});

test('perubahan status memicu pengiriman email pembaruan status ke pemohon', function () {
    Mail::fake();

    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Rahmat Hidayat',
        'email' => 'rahmat@example.com',
        'telepon' => '081333334444',
        'alamat' => 'Jl. Melati No. 5',
        'rincian_informasi' => 'Data statistik UMKM binaan',
        'tujuan_penggunaan' => 'Tugas akhir',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $statusDiverifikasi = StatusPermohonan::where('slug', 'diverifikasi')->first();

    $service = app(PpidService::class);
    $service->prosesJawaban($permohonan, [
        'status_id' => $statusDiverifikasi->id,
        'catatan_internal' => 'Berkas identitas dinyatakan lengkap dan valid.',
    ]);

    Mail::assertSent(StatusPermohonanUpdatedMail::class, function ($mail) use ($permohonan) {
        return $mail->hasTo('rahmat@example.com')
            && $mail->permohonan->id === $permohonan->id;
    });
});

test('perubahan status ke dijawab memicu pengiriman email tanggapan resmi ke pemohon', function () {
    Mail::fake();

    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Dewi Lestari',
        'email' => 'dewi@example.com',
        'telepon' => '081555556666',
        'alamat' => 'Jl. Kenanga No. 1',
        'rincian_informasi' => 'Informasi pengadaan barang dan jasa',
        'tujuan_penggunaan' => 'Publikasi media',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $statusDijawab = StatusPermohonan::where('slug', 'dijawab')->first();

    $service = app(PpidService::class);
    $service->prosesJawaban($permohonan, [
        'status_id' => $statusDijawab->id,
        'jawaban_teks' => 'Informasi terlampir dapat diakses secara publik.',
    ]);

    Mail::assertSent(PermohonanJawabanMail::class, function ($mail) use ($permohonan) {
        return $mail->hasTo('dewi@example.com')
            && $mail->permohonan->id === $permohonan->id;
    });
});

test('nomor registrasi valid dengan email yang salah ditolak dan tidak menampilkan data permohonan', function () {
    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Ahmad Dahlan',
        'email' => 'ahmad@example.com',
        'telepon' => '081233334444',
        'alamat' => 'Jl. Merdeka No. 12',
        'rincian_informasi' => 'Rencana Kerja Pemerintah Daerah',
        'tujuan_penggunaan' => 'Riset',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    // Request dengan nomor registrasi yang valid tetapi email salah
    $response = $this->post(route('ppid.lacak'), [
        'nomor_registrasi' => $permohonan->nomor_registrasi,
        'email' => 'emailsalah@example.com',
    ]);

    $response->assertStatus(200);
    $response->assertSee('Data Permohonan Tidak Ditemukan');
    $response->assertDontSee($permohonan->nama_pemohon);
    $response->assertDontSee('Rencana Kerja Pemerintah Daerah');
});

test('batas percobaan pelacakan per IP memicu rate limiting HTTP 429 setelah melebihi batas', function () {
    // Route ppid.lacak memiliki middleware throttle:30,1
    // Kirim 30 request pertama yang diizinkan
    for ($i = 0; $i < 30; $i++) {
        $this->get(route('ppid.lacak'));
    }

    // Request ke-31 harus menghasilkan HTTP 429 Too Many Requests
    $response = $this->get(route('ppid.lacak'));
    $response->assertStatus(429);
});

test('email konfirmasi pendaftaran memuat nomor registrasi dan tautan lacak mandiri', function () {
    $permohonan = PermohonanInformasi::create([
        'nama_pemohon' => 'Ratna Sari Dewi',
        'email' => 'ratna@example.com',
        'telepon' => '081777778888',
        'alamat' => 'Jl. Melati No. 8',
        'rincian_informasi' => 'Daftar Penerima Bansos Kabupaten',
        'tujuan_penggunaan' => 'Monitoring publik',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $mailable = new \App\Mail\PermohonanKonfirmasiMail($permohonan);
    $rendered = $mailable->render();

    expect($rendered)->toContain($permohonan->nomor_registrasi)
        ->and($rendered)->toContain(route('ppid.lacak'))
        ->and($rendered)->toContain('Lacak Status Permohonan Online');
});
