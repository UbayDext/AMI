<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'manage users',
            'manage questions',
            'manage assessments',
            'manage preparations',
            'view dashboard',
            'fill assessment',
        ];

        foreach ($perms as $p) Permission::firstOrCreate(['name' => $p]);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $asesor = Role::firstOrCreate(['name' => 'asesor']);
        $standar = Role::firstOrCreate(['name' => 'standar']);

        $admin->syncPermissions([
            'manage users',
            'manage questions',
            'manage assessments',
            'manage preparations',
            'view dashboard',
        ]);
        $asesor->syncPermissions(['fill assessment', 'view dashboard']);
        $standar->syncPermissions(['view dashboard']);
    }
}
