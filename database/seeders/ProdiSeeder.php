<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = [
            ['code' => 'PBA',  'name' => 'Pendidikan Bahasa Arab',          'jenjang' => 'S1'],
            ['code' => 'PGMI', 'name' => 'Pendidikan Guru Madrasah Ibtidaiyah', 'jenjang' => 'S1'],
        ];

        foreach ($prodis as $data) {
            Prodi::updateOrCreate(['code' => $data['code']], $data + ['is_active' => true]);
        }
    }
}
