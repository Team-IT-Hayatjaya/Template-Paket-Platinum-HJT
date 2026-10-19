<?php

use App\Models\Berita;
use App\Models\DokumenInformasi;
use App\Models\PageView;
use App\Models\PermohonanInformasi;
use App\Models\SiteSetting;
use App\Models\StatusPermohonan;
use App\Models\User;
use App\Services\MonthlyReportService;
use App\Support\Package;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\PengaturanPpidSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusPermohonanSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(KategoriInformasiSeeder::class);
    $this->seed(StatusPermohonanSeeder::class);
    $this->seed(PengaturanPpidSeeder::class);
    $this->seed(HalamanSeeder::class);
});

/*
|--------------------------------------------------------------------------
| 1. UJI STATISTIK PENGUNJUNG TANPA PIHAK KETIGA (PAGE_VIEWS)
|--------------------------------------------------------------------------
*/

test('tabel page_views tidak menyimpan kolom alamat IP mentah demi privasi', function () {
    expect(Schema::hasTable('page_views'))->toBeTrue()
        ->and(Schema::hasColumn('page_views', 'halaman'))->toBeTrue()
        ->and(Schema::hasColumn('page_views', 'tanggal'))->toBeTrue()
        ->and(Schema::hasColumn('page_views', 'perangkat'))->toBeTrue()
        ->and(Schema::hasColumn('page_views', 'views'))->toBeTrue()
        ->and(Schema::hasColumn('page_views', 'ip_address'))->toBeFalse()
        ->and(Schema::hasColumn('page_views', 'ip'))->toBeFalse();
});

test('kunjungan ke halaman publik dicatat ke tabel page_views dengan deteksi perangkat yang tepat', function () {
    $mobileUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1';
    $desktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $tabletUa = 'Mozilla/5.0 (iPad; CPU OS 16_5 like Mac OS X) AppleWebKit/605.1.15 Version/16.5 Mobile/15E148 Safari/604.1';

    // 1. Kunjungan Mobile
    $this->withHeaders(['User-Agent' => $mobileUa])->get(route('home'))->assertStatus(200);

    $mobileView = PageView::where('halaman', '/')->where('perangkat', 'mobile')->first();
    expect($mobileView)->not()->toBeNull()
        ->and($mobileView->views)->toBeGreaterThanOrEqual(1);

    // 2. Kunjungan Desktop
    $this->withHeaders(['User-Agent' => $desktopUa])->get(route('profil'))->assertStatus(200);

    $desktopView = PageView::where('halaman', '/profil')->where('perangkat', 'desktop')->first();
    expect($desktopView)->not()->toBeNull()
        ->and($desktopView->views)->toBeGreaterThanOrEqual(1);

    // 3. Kunjungan Tablet
    $this->withHeaders(['User-Agent' => $tabletUa])->get(route('ppid.daftar-informasi'))->assertStatus(200);

    $tabletView = PageView::where('halaman', '/ppid/daftar-informasi')->where('perangkat', 'tablet')->first();
    expect($tabletView)->not()->toBeNull()
        ->and($tabletView->views)->toBeGreaterThanOrEqual(1);
});

