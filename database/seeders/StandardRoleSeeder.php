<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Standard;
use Spatie\Permission\Models\Role;

class StandardRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standards = Standard::all();

        foreach ($standards as $standard) {
            Role::firstOrCreate(['name' => $standard->code]);
        }
    }
}
