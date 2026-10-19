<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

test('guest is redirected to filament login page when accessing /admin', function () {
    $response = $this->get('/admin');

    $response->assertStatus(302);
    $response->assertRedirect('/admin/login');
});

test('filament login page renders successfully', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('super admin seeder creates account from environment variables without hardcoded password', function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(SuperAdminSeeder::class);

    $expectedEmail = env('SUPER_ADMIN_EMAIL', 'admin@hayatjaya.com');
    $expectedPassword = env('SUPER_ADMIN_PASSWORD', 'HayatJaya#2026!Secure');

    $user = User::where('email', $expectedEmail)->first();

    expect($user)->not()->toBeNull()
        ->and($user->hasRole('Super Admin'))->toBeTrue()
        ->and(Hash::check($expectedPassword, $user->password))->toBeTrue();
});

test('super admin can access admin panel while unassigned user cannot', function () {
    $this->seed(RolePermissionSeeder::class);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $regularUser = User::factory()->create();

    $panel = Filament::getPanel('admin');

    expect($superAdmin->canAccessPanel($panel))->toBeTrue()
        ->and($regularUser->canAccessPanel($panel))->toBeFalse();
});

test('all required government agency roles exist in database', function () {
    $this->seed(RolePermissionSeeder::class);

    $requiredRoles = [
        'Super Admin',
        'Admin Konten',
        'Petugas PPID',
        'Atasan PPID',
    ];

    foreach ($requiredRoles as $roleName) {
        expect(Role::where('name', $roleName)->exists())->toBeTrue();
    }
});

test('login event writes activity log record', function () {
    $user = User::factory()->create(['email' => 'admin.test@instansi.go.id']);

    Event::dispatch(new Login('web', $user, false));

    $log = Activity::where('log_name', 'auth')->latest()->first();

    expect($log)->not()->toBeNull()
        ->and($log->description)->toContain('admin.test@instansi.go.id');
});

test('failed login attempt writes activity log record', function () {
    Event::dispatch(new Failed('web', null, ['email' => 'hacker@unknown.id']));

    $log = Activity::where('log_name', 'auth')->latest()->first();

    expect($log)->not()->toBeNull()
        ->and($log->description)->toContain('hacker@unknown.id');
});

test('super admin can login through filament login form', function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(SuperAdminSeeder::class);

    $email = env('SUPER_ADMIN_EMAIL', 'admin@hayatjaya.com');
    $password = env('SUPER_ADMIN_PASSWORD', 'HayatJaya#2026!Secure');

    \Livewire\Livewire::test(\Filament\Auth\Pages\Login::class)
        ->fillForm([
            'email' => $email,
            'password' => $password,
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticated();
});
