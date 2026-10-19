<?php

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

test('site settings singleton initializes with government defaults', function () {
    $settings = SiteSetting::current();

    expect($settings)->not()->toBeNull()
        ->and($settings->institution_name)->toBe('Pemerintah Kecamatan Loa Kulu')
        ->and($settings->primary_color)->toBe('#14304F')
        ->and($settings->secondary_color)->toBe('#C9A227');
});

test('site settings update logs activity automatically', function () {
    $settings = SiteSetting::current();

    $settings->update([
        'institution_name' => 'Kecamatan Loa Kulu Baru',
        'primary_color' => '#14304F',
    ]);

    expect($settings->fresh()->institution_name)->toBe('Kecamatan Loa Kulu Baru');

    $latestActivity = Activity::where('log_name', 'site_settings')->latest()->first();
    expect($latestActivity)->not()->toBeNull();
});

test('super admin and admin konten are authorized to manage site settings', function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $adminKonten = User::factory()->create();
    $adminKonten->assignRole('Admin Konten');

    $petugasPpid = User::factory()->create();
    $petugasPpid->assignRole('Petugas PPID');

    $settings = SiteSetting::current();

    expect(Gate::forUser($superAdmin)->allows('update', $settings))->toBeTrue();
    expect(Gate::forUser($adminKonten)->allows('update', $settings))->toBeTrue();
    expect(Gate::forUser($petugasPpid)->allows('update', $settings))->toBeFalse();
});
