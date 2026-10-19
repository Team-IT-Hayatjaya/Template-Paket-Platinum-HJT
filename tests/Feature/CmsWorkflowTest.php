<?php

use App\Models\Banner;
use App\Models\Berita;
use App\Models\DokumenInformasi;
use App\Models\Galeri;
use App\Models\Halaman;
use App\Models\KategoriInformasi;
use App\Models\User;
use App\Policies\BannerPolicy;
use App\Policies\BeritaPolicy;
use App\Policies\DokumenInformasiPolicy;
use App\Policies\GaleriPolicy;
use App\Policies\HalamanPolicy;
use App\Services\ContentQuotaService;
use App\Services\ImageConversionService;
use App\Support\Package;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

/*
|--------------------------------------------------------------------------
| 1. UJI STATUS TERJADWAL BERITA
|--------------------------------------------------------------------------
*/

test('berita terbit masa lalu atau sekarang masuk dalam scope published', function () {
    $terbit = Berita::create([
        'judul' => 'Berita Terbit Saat Ini',
        'isi' => 'Konten berita yang sudah terbit.',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subHour(),
    ]);

    $draft = Berita::create([
        'judul' => 'Berita Masih Draft',
        'isi' => 'Konten draft.',
        'status' => 'draft',
        'tanggal_terbit' => now()->subHour(),
    ]);

    $publishedSlugs = Berita::published()->pluck('slug')->toArray();

    expect($publishedSlugs)->toContain($terbit->slug)
        ->and($publishedSlugs)->not()->toContain($draft->slug);
});

test('berita terjadwal di masa depan tidak muncul dalam scope published', function () {
    $beritaTerjadwalMasaDepan = Berita::create([
        'judul' => 'Rilis Program Baru Pekan Depan',
        'isi' => 'Konten belum waktunya rilis publik.',
        'status' => 'terjadwal',
        'tanggal_terbit' => now()->addDays(3),
    ]);

    $beritaTerjadwalSudahTiba = Berita::create([
        'judul' => 'Rilis Program yang Waktunya Sudah Tiba',
        'isi' => 'Konten sudah waktunya tayang publik.',
        'status' => 'terjadwal',
        'tanggal_terbit' => now()->subMinutes(10),
    ]);

    $published = Berita::published()->pluck('slug')->toArray();

    expect($published)->not()->toContain($beritaTerjadwalMasaDepan->slug)
        ->and($published)->toContain($beritaTerjadwalSudahTiba->slug);
});

