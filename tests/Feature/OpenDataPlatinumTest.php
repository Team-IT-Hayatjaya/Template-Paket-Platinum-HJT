<?php

use App\Models\Dataset;
use App\Models\DatasetVersion;
use App\Models\User;
use App\Services\OpenDataService;
use App\Support\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

test('open data feature is strictly available only on platinum tier and blocked on silver and gold', function () {
    // 1. Silver tier
    config(['packages.default' => 'silver']);
    expect(Package::has('open_data'))->toBeFalse();
    $this->get('/open-data')->assertStatus(404);
    $this->getJson('/api/v1/datasets')->assertStatus(403);

    // 2. Gold tier
    config(['packages.default' => 'gold']);
    expect(Package::has('open_data'))->toBeFalse();
    $this->get('/open-data')->assertStatus(404);
    $this->getJson('/api/v1/datasets')->assertStatus(403);

    // 3. Platinum tier
    config(['packages.default' => 'platinum']);
    expect(Package::has('open_data'))->toBeTrue();
    $this->get('/open-data')->assertStatus(200);
    $this->getJson('/api/v1/datasets')->assertStatus(200);
});

test('open data service validates and parses csv columns, types, and preview', function () {
    $csvContent = "Kecamatan,Jumlah_Penduduk,Target_Realisasi\nLoa Kulu,50000,95.5\nKota Bangun,52000,97.0\nMuara Muntai,54500,98.2";
    Storage::disk('public')->put('test.csv', $csvContent);

    $service = app(OpenDataService::class);
    $parsed = $service->validateAndParseCsv('test.csv');

    expect($parsed['delimiter'])->toBe(',')
        ->and($parsed['headers'])->toBe(['Kecamatan', 'Jumlah_Penduduk', 'Target_Realisasi'])
        ->and($parsed['row_count'])->toBe(3)
        ->and($parsed['label_columns'])->toContain('Kecamatan')
        ->and($parsed['numeric_columns'])->toContain('Jumlah_Penduduk')
        ->and($parsed['numeric_columns'])->toContain('Target_Realisasi')
        ->and(count($parsed['preview']))->toBe(3);
});

