<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على دور المشرف
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // منح جميع الصلاحيات لدور المشرف
            $permissions = Permission::all();
            $adminRole->syncPermissions($permissions);
        }
    }
}