test('halaman publik berita menyembunyikan berita terjadwal masa depan dan 404 pada detail', function () {
    $beritaMasaDepan = Berita::create([
        'judul' => 'Berita Rahasia Masa Depan',
        'isi' => 'Isi berita masa depan.',
        'status' => 'terjadwal',
        'tanggal_terbit' => now()->addDays(5),
    ]);

    // Berita index tidak memuat berita terjadwal masa depan
    $response = $this->get(route('berita.index'));
    $response->assertStatus(200);
    $response->assertDontSee($beritaMasaDepan->judul);

    // Detail berita terjadwal masa depan mengembalikan 404
    $responseDetail = $this->get(route('berita.detail', $beritaMasaDepan->slug));
    $responseDetail->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| 2. UJI KONVERSI GAMBAR KE .WEBP
|--------------------------------------------------------------------------
*/

test('ImageConversionService mengonversi file gambar ke format .webp dengan benar', function () {
    $service = new ImageConversionService();

    // Buat gambar PNG asli sementara
    $tempPng = tempnam(sys_get_temp_dir(), 'img_') . '.png';
    $img = imagecreatetruecolor(100, 100);
    $color = imagecolorallocate($img, 30, 64, 175);
    imagefill($img, 0, 0, $color);
    imagepng($img, $tempPng);
    imagedestroy($img);

    expect(file_exists($tempPng))->toBeTrue();

    // Konversi ke WebP
    $destWebp = tempnam(sys_get_temp_dir(), 'img_') . '.webp';
    $result = $service->convertToWebp($tempPng, $destWebp);

    expect($result)->toBe($destWebp)
        ->and(file_exists($destWebp))->toBeTrue()
        ->and(filesize($destWebp))->toBeGreaterThan(0);

    // Verifikasi header berkas WebP (RIFF .... WEBP)
    $header = file_get_contents($destWebp, false, null, 0, 12);
    expect(str_starts_with($header, 'RIFF'))->toBeTrue()
        ->and(str_contains($header, 'WEBP'))->toBeTrue();

    @unlink($tempPng);
    @unlink($destWebp);
});

test('ImageConversionService storeAsWebp menyimpan UploadedFile langsung sebagai .webp di Storage', function () {
    Storage::fake('public');

    $service = new ImageConversionService();
    $fakeImage = UploadedFile::fake()->image('dokumentasi.png', 200, 150);

    $savedPath = $service->storeAsWebp($fakeImage, 'berita', 'public');

    expect(str_ends_with($savedPath, '.webp'))->toBeTrue();
    Storage::disk('public')->assertExists($savedPath);

    // Cek binary header webp di storage
    $content = Storage::disk('public')->get($savedPath);
    expect(str_starts_with($content, 'RIFF'))->toBeTrue()
        ->and(str_contains($content, 'WEBP'))->toBeTrue();
});

test('model Berita dan Galeri otomatis mengonversi gambar di Storage menjadi .webp saat disimpan', function () {
    Storage::fake('public');

    // Simpan gambar PNG ke storage disk
    $pngContent = imagecreatetruecolor(50, 50);
    ob_start();
    imagepng($pngContent);
    $binaryPng = ob_get_clean();
    imagedestroy($pngContent);

    Storage::disk('public')->put('berita/foto-kegiatan.png', $binaryPng);

    $berita = Berita::create([
        'judul' => 'Liputan Pelayanan Terpadu',
        'isi' => 'Konten liputan kegiatan kedinasan.',
        'gambar_utama' => 'berita/foto-kegiatan.png',
        'status' => 'terbit',
    ]);

    // Model saving hook harus mengonversi path menjadi .webp
    expect(str_ends_with($berita->gambar_utama, '.webp'))->toBeTrue();
    Storage::disk('public')->assertExists($berita->gambar_utama);
});

/*
|--------------------------------------------------------------------------
| 3. UJI HAK AKSES PER PERAN (SUPER ADMIN, ADMIN KONTEN VS PPID)
|--------------------------------------------------------------------------
*/

test('Super Admin dan Admin Konten memiliki izin mengelola konten', function () {
    $this->seed(RolePermissionSeeder::class);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $adminKonten = User::factory()->create();
    $adminKonten->assignRole('Admin Konten');

    $beritaPolicy = new BeritaPolicy();
    $halamanPolicy = new HalamanPolicy();
    $galeriPolicy = new GaleriPolicy();
    $bannerPolicy = new BannerPolicy();
    $dokumenPolicy = new DokumenInformasiPolicy();

    // Super Admin check
    expect($beritaPolicy->viewAny($superAdmin))->toBeTrue()
        ->and($beritaPolicy->create($superAdmin))->toBeTrue()
        ->and($halamanPolicy->viewAny($superAdmin))->toBeTrue()
        ->and($galeriPolicy->viewAny($superAdmin))->toBeTrue()
        ->and($bannerPolicy->viewAny($superAdmin))->toBeTrue()
        ->and($dokumenPolicy->viewAny($superAdmin))->toBeTrue();

    // Admin Konten check
    expect($beritaPolicy->viewAny($adminKonten))->toBeTrue()
        ->and($beritaPolicy->create($adminKonten))->toBeTrue()
        ->and($halamanPolicy->viewAny($adminKonten))->toBeTrue()
        ->and($galeriPolicy->viewAny($adminKonten))->toBeTrue()
        ->and($bannerPolicy->viewAny($adminKonten))->toBeTrue()
        ->and($dokumenPolicy->viewAny($adminKonten))->toBeTrue();
});

test('Petugas PPID dan Atasan PPID tidak memiliki izin mengelola modul konten CMS umum', function () {
    $this->seed(RolePermissionSeeder::class);

    $petugasPpid = User::factory()->create();
    $petugasPpid->assignRole('Petugas PPID');

    $atasanPpid = User::factory()->create();
    $atasanPpid->assignRole('Atasan PPID');

    $beritaPolicy = new BeritaPolicy();
    $halamanPolicy = new HalamanPolicy();
    $galeriPolicy = new GaleriPolicy();
    $bannerPolicy = new BannerPolicy();

    // Petugas PPID ditolak di modul konten berita, halaman, galeri, banner
    expect($beritaPolicy->viewAny($petugasPpid))->toBeFalse()
        ->and($beritaPolicy->create($petugasPpid))->toBeFalse()
        ->and($halamanPolicy->viewAny($petugasPpid))->toBeFalse()
        ->and($galeriPolicy->viewAny($petugasPpid))->toBeFalse()
        ->and($bannerPolicy->viewAny($petugasPpid))->toBeFalse();

    // Atasan PPID juga ditolak di modul konten umum
    expect($beritaPolicy->viewAny($atasanPpid))->toBeFalse()
        ->and($halamanPolicy->viewAny($atasanPpid))->toBeFalse()
        ->and($galeriPolicy->viewAny($atasanPpid))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| 4. UJI KUOTA PEMBARUAN KONTEN BULANAN & DASBOR ADMIN
|--------------------------------------------------------------------------
*/

test('ContentQuotaService menghitung jumlah pembaruan konten dan menghasilkan status kuota paket', function () {
    $quotaService = new ContentQuotaService();

    // Buat berita baru
    Berita::create([
        'judul' => 'Pembaruan Kebijakan 1',
        'isi' => 'Konten pembaruan.',
        'status' => 'terbit',
    ]);

    // Buat halaman baru
    Halaman::create([
        'judul' => 'Halaman Tugas Pokok',
        'isi' => 'Rincian tugas dan fungsi.',
    ]);

    $count = $quotaService->getMonthlyUpdatesCount();
    expect($count)->toBeGreaterThanOrEqual(2);

    $status = $quotaService->getQuotaStatus();
    $currentLimit = Package::limit('content_updates_per_month');

    expect($status)->toHaveKeys(['used', 'limit', 'limit_label', 'display', 'package', 'is_exceeded'])
        ->and($status['display'])->toBe("{$count} dari {$status['limit_label']}");

    // Memastikan admin TIDAK diblokir ketika membuat konten melebihi batas kuota (hanya visual display)
    $beritaExtra = Berita::create([
        'judul' => 'Pembaruan Tambahan Tanpa Blokir',
        'isi' => 'Konten tetap tersimpan dengan lancar.',
        'status' => 'terbit',
    ]);
    expect($beritaExtra->id)->not()->toBeNull();
});

/*
|--------------------------------------------------------------------------
| 5. UJI SEO: SITEMAP.XML, ROBOTS.TXT, DAN HALAMAN STATIS
|--------------------------------------------------------------------------
*/

test('sitemap.xml menghasilkan XML yang valid dengan rute-rute publik dan konten', function () {
    Berita::create([
        'judul' => 'Berita untuk Sitemap',
        'isi' => 'Konten sitemap.',
        'status' => 'terbit',
    ]);

    Halaman::create([
        'judul' => 'Struktur Organisasi Daerah',
        'slug' => 'struktur-organisasi',
        'isi' => 'Bagan susunan organisasi.',
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('<urlset', false);
    $response->assertSee(route('profil'), false);
    $response->assertSee(route('berita.index'), false);
    $response->assertSee('struktur-organisasi', false);
});

test('robots.txt merespons dengan aturan crawler dan tautan ke sitemap', function () {
    $response = $this->get('/robots.txt');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    $response->assertSee('User-agent: *');
    $response->assertSee('Disallow: /admin/');
    $response->assertSee('Sitemap: ' . url('/sitemap.xml'));
});

test('halaman statis dinamis dapat diakses publik melalui rute /halaman/{slug}', function () {
    $halaman = Halaman::create([
        'judul' => 'Visi dan Misi Strategis',
        'slug' => 'visi-misi-strategis',
        'ringkasan' => 'Arah dan visi pembangunan jangka menengah.',
        'isi' => '<p>Mewujudkan pemerintahan yang bersih dan pelayanan prima.</p>',
        'is_active' => true,
        'show_in_menu' => true,
    ]);

    $response = $this->get(route('halaman.show', 'visi-misi-strategis'));

    $response->assertStatus(200);
    $response->assertSee('Visi dan Misi Strategis');
    $response->assertSee('Mewujudkan pemerintahan yang bersih');
});