test('public catalog lists datasets with search and category filter', function () {
    config(['packages.default' => 'platinum']);

    $csvContent = "Wilayah,Nilai\nKecamatan A,100\nKecamatan B,200";
    Storage::disk('public')->put('datasets/penduduk.csv', $csvContent);
    Storage::disk('public')->put('datasets/kesehatan.csv', $csvContent);

    Dataset::create([
        'judul' => 'Data Kependudukan Tahunan',
        'slug' => 'data-kependudukan-tahunan',
        'kategori' => 'Kependudukan',
        'sumber' => 'Dukcapil',
        'periode' => '2026',
        'penanggung_jawab' => 'Bidang TI',
        'berkas_csv' => 'datasets/penduduk.csv',
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    Dataset::create([
        'judul' => 'Data Cakupan Puskesmas',
        'slug' => 'data-cakupan-puskesmas',
        'kategori' => 'Kesehatan',
        'sumber' => 'Dinkes',
        'periode' => '2026',
        'penanggung_jawab' => 'Bidang Kesehatan',
        'berkas_csv' => 'datasets/kesehatan.csv',
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    // Katalog publik
    $response = $this->get('/open-data');
    $response->assertStatus(200)
        ->assertSee('Data Kependudukan Tahunan')
        ->assertSee('Data Cakupan Puskesmas');

    // Filter kategori Kependudukan
    $filtered = $this->get('/open-data?kategori=Kependudukan');
    $filtered->assertStatus(200)
        ->assertSee('Data Kependudukan Tahunan')
        ->assertDontSee('Data Cakupan Puskesmas');

    // Pencarian kata kunci Puskesmas
    $searched = $this->get('/open-data?q=Puskesmas');
    $searched->assertStatus(200)
        ->assertSee('Data Cakupan Puskesmas')
        ->assertDontSee('Data Kependudukan Tahunan');
});

test('detail dataset displays preview data and chart config', function () {
    config(['packages.default' => 'platinum']);

    $csvContent = "Bulan,Kasus_Sembuh\nJanuari,150\nFebruari,210\nMaret,185";
    Storage::disk('public')->put('datasets/sembuh.csv', $csvContent);

    $dataset = Dataset::create([
        'judul' => 'Perkembangan Kesembuhan Pasien',
        'slug' => 'perkembangan-kesembuhan-pasien',
        'kategori' => 'Kesehatan',
        'sumber' => 'RSUD',
        'periode' => '2026',
        'penanggung_jawab' => 'Humas RSUD',
        'berkas_csv' => 'datasets/sembuh.csv',
        'chart_type' => 'bar',
        'chart_x_column' => 'Bulan',
        'chart_y_column' => 'Kasus_Sembuh',
        'row_count' => 3,
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    $response = $this->get(route('open-data.detail', $dataset->slug));
    $response->assertStatus(200)
        ->assertSee('Perkembangan Kesembuhan Pasien')
        ->assertSee('Januari')
        ->assertSee('150')
        ->assertSee('RSUD');

    // Views incremented
    expect($dataset->fresh()->total_views)->toBe(1);
});

test('download dataset supports csv and json and increments counter', function () {
    config(['packages.default' => 'platinum']);

    $csvContent = "Item,Total\nBuku,1000\nMeja,500";
    Storage::disk('public')->put('datasets/aset.csv', $csvContent);

    $dataset = Dataset::create([
        'judul' => 'Inventaris Sarana Pendidikan',
        'slug' => 'inventaris-sarana-pendidikan',
        'kategori' => 'Pendidikan',
        'sumber' => 'Disdik',
        'periode' => '2026',
        'penanggung_jawab' => 'Aset',
        'berkas_csv' => 'datasets/aset.csv',
        'total_unduhan' => 0,
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    // Unduh CSV
    $csvRes = $this->get(route('open-data.download', ['slug' => $dataset->slug, 'format' => 'csv']));
    $csvRes->assertStatus(200);
    expect($dataset->fresh()->total_unduhan)->toBe(1);

    // Unduh JSON
    $jsonRes = $this->get(route('open-data.download', ['slug' => $dataset->slug, 'format' => 'json']));
    $jsonRes->assertStatus(200)
        ->assertJsonStructure([
            'metadata' => ['id', 'judul', 'slug', 'kategori'],
            'data',
        ]);
    expect($dataset->fresh()->total_unduhan)->toBe(2);
});

test('api v1 datasets provides read-only endpoints with metadata and records', function () {
    config(['packages.default' => 'platinum']);

    $csvContent = "Komoditas,Produksi_Ton\nPadi,1200\nJagung,850";
    Storage::disk('public')->put('datasets/pertanian.csv', $csvContent);

    $dataset = Dataset::create([
        'judul' => 'Produksi Tanaman Pangan',
        'slug' => 'produksi-tanaman-pangan',
        'kategori' => 'Pertanian',
        'sumber' => 'Distan',
        'periode' => '2026',
        'penanggung_jawab' => 'Bidang Pangan',
        'berkas_csv' => 'datasets/pertanian.csv',
        'row_count' => 2,
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    // 1. API List
    $listRes = $this->getJson('/api/v1/datasets');
    $listRes->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'package',
            'pagination' => ['current_page', 'per_page', 'total'],
            'data' => [
                '*' => ['id', 'judul', 'slug', 'kategori', 'sumber', 'links'],
            ],
        ]);

    // 2. API Detail
    $detailRes = $this->getJson("/api/v1/datasets/{$dataset->slug}");
    $detailRes->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'metadata',
            'chart_config',
            'data' => [
                '*' => ['Komoditas', 'Produksi_Ton'],
            ],
        ])
        ->assertJsonPath('metadata.slug', 'produksi-tanaman-pangan');

    // 3. API 404 on invalid slug
    $this->getJson('/api/v1/datasets/tidak-ada')->assertStatus(404);
});

test('dataset version history records previous versions on file update', function () {
    config(['packages.default' => 'platinum']);

    $dataset = Dataset::create([
        'judul' => 'Statistik Tenaga Medis',
        'slug' => 'statistik-tenaga-medis',
        'kategori' => 'Kesehatan',
        'sumber' => 'Dinkes',
        'periode' => '2026',
        'penanggung_jawab' => 'SDM',
        'berkas_csv' => 'datasets/medis_v1.csv',
        'version' => 1,
        'row_count' => 25,
        'tanggal_pembaruan' => now()->subMonths(2),
        'is_published' => true,
    ]);

    // Catat riwayat versi 1
    DatasetVersion::create([
        'dataset_id' => $dataset->id,
        'version' => 1,
        'berkas_csv' => 'datasets/medis_v1.csv',
        'catatan_perubahan' => 'Rilis perdana dataset tenaga medis 2026',
        'row_count' => 25,
    ]);

    // Naikkan dataset ke versi 2
    $dataset->update([
        'version' => 2,
        'berkas_csv' => 'datasets/medis_v2.csv',
        'row_count' => 30,
        'tanggal_pembaruan' => now(),
    ]);

    expect($dataset->fresh()->version)->toBe(2)
        ->and($dataset->versions()->count())->toBe(1)
        ->and($dataset->versions()->first()->version)->toBe(1);
});
