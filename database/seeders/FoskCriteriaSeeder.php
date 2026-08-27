<?php

namespace Database\Seeders;

use App\Models\FoskCriteria;
use Illuminate\Database\Seeder;

class FoskCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            ['code' => 'K1', 'name' => 'Tata Pamong, Tata Kelola, dan Kerjasama',        'sort_order' => 1],
            ['code' => 'K2', 'name' => 'Mahasiswa',                                        'sort_order' => 2],
            ['code' => 'K3', 'name' => 'Sumber Daya Manusia',                              'sort_order' => 3],
            ['code' => 'K4', 'name' => 'Keuangan, Sarana, dan Prasarana',                  'sort_order' => 4],
            ['code' => 'K5', 'name' => 'Pendidikan',                                       'sort_order' => 5],
            ['code' => 'K6', 'name' => 'Penelitian',                                       'sort_order' => 6],
            ['code' => 'K7', 'name' => 'Pengabdian kepada Masyarakat',                     'sort_order' => 7],
            ['code' => 'K8', 'name' => 'Luaran dan Capaian Tridharma',                     'sort_order' => 8],
            ['code' => 'K9', 'name' => 'Tata Pamong, Tata Kelola, dan Kerjasama Institusi', 'sort_order' => 9],
        ];

        foreach ($criteria as $c) {
            FoskCriteria::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
