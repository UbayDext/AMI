<?php

namespace Database\Seeders;

use App\Models\Standard;
use Illuminate\Database\Seeder;

class StandardSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'ST1', 'name' => 'Standar Kompetensi Lulusan'],
            ['code' => 'ST2', 'name' => 'Standar Isi Pembelajaran'],
            ['code' => 'ST3', 'name' => 'Standar Proses Pembelajaran'],
            ['code' => 'ST4', 'name' => 'Standar Penilaian Pembelajaran'],
            ['code' => 'ST5', 'name' => 'Standar Dosen & Tenaga Kependidikan'],
            ['code' => 'ST6', 'name' => 'Standar Sarana & Prasarana'],
            ['code' => 'ST7', 'name' => 'Standar Pengelolaan Pembelajaran'],
            ['code' => 'ST8', 'name' => 'Standar Pembiayaan Pembelajaran'],
            ['code' => 'ST9', 'name' => 'Standar Penelitian'],
            // lanjutkan sesuai Excel kamu...
        ];

        foreach ($rows as $r) {
            Standard::updateOrCreate(['code' => $r['code']], ['name' => $r['name']]);
        }
    }
}
