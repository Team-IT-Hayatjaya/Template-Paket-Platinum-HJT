<?php

use App\Mail\AccountLockedMail;
use App\Mail\DefacementAlertMail;
use App\Models\IntegrityCheck;
use App\Models\IpBlocklist;
use App\Models\User;
use App\Services\AccountLockoutService;
use App\Services\IntegrityMonitorService;
use App\Services\MonthlyReportService;
use App\Services\SecurityReportService;
use App\Support\Package;
use Database\Seeders\HalamanSeeder;
use Database\Seeders\KategoriInformasiSeeder;
use Database\Seeders\PengaturanPpidSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusPermohonanSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(KategoriInformasiSeeder::class);
    $this->seed(StatusPermohonanSeeder::class);
    $this->seed(PengaturanPpidSeeder::class);
    $this->seed(HalamanSeeder::class);
    config(['packages.default' => 'gold']);
});

afterEach(function () {
    config(['packages.default' => env('SITE_PACKAGE', 'gold')]);
});

/*
|--------------------------------------------------------------------------
| 1. UJI PENGUNCIAN AKUN (ACCOUNT LOCKOUT) & NOTIFIKASI EMAIL
|--------------------------------------------------------------------------
*/

test('kunci akun sementara bekerja setelah gagal login berulang dan mengirim email ke super admin', function () {
    Mail::fake();

    expect(Package::has('security_hardening'))->toBeTrue();

    $superAdmin = User::factory()->create([
        'email' => 'superadmin@kukar.go.id',
    ]);
    $superAdmin->assignRole('Super Admin');

    $targetUser = User::factory()->create([
        'email' => 'staff@kukar.go.id',
        'failed_login_attempts' => 0,
        'locked_until' => null,
    ]);

    $service = app(AccountLockoutService::class);
    $maxAttempts = $service->maxAttempts();

    // Simulasikan percobaan login gagal hingga batas maksimal
    for ($i = 1; $i <= $maxAttempts; $i++) {
        $service->recordFailedAttempt('staff@kukar.go.id', '192.168.1.100');
    }

    $targetUser->refresh();

    // Akun harus terkunci
    expect($targetUser->isLocked())->toBeTrue()
        ->and($targetUser->locked_until)->not()->toBeNull()
        ->and($service->isLocked('staff@kukar.go.id'))->toBeTrue();

    // Notifikasi email harus dikirimkan ke Super Admin
    Mail::assertSent(AccountLockedMail::class, function ($mail) use ($targetUser) {
        return $mail->hasTo('superadmin@kukar.go.id')
            && $mail->user->id === $targetUser->id
            && $mail->attempts >= 5;
    });
});

test('fitur penguncian akun tidak aktif pada paket Silver', function () {
    config(['packages.default' => 'silver']);

    expect(Package::has('security_hardening'))->toBeFalse();

    $targetUser = User::factory()->create([
        'email' => 'staff_silver@kukar.go.id',
        'failed_login_attempts' => 0,
        'locked_until' => null,
    ]);

    $service = app(AccountLockoutService::class);

    // Gagal berulang tidak mengunci akun pada paket Silver
    for ($i = 1; $i <= 10; $i++) {
        $service->recordFailedAttempt('staff_silver@kukar.go.id', '192.168.1.50');
    }

    $targetUser->refresh();
    expect($targetUser->isLocked())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| 2. UJI DAFTAR BLOKIR IP & PENCATATAN AKSES MENCURIGAKAN
|--------------------------------------------------------------------------
*/

test('ip yang diblokir terdeteksi oleh model IpBlocklist', function () {
    $blockedIp = '203.0.113.45';

    IpBlocklist::create([
        'ip_address' => $blockedIp,
        'reason' => 'Percobaan SQL Injection berulang',
        'blocked_until' => now()->addDay(),
        'is_automatic' => false,
    ]);

    expect(IpBlocklist::isBlocked($blockedIp))->toBeTrue();
    expect(IpBlocklist::isBlocked('127.0.0.1'))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| 3. UJI PEMANTAU DEFACEMENT (CHECKSUM SHA-256)
|--------------------------------------------------------------------------
*/

test('pemantau defacement dapat membuat baseline dan memeriksa integritas berkas', function () {
    $monitor = app(IntegrityMonitorService::class);

    expect($monitor->isEnabled())->toBeTrue();

    // 1. Inisialisasi baseline
    $baselineCheck = $monitor->createBaseline();
    expect($baselineCheck)->not()->toBeNull()
        ->and($baselineCheck->status)->toBe(IntegrityCheck::STATUS_BASELINE)
        ->and($baselineCheck->files_checked)->toBeGreaterThan(0);

    // 2. Pemeriksaan saat tidak ada berkas yang berubah harus bersih (CLEAN)
    $cleanCheck = $monitor->check(notify: false);
    expect($cleanCheck->status)->toBe(IntegrityCheck::STATUS_CLEAN)
        ->and($cleanCheck->totalChanges())->toBe(0);
});

test('pemantau defacement mendeteksi perubahan berkas dan mengirim email peringatan', function () {
    Mail::fake();
    config(['security.integrity.alert_email' => 'superadmin@kukar.go.id']);

    $monitor = app(IntegrityMonitorService::class);

    // Simulasikan insiden defacement dengan berkas yang berubah
    $incidentCheck = IntegrityCheck::create([
        'status' => IntegrityCheck::STATUS_MODIFIED,
        'checked_at' => now(),
        'files_checked' => 10,
        'modified_files' => ['public/index.php'],
        'missing_files' => [],
        'new_files' => ['public/malicious.php'],
    ]);

    $monitor->sendAlert($incidentCheck);

    Mail::assertSent(DefacementAlertMail::class, function ($mail) {
        return $mail->check->totalChanges() === 2;
    });
});

/*
|--------------------------------------------------------------------------
| 4. UJI LAPORAN BULANAN DILENGKAPI DATA KEAMANAN
|--------------------------------------------------------------------------
*/

test('laporan bulanan memuat data ringkasan keamanan pada paket Gold dan Platinum', function () {
    $year = (int) date('Y');
    $month = (int) date('n');

    $reportService = app(MonthlyReportService::class);
    $data = $reportService->getReportData($year, $month);

    expect($data)->toHaveKey('security')
        ->and($data['security'])->not()->toBeNull()
        ->and($data['security'])->toHaveKeys([
            'failed_logins',
            'top_failed_ips',
            'accounts_locked',
            'currently_locked',
            'ip_blocked_new',
            'ip_blocked_active',
            'integrity_checks',
            'integrity_incidents',
            'backup_frequency',
        ]);
});

test('seksi keamanan laporan bulanan bernilai null pada paket Silver', function () {
    config(['packages.default' => 'silver']);

    $year = (int) date('Y');
    $month = (int) date('n');

    $reportService = app(MonthlyReportService::class);
    $data = $reportService->getReportData($year, $month);

    expect($data['security'])->toBeNull();
});

/*
|--------------------------------------------------------------------------
| 5. UJI FREKUENSI BACKUP BERDASARKAN PAKET
|--------------------------------------------------------------------------
*/

test('frekuensi backup tepat sesuai ketentuan paket: gold mingguan dan platinum harian', function () {
    config(['packages.default' => 'silver']);
    expect(Package::limit('backup_frequency'))->toBe('monthly');

    config(['packages.default' => 'gold']);
    expect(Package::limit('backup_frequency'))->toBe('weekly');

    config(['packages.default' => 'platinum']);
    expect(Package::limit('backup_frequency'))->toBe('daily');
});
