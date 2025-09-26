<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            [
                'guard_name' => 'web',
                'status' => 'active',
                'can_delete' => 'N',
            ]
        );

        // Assign all permissions to Super Admin
        $allPermissions = Permission::pluck('name')->toArray();
        $superAdmin->syncPermissions($allPermissions);
    }
}