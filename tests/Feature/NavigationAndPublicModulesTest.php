<?php

use App\Models\Halaman;
use App\Models\Layanan;
use App\Models\NavigationMenu;
use App\Models\PesanKontak;
use App\Models\SiteSetting;
use App\Models\Statistik;
use App\Models\User;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\LayananSeeder;
use Database\Seeders\NavigationMenuSeeder;
use Database\Seeders\StatistikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(HalamanSeeder::class);
    $this->seed(NavigationMenuSeeder::class);
    $this->seed(LayananSeeder::class);
    $this->seed(StatistikSeeder::class);
});

test('seluruh 8 menu utama dapat dibuka tanpa error dengan status 200', function () {
    $mainMenus = [
        '/' => 'Beranda',
        '/profil' => 'Profil',
        '/pemerintahan' => 'Pemerintahan',
        '/layanan' => 'Layanan Publik',
        '/ppid/daftar-informasi' => 'Informasi Publik / PPID',
        '/data-statistik' => 'Data & Statistik',
        '/berita' => 'Publikasi',
        '/kontak' => 'Kontak',
    ];

    foreach ($mainMenus as $url => $label) {
        $response = $this->get($url);
        $response->assertStatus(200);
    }
});

test('seluruh submenu awal pada 4 menu induk dapat dibuka dengan status 200', function () {
    $submenus = [
        // Profil
        '/halaman/sejarah',
        '/halaman/visi-misi',
        '/halaman/struktur-organisasi',
        '/halaman/tugas-fungsi',
        // Pemerintahan
        '/halaman/profil-pejabat',
        '/halaman/unit-kerja',
        '/halaman/dasar-hukum',
        '/halaman/rencana-strategis',
        // Informasi Publik / PPID
        '/ppid/daftar-informasi',
        '/halaman/laporan-keuangan',
        '/ppid/permohonan',
        '/halaman/profil-ppid',
        // Publikasi
        '/berita',
        '/berita?kategori=Pengumuman',
        '/galeri',
        '/publikasi',
    ];

    foreach ($submenus as $url) {
        $response = $this->get($url);
        $response->assertStatus(200);
    }
});

test('menu yang disembunyikan admin tidak muncul pada situs publik', function () {
    // Sembunyikan menu Data & Statistik
    $statistikMenu = NavigationMenu::where('nama', 'Data & Statistik')->first();
    expect($statistikMenu)->not()->toBeNull();
    $statistikMenu->update(['is_active' => false]);

    // Buka halaman beranda
    $response = $this->get('/');
    $response->assertStatus(200);

    // Di navigasi desktop/mobile, menu yang disembunyikan tidak boleh muncul di shared navigationMenus
    $activeNavMenus = $response->viewData('navigationMenus');
    expect($activeNavMenus)->not()->toBeNull()
        ->and($activeNavMenus->pluck('nama')->contains('Data & Statistik'))->toBeFalse();

    // Sembunyikan submenu Sejarah di Profil
    $sejarahSub = NavigationMenu::where('nama', 'Sejarah')->first();
    expect($sejarahSub)->not()->toBeNull();
    $sejarahSub->update(['is_active' => false]);

    $responseProfil = $this->get('/profil');
    $responseProfil->assertStatus(200);

    $profilMenu = $responseProfil->viewData('navigationMenus')->firstWhere('nama', 'Profil');
    expect($profilMenu)->not()->toBeNull()
        ->and($profilMenu->children->pluck('nama')->contains('Sejarah'))->toBeFalse();
});

test('submenu mengikuti urutan yang ditentukan oleh admin', function () {
    $profilMenu = NavigationMenu::where('nama', 'Profil')->first();
    expect($profilMenu)->not()->toBeNull();

    // Urutan default: Sejarah (1), Visi dan Misi (2), Struktur Organisasi (3), Tugas dan Fungsi (4)
    $children = $profilMenu->children()->where('is_active', true)->orderBy('urutan')->get();
    expect($children->pluck('nama')->toArray())->toBe([
        'Sejarah',
        'Visi dan Misi',
        'Struktur Organisasi',
        'Tugas dan Fungsi',
    ]);

    // Ubah urutan: jadikan Tugas dan Fungsi urutan 0
    $tugasFungsi = NavigationMenu::where('nama', 'Tugas dan Fungsi')->first();
    $tugasFungsi->update(['urutan' => 0]);

    $response = $this->get('/');
    $activeProfil = $response->viewData('navigationMenus')->firstWhere('nama', 'Profil');
    $orderedChildren = $activeProfil->children->pluck('nama')->toArray();

    expect($orderedChildren[0])->toBe('Tugas dan Fungsi');
});

