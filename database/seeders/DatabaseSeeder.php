<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            AuditAreaSeeder::class,
            StandardSeeder::class,
            AccreditationYearSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
