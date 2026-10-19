<?php

use App\Contracts\WhatsAppProvider;
use App\Models\Berita;
use App\Models\Dataset;
use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use App\Models\Unit;
use App\Models\WhatsappConversation;
use App\Models\WhatsappFaq;
use App\Models\WhatsappTicket;
use App\Services\MonthlyReportService;
use App\Services\WhatsApp\MockWhatsAppProvider;
use App\Support\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    MockWhatsAppProvider::clear();
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
    MockWhatsAppProvider::clear();
});

test('whatsapp bot feature is enabled only on platinum and prohibited on silver and gold', function () {
    config(['packages.default' => 'silver']);
    expect(Package::has('whatsapp_bot'))->toBeFalse();
    $this->get('/api/v1/whatsapp/webhook')->assertStatus(404);
    $this->postJson('/api/v1/whatsapp/webhook', [])->assertStatus(403);

    config(['packages.default' => 'gold']);
    expect(Package::has('whatsapp_bot'))->toBeFalse();
    $this->get('/api/v1/whatsapp/webhook')->assertStatus(404);
    $this->postJson('/api/v1/whatsapp/webhook', [])->assertStatus(403);

    config(['packages.default' => 'platinum']);
    expect(Package::has('whatsapp_bot'))->toBeTrue();
});

test('webhook hub challenge verification responds with challenge token', function () {
    config(['packages.default' => 'platinum']);

    $response = $this->get('/api/v1/whatsapp/webhook?hub.mode=subscribe&hub.challenge=test_token_9988&hub.verify_token=valid');
    $response->assertStatus(200);
});

test('bot sends main menu on greeting or menu keywords', function () {
    config(['packages.default' => 'platinum']);

    $res = $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => 'menu',
    ]);

    $res->assertStatus(200)
        ->assertJsonPath('result.tipe_menu', 'menu_utama');

    $last = MockWhatsAppProvider::lastMessage();
    expect($last)->not()->toBeNull()
        ->and($last['to'])->toBe('6281234567890')
        ->and($last['message'])->toContain('Cek Status Permohonan PPID')
        ->and($last['message'])->toContain('Informasi Publik Terbaru');
});

test('bot menu 1 checks ppid permohonan status accurately', function () {
    config(['packages.default' => 'platinum']);

    $status = StatusPermohonan::firstOrCreate([
        'nama' => 'Sedang Diproses Petugas',
        'slug' => 'proses',
        'warna' => 'warning',
    ]);

    $permohonan = PermohonanInformasi::create([
        'nomor_registrasi' => 'PPID-202610-0999',
        'nama_pemohon' => 'Budi Santoso',
        'nik' => '6402010000000001',
        'email' => 'budi@example.com',
        'telepon' => '081299998888',
        'pekerjaan' => 'Wiraswasta',
        'alamat' => 'Jl. Pahlawan No. 10',
        'rincian_informasi' => 'Data realisasi anggaran infrastruktur jembatan tahun 2026',
        'tujuan_penggunaan' => 'Riset akademis dan transparansi publik',
        'cara_memperoleh' => 'Melihat / Membaca',
        'cara_mendapatkan' => 'Salinan Elektronik (Email)',
        'status_id' => $status->id,
    ]);

    // Check with valid registration number
    $resValid = $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => '1 PPID-202610-0999',
    ]);

    $resValid->assertStatus(200);
    $lastValid = MockWhatsAppProvider::lastMessage();
    expect($lastValid['message'])->toContain('PPID-202610-0999')
        ->and($lastValid['message'])->toContain('Budi Santoso')
        ->and($lastValid['message'])->toContain('Sedang Diproses Petugas');

    // Check with invalid registration number
    $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => '1 PPID-INVALID-999',
    ]);

    $lastInvalid = MockWhatsAppProvider::lastMessage();
    expect($lastInvalid['message'])->toContain('tidak ditemukan');
});

test('bot menu 2 returns latest published public news', function () {
    config(['packages.default' => 'platinum']);

    Berita::create([
        'judul' => 'Sosialisasi Program Keterbukaan Informasi Desa',
        'slug' => 'sosialisasi-program-keterbukaan-informasi-desa',
        'isi' => 'Isi berita resmi',
        'status' => 'terbit',
        'tanggal_terbit' => now()->subHours(2),
    ]);

    $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => '2',
    ]);

    $last = MockWhatsAppProvider::lastMessage();
    expect($last['message'])->toContain('Sosialisasi Program Keterbukaan Informasi Desa');
});

test('bot menu 3 returns contact and service hours', function () {
    config(['packages.default' => 'platinum']);

    $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => '3',
    ]);

    $last = MockWhatsAppProvider::lastMessage();
    expect($last['message'])->toContain('KONTAK & JAM LAYANAN PPID')
        ->and($last['message'])->toContain('Jam Layanan');
});

