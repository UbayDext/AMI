<?php

namespace Database\Seeders;

use App\Models\AccreditationYear;
use Illuminate\Database\Seeder;

class AccreditationYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [2023, 2024, 2025];
        foreach ($years as $y) {
            AccreditationYear::updateOrCreate(['year' => $y]);
        }
    }
}
