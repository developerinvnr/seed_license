<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Role Permissions
            ['name' => 'add-role', 'group_name' => 'Role', 'module' => 'Common'],
            ['name' => 'edit-role', 'group_name' => 'Role', 'module' => 'Common'],
            ['name' => 'delete-role', 'group_name' => 'Role', 'module' => 'Common'],
            ['name' => 'view-role', 'group_name' => 'Role', 'module' => 'Common'],
            ['name' => 'view-permission', 'group_name' => 'Permission', 'module' => 'Common'],

            //License Type
            ['name' => 'add-License Type', 'group_name' => 'License Type', 'module' => 'Common'],
            ['name' => 'edit-License Type', 'group_name' => 'License Type', 'module' => 'Common'],
            ['name' => 'delete-License Type', 'group_name' => 'License Type', 'module' => 'Common'],
            ['name' => 'view-License Type', 'group_name' => 'License Type', 'module' => 'Common'],

            //License Name
            ['name' => 'add-License Name', 'group_name' => 'License Name', 'module' => 'Common'],
            ['name' => 'edit-License Name', 'group_name' => 'License Name', 'module' => 'Common'],
            ['name' => 'delete-License Name', 'group_name' => 'License Name', 'module' => 'Common'],
            ['name' => 'view-License Name', 'group_name' => 'License Name', 'module' => 'Common'],

            //company Responsible Person
            ['name' => 'add-company Responsible Person', 'group_name' => 'company Responsible Person', 'module' => 'Common'],
            ['name' => 'edit-company Responsible Person', 'group_name' => 'company Responsible Person', 'module' => 'Common'],
            ['name' => 'delete-company Responsible Person', 'group_name' => 'company Responsible Person', 'module' => 'Common'],
            ['name' => 'view-company Responsible Person', 'group_name' => 'company Responsible Person', 'module' => 'Common'],

            //company master
            ['name' => 'add-Company', 'group_name' => 'Company', 'module' => 'Common'],
            ['name' => 'edit-Company', 'group_name' => 'Company', 'module' => 'Common'],
            ['name' => 'delete-Company', 'group_name' => 'Company', 'module' => 'Common'],
            ['name' => 'view-Company', 'group_name' => 'Company', 'module' => 'Common'],

            //Core API Data 
            ['name' => 'add-Core API Data', 'group_name' => 'Core API Data', 'module' => 'Common'],
            ['name' => 'edit-Core API Data', 'group_name' => 'Core API Data', 'module' => 'Common'],
            // ['name' => 'delete-Core API Data', 'group_name' => 'Core API Data', 'module' => 'Common'],
            ['name' => 'view-Core API Data', 'group_name' => 'Core API Data', 'module' => 'Common'],

            //License Lable
            ['name' => 'add-License Label', 'group_name' => 'License Label', 'module' => 'Common'],
            ['name' => 'edit-License Label', 'group_name' => 'License Label', 'module' => 'Common'],
            ['name' => 'delete-License Label', 'group_name' => 'License Label', 'module' => 'Common'],
            ['name' => 'view-License Label', 'group_name' => 'License Label', 'module' => 'Common'],

            //Lable sub field
            ['name' => 'add-Lable sub field', 'group_name' => 'Lable sub field', 'module' => 'Common'],
            ['name' => 'edit-Lable sub field', 'group_name' => 'Lable sub field', 'module' => 'Common'],
            ['name' => 'delete-Lable sub field', 'group_name' => 'Lable sub field', 'module' => 'Common'],
            ['name' => 'view-Lable sub field', 'group_name' => 'Lable sub field', 'module' => 'Common'],

            //user
            ['name' => 'add-user', 'group_name' => 'user', 'module' => 'Common'],
            ['name' => 'edit-user', 'group_name' => 'user', 'module' => 'Common'],
            ['name' => 'delete-user', 'group_name' => 'user', 'module' => 'Common'],
            ['name' => 'view-user', 'group_name' => 'user', 'module' => 'Common'],

            //Add License
            ['name' => 'add-Add License', 'group_name' => 'Add License', 'module' => 'Common'],
            ['name' => 'edit-Add License', 'group_name' => 'Add License', 'module' => 'Common'],
            ['name' => 'delete-Add License', 'group_name' => 'Add License', 'module' => 'Common'],
            ['name' => 'view-Add License', 'group_name' => 'Add License', 'module' => 'Common'],

            // Dashboard
            ['name' => 'view-dashboard', 'group_name' => 'Dashboard', 'module' => 'Common'],
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'group_name' => $permission['group_name'],
                    'module' => $permission['module']
                ]
            );
        }
    }
}
