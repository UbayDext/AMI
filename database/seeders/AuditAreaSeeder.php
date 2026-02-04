<?php

namespace Database\Seeders;

use App\Models\AuditArea;
use Illuminate\Database\Seeder;

class AuditAreaSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Bidang Adm. Umum, SDM, Perencanaan & Keuangan', 'code' => 'WK2'],
            ['name' => 'Bidang Akademik & Kelembagaan', 'code' => 'WK1'],
            ['name' => 'Bidang Kemahasiswaan dan Kerja Sama', 'code' => 'WK3'],
            ['name' => 'Prodi PGMI', 'code' => 'PGMI'],
            ['name' => 'Prodi PBA', 'code' => 'PBA'],
            ['name' => 'Bagian Administrasi Akademik dan Kemahasiswaan (BAAK)', 'code' => 'BAAK'],
            ['name' => 'Pusat Penelitian, Publikasi & Pengabdian kepada Masyarakat (P4M)', 'code' => 'P4M'],
            ['name' => 'Sistem Penjaminan Mutu Internal (SPMI)', 'code' => 'SPMI'],
            ['name' => 'UPT Perpustakaan', 'code' => 'PUS'],
            ['name' => 'UPT Humas, CDC-TS', 'code' => 'HCDC'],
            ['name' => 'UPT Bahasa & Asrama', 'code' => 'BAS'],
            ['name' => 'Satuan Pengawas Internal', 'code' => 'SPI'],
            ['name' => 'Bag. Sarana & Prasarana', 'code' => 'SRP'],
            ['name' => 'Bag. Keuangan', 'code' => 'KEU'],
        ];

        foreach ($rows as $r) {
            AuditArea::updateOrCreate(
                ['name' => $r['name']],
                ['code' => $r['code']]
            );
        }
    }
}
