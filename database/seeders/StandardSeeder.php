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
            ['code' => 'ST10', 'name' => 'Standar Pelaksanaan Ujia'],
            ['code' => 'ST11', 'name' => 'Standar Hasil Penelitian'],
            ['code' => 'ST12', 'name' => 'Standar Isi Penelitian'],
            ['code' => 'ST13', 'name' => 'Standar Proses Penelitian'],
            ['code' => 'ST14', 'name' => 'Standar Penilaian Penelitian'],
            ['code' => 'ST15', 'name' => 'Standar Peneliti Penelitian'],
            ['code' => 'ST16', 'name' => 'Standar Sarana Dan Prasarana Penelitian'],
            ['code' => 'ST17', 'name' => 'Standar Pengelolaan Penelitian'],
            ['code' => 'ST18', 'name' => 'Standar Pendanaan Dan Pembiayaan Penelitian'],
            ['code' => 'ST19', 'name' => 'Standar Hasil Pengabdian Kepada Masyarakat'],
            ['code' => 'ST20', 'name' => 'Standar Isi Pengabdian Kepada Masyarakat'],
            ['code' => 'ST21', 'name' => 'Standar Proses Pengabdian Kepada Masyarakat'],
            ['code' => 'ST22', 'name' => 'Standar Penilaian Pengabdian Kepada Masyarakat'],
            ['code' => 'ST23', 'name' => 'Standar Pelaksana Pengabdian Kepada Masyarakat'],
            ['code' => 'ST24', 'name' => 'Standar Sarana dan Prasarana Pengabdian Kepada Masyarakat'],
            ['code' => 'ST25', 'name' => 'Standar Pengelolaan Pengabdian Kepada Masyarakat'],
            ['code' => 'ST26', 'name' => 'Standar Pendanaan Dan Pembiyaan Pengabdian Kepada Masyarakat'],
            ['code' => 'ST27', 'name' => 'Standar Penerimaan Mahasiswa Baru'],
            ['code' => 'ST28', 'name' => 'Standar Lembaga Kemahasiswaan'],
            ['code' => 'ST29', 'name' => 'Standar Sumber Daya Manusia Kemahasiswaan'],
            ['code' => 'ST30', 'name' => 'Standar Sarana Dan Prasarana Kegiatan Kemahasiswaan'],
            ['code' => 'ST31', 'name' => 'Standar Pembiyaan Kegiatan Mahasiswa'],
            ['code' => 'ST32', 'name' => 'Penghargaan Dan Prestasi Mahasiswa'],
            ['code' => 'ST33', 'name' => 'Standar Kebijakan Kerja Sama'],
            ['code' => 'ST34', 'name' => 'Standar Lembaga Kerja Sama'],
            ['code' => 'ST35', 'name' => 'Sumber Daya Manusia Untuk Kerja Sama'],
            ['code' => 'ST36', 'name' => 'Standar Prosedur Pelaksanaan Kegiatan Kerja Sama'],
            ['code' => 'ST37', 'name' => 'Standar Pelaksanaan Kegiatan Kerja Sama'],
            ['code' => 'ST38', 'name' => 'Standar Kebijakan Tata Pamong Dan Tata Kelola'],
            ['code' => 'ST39', 'name' => 'Standar Pelaksanaan Tata Pamong Dan Tata Kelola'],
            ['code' => 'ST40', 'name' => 'Standar Tata Pamong Dan Tata Kelola Pelaksana Administrasi Dan Unit Teknis'],
            ['code' => 'ST41', 'name' => 'Standar Tupoksi Dan Jobdesk Tata Pamong Dan Tata Kelola STIT Hidayatunnajah'],
        ];

        foreach ($rows as $r) {
            Standard::updateOrCreate(['code' => $r['code']], ['name' => $r['name']]);
        }
    }
}