test('jenis konten layanan menampilkan daftar dan detail dengan lengkap', function () {
    // Daftar layanan
    $responseList = $this->get('/layanan');
    $responseList->assertStatus(200);
    $responseList->assertSee('Pusat Pelayanan Administrasi Terpadu');
    $responseList->assertSee('Rekomendasi Surat Keterangan Tidak Mampu (SKTM)');

    // Detail layanan
    $layanan = Layanan::where('slug', 'rekomendasi-sktm')->first();
    expect($layanan)->not()->toBeNull();

    $responseDetail = $this->get('/layanan/' . $layanan->slug);
    $responseDetail->assertStatus(200);
    $responseDetail->assertSee($layanan->nama);
    $responseDetail->assertSee($layanan->biaya);
    $responseDetail->assertSee($layanan->waktu_penyelesaian);
    $responseDetail->assertSee($layanan->penanggung_jawab);
    $responseDetail->assertSee('Persyaratan Berkas', false);
    $responseDetail->assertSee('Alur &amp; Prosedur', false);
});

test('jenis konten statistik menampilkan daftar dengan filter kategori serta tahun dan halaman detail', function () {
    // Daftar statistik
    $responseList = $this->get('/data-statistik');
    $responseList->assertStatus(200);
    $responseList->assertSee('Katalog Data & Statistik');
    $responseList->assertSee('Kependudukan');

    // Filter kategori
    $responseKat = $this->get('/data-statistik?kategori=Kependudukan');
    $responseKat->assertStatus(200);
    $responseKat->assertSee('Data Kependudukan Menurut Jenis Kelamin');

    // Filter tahun
    $responseTahun = $this->get('/data-statistik?tahun=' . date('Y'));
    $responseTahun->assertStatus(200);

    // Detail statistik
    $stat = Statistik::first();
    expect($stat)->not()->toBeNull();

    $responseDetail = $this->get('/data-statistik/' . $stat->slug);
    $responseDetail->assertStatus(200);
    $responseDetail->assertSee($stat->judul);
    $responseDetail->assertSee($stat->kategori);
    $responseDetail->assertSee($stat->periode);
    $responseDetail->assertSee('Tabel Rincian Data Statistik');

    // Pastikan tidak ada script interactive charting (Chart.js / Highcharts / ApexCharts)
    $responseDetail->assertDontSee('chart.js');
    $responseDetail->assertDontSee('new Chart(');
    $responseDetail->assertDontSee('apexcharts');
});

test('halaman kontak menampilkan site_settings dan menyimpan pesan dengan proteksi honeypot', function () {
    $settings = SiteSetting::current();
    $settings->update([
        'address' => 'Jl. Pahlawan Reformasi No. 45',
        'phone' => '0541-662999',
        'service_hours' => "Senin - Kamis: 08.00 - 15.30 WITA\nJumat: 08.00 - 11.30 WITA",
    ]);

    $responseKontak = $this->get('/kontak');
    $responseKontak->assertStatus(200);
    $responseKontak->assertSee('Jl. Pahlawan Reformasi No. 45');
    $responseKontak->assertSee('0541-662999');
    $responseKontak->assertSee('_hp_security_check'); // Honeypot field hadir

    // Kirim pesan valid oleh pengguna manusia (_hp_security_check kosong)
    $payload = [
        'nama' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'telepon' => '08123456789',
        'topik' => 'pelayanan_ppid',
        'pesan' => 'Mohon informasi jadwal pelayanan meja PPID.',
        '_hp_security_check' => '',
    ];

    $responseSubmit = $this->post('/kontak', $payload);
    $responseSubmit->assertRedirect('/kontak');
    $responseSubmit->assertSessionHas('success');

    $pesan = PesanKontak::where('email', 'budi@example.com')->first();
    expect($pesan)->not()->toBeNull()
        ->and($pesan->status)->toBe('belum_dibaca')
        ->and($pesan->nama)->toBe('Budi Santoso');

    // Kirim pesan oleh bot yang mengisi honeypot
    $botPayload = [
        'nama' => 'Spam Bot',
        'email' => 'bot@spammer.com',
        'telepon' => '123456',
        'topik' => 'lainnya',
        'pesan' => 'Buy cheap medications now http://spam.example',
        '_hp_security_check' => 'I am a bot filling hidden fields',
    ];

    $responseBot = $this->post('/kontak', $botPayload);
    $responseBot->assertRedirect('/kontak');

    $botMessage = PesanKontak::where('email', 'bot@spammer.com')->first();
    expect($botMessage)->toBeNull(); // Bot ditolak dan tidak disimpan ke database
});
