<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = env('SUPER_ADMIN_NAME', 'Super Admin HJT');
        $email = env('SUPER_ADMIN_EMAIL', 'admin@hayatjaya.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'HayatJaya#2026!Secure');

        // Ensure Super Admin role exists
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['Super Admin']);
    }
}
