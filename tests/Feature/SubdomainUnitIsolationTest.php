<?php

use App\Facades\UnitContext;
use App\Models\Berita;
use App\Models\Unit;
use App\Models\User;
use App\Policies\BeritaPolicy;
use App\Policies\UnitPolicy;
use App\Services\ContentDataService;
use App\Support\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed Spatie roles
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Admin Konten', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Admin Unit', 'guard_name' => 'web']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
    UnitContext::reset();
});

test('subdomains quota is limited to 5 on platinum tier and prohibited on silver and gold', function () {
    config(['packages.default' => 'silver']);
    expect(Package::has('subdomains'))->toBeFalse()
        ->and(Package::limit('subdomains'))->toBe(0);

    config(['packages.default' => 'gold']);
    expect(Package::has('subdomains'))->toBeFalse()
        ->and(Package::limit('subdomains'))->toBe(0);

    config(['packages.default' => 'platinum']);
    expect(Package::has('subdomains'))->toBeTrue()
        ->and(Package::limit('subdomains'))->toBe(5);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $policy = new UnitPolicy();

    // Create 5 units
    for ($i = 1; $i <= 5; $i++) {
        expect($policy->create($superAdmin))->toBeTrue();
        Unit::create([
            'nama' => "Unit Kerja {$i}",
            'slug' => "unit-kerja-{$i}",
            'subdomain' => "unit{$i}",
            'is_active' => true,
        ]);
    }

    // 6th unit should be prohibited by policy (quota limit reached)
    expect(Unit::count())->toBe(5)
        ->and($policy->create($superAdmin))->toBeFalse();
});

test('detects unit context from host subdomain and headers in platinum tier', function () {
    config(['packages.default' => 'platinum']);

    $unit = Unit::create([
        'nama' => 'Dinas Pemuda dan Olahraga',
        'slug' => 'dispora',
        'subdomain' => 'dispora',
        'singkatan' => 'DISPORA',
        'is_active' => true,
    ]);

    // Test with subdomain header
    $response = $this->withHeaders(['X-Unit-Subdomain' => 'dispora'])->get('/');
    $response->assertStatus(200);
    expect(UnitContext::current()?->id)->toBe($unit->id);

    $this->flushHeaders();

    // Test with server HTTP_HOST via full URL
    $this->get('http://dispora.pemprov.go.id/');
    expect(UnitContext::current()?->id)->toBe($unit->id);

    // Test main portal (no subdomain match)
    $this->get('http://pemprov.go.id/');
    expect(UnitContext::current())->toBeNull();
});