test('rute panel admin dan aset diabaikan dari pencatatan statistik pengunjung', function () {
    $initialCount = PageView::count();

    // Rute admin
    $this->get('/admin');

    // Health check
    $this->get('/health');

    // Robots.txt
    $this->get('/robots.txt');

    $adminView = PageView::where('halaman', 'like', '%admin%')->first();
    expect($adminView)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| 2. UJI LAPORAN BULANAN & EKSPOR PDF
|--------------------------------------------------------------------------
*/

test('monthly report service mengumpulkan data statistik lengkap dan akurat', function () {
    $service = app(MonthlyReportService::class);
    $year = (int) date('Y');
    $month = (int) date('n');

    // Siapkan data dummy page views
    PageView::recordView('/berita', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    PageView::recordView('/layanan', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)');

    // Siapkan data dummy konten
    Berita::create([
        'judul' => 'Berita Pelaporan ' . uniqid(),
        'isi' => 'Konten laporan.',
        'status' => 'terbit',
        'tanggal_terbit' => now(),
    ]);

    // Siapkan data dummy permohonan
    PermohonanInformasi::create([
        'nama_pemohon' => 'Pemohon Uji Laporan',
        'email' => 'uji@example.com',
        'telepon' => '0812345',
        'alamat' => 'Alamat',
        'rincian_informasi' => 'Rincian',
        'tujuan_penggunaan' => 'Tujuan',
        'cara_memperoleh_informasi' => 'salinan',
        'cara_mendapat_salinan' => 'email',
    ]);

    $data = $service->getReportData($year, $month);

    expect($data)->toHaveKeys(['total_views', 'views_by_device', 'top_pages', 'content_updates', 'permohonan', 'instansi'])
        ->and($data['total_views'])->toBeGreaterThan(0)
        ->and($data['content_updates']['total'])->toBeGreaterThanOrEqual(1)
        ->and($data['permohonan']['total'])->toBeGreaterThanOrEqual(1)
        ->and(count($data['permohonan']['breakdown']))->toBeGreaterThan(0);
});

test('ekspor laporan bulanan menghasilkan berkas PDF yang valid', function () {
    $service = app(MonthlyReportService::class);
    $year = (int) date('Y');
    $month = (int) date('n');

    $response = $service->exportPdf($year, $month);

    expect($response->headers->get('content-type'))->toBe('application/pdf')
        ->and($response->headers->get('content-disposition'))->toContain('.pdf');
});

test('unduh laporan PDF di rute admin memerlukan autentikasi dan mengembalikan file pdf', function () {
    $year = date('Y');
    $month = date('n');

    // 1. Guest dilarang (redirect ke login)
    $this->get(route('admin.laporan-bulanan.pdf', ['year' => $year, 'month' => $month]))
        ->assertRedirect('/admin/login');

    // 2. Admin terautentikasi dapat mengunduh PDF
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)
        ->get(route('admin.laporan-bulanan.pdf', ['year' => $year, 'month' => $month]));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toBe('application/pdf');
});

/*
|--------------------------------------------------------------------------
| 3. UJI PENJADWALAN BACKUP MENGIKUTI PAKET AKTIF
|--------------------------------------------------------------------------
*/

test('jadwal backup mengikuti frekuensi paket aktif pada config packages', function () {
    // Pada paket silver, frekuensi backup adalah monthly
    config(['packages.default' => 'silver']);
    expect(Package::limit('backup_frequency'))->toBe('monthly');

    // Pada paket gold, frekuensi backup adalah weekly
    config(['packages.default' => 'gold']);
    expect(Package::limit('backup_frequency'))->toBe('weekly');

    // Pada paket platinum, frekuensi backup adalah daily
    config(['packages.default' => 'platinum']);
    expect(Package::limit('backup_frequency'))->toBe('daily');

    // Kembalikan ke paket bawaan
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

/*
|--------------------------------------------------------------------------
| 4. UJI PERINTAH ARTISAN SITE:INSTALL (1-COMMAND INSTALLATION)
|--------------------------------------------------------------------------
*/

test('perintah php artisan site:install dapat dijalankan dari nol dengan sukses', function () {
    $exitCode = Artisan::call('site:install', [
        '--package' => 'silver',
        '--institution' => 'Pemerintah Kabupaten Kutai Kartanegara',
        '--acronym' => 'KUKAR',
        '--domain' => 'https://kukar.go.id',
        '--admin-name' => 'Admin Utama Kukar',
        '--admin-email' => 'admin@kukar.go.id',
        '--admin-password' => 'SecurePass#2026',
        '--force' => true,
    ]);

    expect($exitCode)->toBe(0);

    // Verifikasi identitas instansi tersimpan
    $setting = SiteSetting::first();
    expect($setting)->not()->toBeNull()
        ->and($setting->institution_name)->toBe('Pemerintah Kabupaten Kutai Kartanegara')
        ->and($setting->institution_acronym)->toBe('KUKAR');

    // Verifikasi akun Super Admin dibuat dan memiliki role
    $admin = User::where('email', 'admin@kukar.go.id')->first();
    expect($admin)->not()->toBeNull()
        ->and($admin->hasRole('Super Admin'))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| 5. UJI ENDPOINT /HEALTH (DATABASE, STORAGE, QUEUE)
|--------------------------------------------------------------------------
*/

test('endpoint /health mengembalikan status 200 dan tampilan sehat', function () {
    $response = $this->get('/health');

    $response->assertStatus(200);
    $response->assertSee('Sistem Beroperasi Normal');
    $response->assertSee('Koneksi Database');
    $response->assertSee('Penyimpanan Berkas (Storage)');
    $response->assertSee('Layanan Antrean (Queue)');
});

test('endpoint /health dengan format json mengembalikan payload terstruktur', function () {
    $response = $this->get('/health?format=json');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'timestamp',
        'total_latency_ms',
        'checks' => [
            'database' => ['status', 'message', 'connection'],
            'storage' => ['status', 'message', 'disks'],
            'queue' => ['status', 'message', 'driver'],
        ],
        'package',
    ]);

    expect($response->json('status'))->toBe('healthy')
        ->and($response->json('checks.database.status'))->toBe('ok')
        ->and($response->json('checks.storage.status'))->toBe('ok')
        ->and($response->json('checks.queue.status'))->toBe('ok');
});
