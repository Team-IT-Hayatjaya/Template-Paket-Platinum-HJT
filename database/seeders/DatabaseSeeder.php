<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            KategoriInformasiSeeder::class,
            StatusPermohonanSeeder::class,
            PengaturanPpidSeeder::class,
            HalamanSeeder::class,
            NavigationMenuSeeder::class,
            LayananSeeder::class,
            StatistikSeeder::class,
        ]);
    }
}