test('bot menu 4 answers faqs and custom keywords', function () {
    config(['packages.default' => 'platinum']);

    WhatsappFaq::create([
        'kata_kunci' => 'retribusi',
        'pertanyaan' => 'Bagaimana ketentuan retribusi daerah?',
        'jawaban' => 'Retribusi daerah diatur melalui Peraturan Daerah Nomor 5 Tahun 2024.',
        'is_active' => true,
    ]);

    $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281234567890',
        'text' => 'retribusi',
    ]);

    $last = MockWhatsAppProvider::lastMessage();
    expect($last['message'])->toContain('Bagaimana ketentuan retribusi daerah?')
        ->and($last['message'])->toContain('Peraturan Daerah Nomor 5 Tahun 2024');
});

test('unrecognized questions escalate automatically to admin tickets', function () {
    config(['packages.default' => 'platinum']);

    $question = 'Bagaimana prosedur izin penebangan pohon peneduh di tepi jalan kabupaten?';

    $res = $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281299994444',
        'text' => $question,
    ]);

    $res->assertStatus(200);

    // Verify ticket created
    $ticket = WhatsappTicket::latest()->first();
    expect($ticket)->not()->toBeNull()
        ->and($ticket->pesan)->toBe($question)
        ->and($ticket->status)->toBe('menunggu_petugas')
        ->and($ticket->phone_number_masked)->toContain('****')
        ->and($ticket->phone_number_masked)->not()->toBe('6281299994444'); // Privacy preserved

    $last = MockWhatsAppProvider::lastMessage();
    expect($last['message'])->toContain($ticket->nomor_tiket);
    expect($last['message'])->toContain('Nomor Tiket');
    expect($last['message'])->toContain('petugas');
});

test('conversations are logged with masked phone number for privacy', function () {
    config(['packages.default' => 'platinum']);

    $this->postJson('/api/v1/whatsapp/test-simulate', [
        'from' => '6281355556666',
        'text' => 'halo bot',
    ]);

    $log = WhatsappConversation::latest()->first();
    expect($log)->not()->toBeNull()
        ->and($log->phone_hash)->toBe(hash('sha256', '6281355556666'))
        ->and($log->phone_masked)->toContain('****')
        ->and($log->phone_masked)->not()->toBe('6281355556666');
});

test('monthly report includes units open data and whatsapp stats on platinum tier', function () {
    config(['packages.default' => 'platinum']);

    // 1. Create Unit data
    $unit = Unit::create([
        'nama' => 'Dinas Pariwisata',
        'slug' => 'dispar',
        'subdomain' => 'dispar',
        'is_active' => true,
    ]);

    // 2. Create Dataset
    Dataset::create([
        'judul' => 'Data Kunjungan Wisatawan 2026',
        'slug' => 'data-kunjungan-wisatawan-2026',
        'kategori' => 'Pariwisata',
        'sumber' => 'Dispar',
        'periode' => '2026',
        'penanggung_jawab' => 'Bidang Pemasaran',
        'berkas_csv' => 'datasets/wisata.csv',
        'total_unduhan' => 15,
        'tanggal_pembaruan' => now(),
        'is_published' => true,
    ]);

    // 3. Create Whatsapp Conversation & Ticket
    WhatsappConversation::create([
        'phone_hash' => hash('sha256', '628111'),
        'phone_masked' => '62811****',
        'pesan_masuk' => '1 PPID-01',
        'pesan_keluar' => 'Status',
        'tipe_menu' => 'cek_ppid',
        'status' => 'sukses',
    ]);

    WhatsappTicket::create([
        'nomor_tiket' => 'WA-202610-0099',
        'phone_number_masked' => '62811****',
        'pesan' => 'Pertanyaan warga',
        'status' => 'menunggu_petugas',
    ]);

    $reportService = app(MonthlyReportService::class);
    $data = $reportService->getReportData((int) now()->format('Y'), (int) now()->format('n'));

    // On Platinum, all 3 new report sections must exist and have accurate stats
    expect($data['units'])->not()->toBeNull()
        ->and($data['units']['total_units'])->toBeGreaterThanOrEqual(1)
        ->and($data['open_data'])->not()->toBeNull()
        ->and($data['open_data']['total_datasets'])->toBeGreaterThanOrEqual(1)
        ->and($data['whatsapp'])->not()->toBeNull()
        ->and($data['whatsapp']['total_conversations'])->toBeGreaterThanOrEqual(1)
        ->and($data['whatsapp']['total_tickets'])->toBeGreaterThanOrEqual(1);

    // On Gold, they must be null
    config(['packages.default' => 'gold']);
    $goldData = $reportService->getReportData((int) now()->format('Y'), (int) now()->format('n'));
    expect($goldData['units'])->toBeNull()
        ->and($goldData['open_data'])->toBeNull()
        ->and($goldData['whatsapp'])->toBeNull();
});
