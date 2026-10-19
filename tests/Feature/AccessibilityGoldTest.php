<?php

use App\Models\Banner;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Package;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(HalamanSeeder::class);
    Storage::fake('public');
    config(['packages.default' => 'gold']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

test('widget aksesibilitas muncul pada layout publik saat paket Gold aktif', function () {
    expect(Package::has('accessibility'))->toBeTrue();

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('accessibility-widget', false);
    $response->assertSee('Alat Aksesibilitas Publik');
    $response->assertSee('Ukuran Teks (Huruf)');
    $response->assertSee('Kontras Tinggi');
    $response->assertSee('Pembaca Suara (TTS)');
});

test('widget aksesibilitas tidak muncul pada layout publik jika paket Silver diaktifkan', function () {
    config(['packages.default' => 'silver']);

    expect(Package::has('accessibility'))->toBeFalse();

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertDontSee('accessibility-widget', false);
    $response->assertDontSee('Alat Aksesibilitas Publik');
});

test('skip links semantik untuk navigasi keyboard ramah disabilitas tersedia pada layout publik', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Lompat ke Konten Utama');
    $response->assertSee('#main-content', false);
    $response->assertSee('Lompat ke Navigasi Menu');
    $response->assertSee('#main-navigation', false);
    $response->assertSee('Lompat ke Alat Aksesibilitas');
    $response->assertSee('#accessibility-widget-toggle', false);
});

test('halaman detail berita memuat modul Text-to-Speech pada paket Gold', function () {
    $admin = User::factory()->create();

    $berita = Berita::create([
        'judul' => 'Peningkatan Pelayanan Publik Disabilitas Kabupaten',
        'slug' => 'peningkatan-pelayanan-disabilitas',
        'ringkasan' => 'Dinas sosial meluncurkan fasilitas ramah disabilitas.',
        'isi' => 'Pemerintah daerah berkomitmen penuh menghadirkan kesetaraan akses layanan publik.',
        'kategori' => 'Sosial',
        'status' => 'terbit',
        'author_id' => $admin->id,
        'alt_text' => 'Foto peresmian loket layanan disabilitas terpadu',
    ]);

    $response = $this->get(route('berita.detail', $berita->slug));

    $response->assertStatus(200);
    $response->assertSee('Dengarkan Berita Ini (Audio TTS)');
    $response->assertSee('Membaca teks dalam bahasa Indonesia ramah disabilitas netra');
});

test('halaman detail berita tidak memuat modul Text-to-Speech pada paket Silver', function () {
    config(['packages.default' => 'silver']);

    $admin = User::factory()->create();

    $berita = Berita::create([
        'judul' => 'Peningkatan Pelayanan Publik Silver',
        'slug' => 'peningkatan-pelayanan-silver',
        'ringkasan' => 'Ringkasan berita silver.',
        'isi' => 'Konten lengkap berita silver.',
        'kategori' => 'Sosial',
        'status' => 'terbit',
        'author_id' => $admin->id,
    ]);

    $response = $this->get(route('berita.detail', $berita->slug));

    $response->assertStatus(200);
    $response->assertDontSee('Dengarkan Berita Ini (Audio TTS)');
});

test('kolom alt_text tersimpan pada model Berita, Banner, dan Galeri untuk standar WCAG 2.1 AA', function () {
    $berita = Berita::create([
        'judul' => 'Pelatihan Digital UMKM',
        'slug' => 'pelatihan-digital-umkm',
        'isi' => 'Konten pelatihan...',
        'alt_text' => 'Suasana pelatihan peserta UMKM memegang gawai',
    ]);
    expect($berita->alt_text)->toBe('Suasana pelatihan peserta UMKM memegang gawai');

    $banner = Banner::create([
        'judul' => 'Banner Pelayanan KTP Online',
        'alt_text' => 'Infografis alur pembuatan KTP digital instansi',
    ]);
    expect($banner->alt_text)->toBe('Infografis alur pembuatan KTP digital instansi');

    $galeri = Galeri::create([
        'judul' => 'Kunjungan Kerja Bupati',
        'slug' => 'kunjungan-kerja-bupati',
        'gambar' => 'galeri/bupati.webp',
        'alt_text' => 'Bupati menyapa warga di balai pertemuan kecamatan',
        'tanggal' => now(),
    ]);
    expect($galeri->alt_text)->toBe('Bupati menyapa warga di balai pertemuan kecamatan');
});

test('layout publik menyinkronkan identitas visual dari site_settings dinamis', function () {
    SiteSetting::query()->delete();
    $setting = SiteSetting::create([
        'institution_name' => 'Badan Pendapatan Daerah Kabupaten Kutai Kartanegara',
        'institution_acronym' => 'Bapenda Kukar',
        'primary_color' => '#0c2340',
        'secondary_color' => '#f2a900',
        'address' => 'Jl. Wolter Monginsidi No. 1, Tenggarong',
        'email' => 'bapenda@kukarkab.go.id',
    ]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Badan Pendapatan Daerah Kabupaten Kutai Kartanegara');
    $response->assertSee('#0c2340', false);
    $response->assertSee('#f2a900', false);
});
