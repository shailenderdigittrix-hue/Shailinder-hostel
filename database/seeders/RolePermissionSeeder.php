<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🔑 Define permissions
        $permissions = [
            // Admin permissions
            'manage users',
            'manage roles',
            'view attendance',
            'manage disciplinary actions',
            'approve mess allotments',
            'configure holidays',
            'generate reports',

            // Warden permissions
            'manage room allotments',
            'view occupancy reports',
            'submit disciplinary actions',

            // Mess Manager permissions
            'manage mess allotments',
            'generate mess bills',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 🧑‍💼 Admin Role — Give all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all()); // ✅ This ensures all new permissions are applied

        // 🧑‍🏫 Hostel Warden Role
        $wardenRole = Role::firstOrCreate(['name' => 'Hostel Warden']);
        $wardenPermissions = [
            'manage room allotments',
            'view occupancy reports',
            'view attendance',
            'submit disciplinary actions',
        ];
        $wardenRole->syncPermissions($wardenPermissions);

        // 🍽️ Mess Manager Role
        $messRole = Role::firstOrCreate(['name' => 'Mess Manager']);
        $messPermissions = [
            'manage mess allotments',
            'generate mess bills',
        ];
        $messRole->syncPermissions($messPermissions);
    }
}
