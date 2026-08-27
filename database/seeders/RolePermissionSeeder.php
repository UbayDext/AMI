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
        $auditor = Role::firstOrCreate(['name' => 'auditor']);
        $auditee = Role::firstOrCreate(['name' => 'auditee']);

        $admin->syncPermissions([
            'manage users',
            'manage questions',
            'manage assessments',
            'manage preparations',
            'view dashboard',
        ]);
        $auditor->syncPermissions(['fill assessment', 'view dashboard']);
        $auditee->syncPermissions(['view dashboard']);
    }
}