test('data between units does not leak and is strictly isolated', function () {
    config(['packages.default' => 'platinum']);

    $unitA = Unit::create([
        'nama' => 'Dinas Pendidikan',
        'slug' => 'disdik',
        'subdomain' => 'disdik',
        'singkatan' => 'DISDIK',
        'is_active' => true,
    ]);

    $unitB = Unit::create([
        'nama' => 'Dinas Kesehatan',
        'slug' => 'dinkes',
        'subdomain' => 'dinkes',
        'singkatan' => 'DINKES',
        'is_active' => true,
    ]);

    // Create news for Unit A
    UnitContext::set($unitA);
    $beritaA = Berita::create([
        'judul' => 'Kegiatan Beasiswa Pendidikan Unggulan',
        'slug' => 'kegiatan-beasiswa-pendidikan-unggulan',
        'isi' => 'Berita khusus Dinas Pendidikan.',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);
    expect($beritaA->unit_id)->toBe($unitA->id);

    // Create news for Unit B
    UnitContext::set($unitB);
    $beritaB = Berita::create([
        'judul' => 'Program Vaksinasi Balita Serentak',
        'slug' => 'program-vaksinasi-balita-serentak',
        'isi' => 'Berita khusus Dinas Kesehatan.',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);
    expect($beritaB->unit_id)->toBe($unitB->id);

    // Create news for Portal Utama
    UnitContext::reset();
    $beritaPortal = Berita::create([
        'unit_id' => null,
        'judul' => 'Pengumuman Resmi Sekretariat Daerah',
        'slug' => 'pengumuman-resmi-sekretariat-daerah',
        'isi' => 'Berita portal utama pemerintah.',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);
    expect($beritaPortal->unit_id)->toBeNull();

    // 1. Querying within Unit A context (disdik)
    UnitContext::set($unitA);
    $itemsA = Berita::published()->get();
    expect($itemsA->pluck('id'))->toContain($beritaA->id)
        ->and($itemsA->pluck('id'))->not()->toContain($beritaB->id)
        ->and($itemsA->pluck('id'))->not()->toContain($beritaPortal->id);

    // 2. Querying within Unit B context (dinkes)
    UnitContext::set($unitB);
    $itemsB = Berita::published()->get();
    expect($itemsB->pluck('id'))->toContain($beritaB->id)
        ->and($itemsB->pluck('id'))->not()->toContain($beritaA->id)
        ->and($itemsB->pluck('id'))->not()->toContain($beritaPortal->id);

    // 3. Querying within Portal Utama context
    UnitContext::reset();
    $itemsPortal = Berita::published()->get();
    expect($itemsPortal->pluck('id'))->toContain($beritaPortal->id)
        ->and($itemsPortal->pluck('id'))->not()->toContain($beritaA->id)
        ->and($itemsPortal->pluck('id'))->not()->toContain($beritaB->id);

    // 4. HTTP Request Isolation test: Disdik host accessing Dinkes news detail must 404
    $response = $this->withHeaders(['X-Unit-Subdomain' => 'disdik'])
        ->get(route('berita.detail', $beritaB->slug));
    $response->assertStatus(404);

    // Accessing own news must 200
    $responseOwn = $this->withHeaders(['X-Unit-Subdomain' => 'disdik'])
        ->get(route('berita.detail', $beritaA->slug));
    $responseOwn->assertStatus(200);
});

test('admin unit role is strictly confined to own unit data', function () {
    config(['packages.default' => 'platinum']);

    $unitA = Unit::create([
        'nama' => 'Unit Pelayanan Pajak Daerah',
        'slug' => 'uppd',
        'subdomain' => 'uppd',
        'is_active' => true,
    ]);

    $unitB = Unit::create([
        'nama' => 'Unit Pelayanan Kehutanan',
        'slug' => 'upk',
        'subdomain' => 'upk',
        'is_active' => true,
    ]);

    $adminA = User::factory()->create(['unit_id' => $unitA->id]);
    $adminA->assignRole('Admin Unit');

    $adminB = User::factory()->create(['unit_id' => $unitB->id]);
    $adminB->assignRole('Admin Unit');

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $beritaA = Berita::create([
        'unit_id' => $unitA->id,
        'judul' => 'Pengumuman Pajak UPPD',
        'slug' => 'pengumuman-pajak-uppd',
        'isi' => 'Konten UPPD',
        'status' => 'terbit',
    ]);

    $beritaB = Berita::create([
        'unit_id' => $unitB->id,
        'judul' => 'Pengumuman Hutan Lindung UPK',
        'slug' => 'pengumuman-hutan-lindung-upk',
        'isi' => 'Konten UPK',
        'status' => 'terbit',
    ]);

    $policy = new BeritaPolicy();

    // Admin A permissions
    expect($policy->view($adminA, $beritaA))->toBeTrue()
        ->and($policy->update($adminA, $beritaA))->toBeTrue()
        ->and($policy->delete($adminA, $beritaA))->toBeTrue()
        // Admin A trying to access Unit B data -> DENIED
        ->and($policy->view($adminA, $beritaB))->toBeFalse()
        ->and($policy->update($adminA, $beritaB))->toBeFalse()
        ->and($policy->delete($adminA, $beritaB))->toBeFalse();

    // Admin B permissions
    expect($policy->view($adminB, $beritaB))->toBeTrue()
        ->and($policy->update($adminB, $beritaB))->toBeTrue()
        ->and($policy->delete($adminB, $beritaB))->toBeTrue()
        // Admin B trying to access Unit A data -> DENIED
        ->and($policy->view($adminB, $beritaA))->toBeFalse()
        ->and($policy->update($adminB, $beritaA))->toBeFalse()
        ->and($policy->delete($adminB, $beritaA))->toBeFalse();

    // Super Admin can access all
    expect($policy->view($superAdmin, $beritaA))->toBeTrue()
        ->and($policy->update($superAdmin, $beritaA))->toBeTrue()
        ->and($policy->view($superAdmin, $beritaB))->toBeTrue()
        ->and($policy->update($superAdmin, $beritaB))->toBeTrue();
});

test('portal utama can aggregate news from all units', function () {
    config(['packages.default' => 'platinum']);

    $unit = Unit::create([
        'nama' => 'Dinas Perhubungan',
        'slug' => 'dishub',
        'subdomain' => 'dishub',
        'singkatan' => 'DISHUB',
        'is_active' => true,
    ]);

    Berita::create([
        'unit_id' => $unit->id,
        'judul' => 'Rekayasa Lalu Lintas PON',
        'slug' => 'rekayasa-lalu-lintas-pon',
        'isi' => 'Isi berita dishub',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);

    Berita::create([
        'unit_id' => null,
        'judul' => 'Pengumuman Portal Utama',
        'slug' => 'pengumuman-portal-utama',
        'isi' => 'Isi portal',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);

    $contentService = app(ContentDataService::class);
    $agregasi = $contentService->getBeritaAgregasi(10);

    expect(count($agregasi))->toBeGreaterThanOrEqual(2);

    $dishubNews = collect($agregasi)->firstWhere('slug', 'rekayasa-lalu-lintas-pon');
    expect($dishubNews)->not()->toBeNull()
        ->and($dishubNews['unit_singkatan'])->toBe('DISHUB');
});

test('unit sitemap contains only unit URLs and does not leak cross-unit URLs', function () {
    config(['packages.default' => 'platinum']);

    $unitA = Unit::create([
        'nama' => 'Unit Pariwisata',
        'slug' => 'dispar',
        'subdomain' => 'dispar',
        'singkatan' => 'DISPAR',
        'is_active' => true,
    ]);

    $unitB = Unit::create([
        'nama' => 'Unit Perikanan',
        'slug' => 'diskan',
        'subdomain' => 'diskan',
        'singkatan' => 'DISKAN',
        'is_active' => true,
    ]);

    $beritaA = Berita::create([
        'unit_id' => $unitA->id,
        'judul' => 'Festival Budaya Bahari',
        'slug' => 'festival-budaya-bahari',
        'isi' => 'Konten Dispar',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);

    $beritaB = Berita::create([
        'unit_id' => $unitB->id,
        'judul' => 'Bantuan Bibit Ikan Nila',
        'slug' => 'bantuan-bibit-ikan-nila',
        'isi' => 'Konten Diskan',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);

    // Dispar Sitemap
    $responseA = $this->withHeaders(['X-Unit-Subdomain' => 'dispar'])->get('/sitemap.xml');
    $responseA->assertStatus(200);
    $xmlA = $responseA->getContent();

    expect($xmlA)->toContain('festival-budaya-bahari')
        ->and($xmlA)->not()->toContain('bantuan-bibit-ikan-nila');

    // Diskan Sitemap
    $responseB = $this->withHeaders(['X-Unit-Subdomain' => 'diskan'])->get('/sitemap.xml');
    $responseB->assertStatus(200);
    $xmlB = $responseB->getContent();

    expect($xmlB)->toContain('bantuan-bibit-ikan-nila')
        ->and($xmlB)->not()->toContain('festival-budaya-bahari');
});

test('silver and gold tiers disable unit scoping and behave identically to original codebase', function () {
    config(['packages.default' => 'gold']);

    // UnitScope is inactive on gold
    expect(Package::has('subdomains'))->toBeFalse();

    $unit = Unit::create([
        'nama' => 'Unit Dummy',
        'slug' => 'dummy',
        'subdomain' => 'dummy',
        'is_active' => true,
    ]);

    $berita = Berita::create([
        'unit_id' => $unit->id,
        'judul' => 'Berita Paket Gold Bebas Scope',
        'slug' => 'berita-paket-gold-bebas-scope',
        'isi' => 'Konten Gold',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subDay(),
    ]);

    // On Gold, global scope is bypassed, so all published news is accessible as normal
    $all = Berita::published()->get();
    expect($all->pluck('id'))->toContain($berita->id);
});
