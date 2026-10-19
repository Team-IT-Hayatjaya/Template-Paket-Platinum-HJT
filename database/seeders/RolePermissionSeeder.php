<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'manage_settings',
            'manage_content',
            'view_ppid',
            'process_ppid',
            'review_ppid_objection',
            'manage_users',
            'view_activity_logs',
            'run_backups',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminKonten = Role::firstOrCreate(['name' => 'Admin Konten', 'guard_name' => 'web']);
        $adminUnit = Role::firstOrCreate(['name' => 'Admin Unit', 'guard_name' => 'web']);
        $petugasPpid = Role::firstOrCreate(['name' => 'Petugas PPID', 'guard_name' => 'web']);
        $atasanPpid = Role::firstOrCreate(['name' => 'Atasan PPID', 'guard_name' => 'web']);

        // Give permissions
        $superAdmin->syncPermissions(Permission::all());

        $adminKonten->syncPermissions([
            'manage_settings',
            'manage_content',
        ]);

        $adminUnit->syncPermissions([
            'manage_content',
        ]);

        $petugasPpid->syncPermissions([
            'view_ppid',
            'process_ppid',
        ]);

        $atasanPpid->syncPermissions([
            'view_ppid',
            'process_ppid',
            'review_ppid_objection',
        ]);
    }
}
