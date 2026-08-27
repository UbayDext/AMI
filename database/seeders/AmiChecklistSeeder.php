<?php

namespace Database\Seeders;

use App\Models\AmiChecklistQuestion;
use Illuminate\Database\Seeder;

class AmiChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->getData();

        foreach ($data as $st) {
            foreach ($st['questions'] as $i => $text) {
                AmiChecklistQuestion::updateOrCreate([
                    'standard_code' => $st['code'],
                    'question_number' => $i + 1,
                ], [
                    'standard_name' => $st['name'],
                    'bidang' => $st['bidang'],
                    'auditi' => $st['auditi'],
                    'question_text' => $text,
                ]);
            }
        }
    }

    private function getData(): array
    {
        return [
            /* ------------------------------------------------------------------ */
            /* ST01 — Standar Kompetensi Lulusan */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST01',
                'name' => 'Standar Kompetensi Lulusan',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Dosen',
                'questions' => [
                    'Apakah Program Studi memiliki rumusan Capaian Pembelajaran Lulusan yang mencakup unsur sikap, pengetahuan, keterampilan umum, dan keterampilan khusus sesuai Lampiran Permenristek Dikti No.44 Tahun 2015?',
                    'Apakah rumusan Capaian Pembelajaran Lulusan diturunkan dari profil lulusan dan mengacu pada hasil kesepakatan asosiasi/profesi?',
                    'Apakah rumusan Capaian Pembelajaran Lulusan telah mengacu pada deskripsi capaian pembelajaran lulusan KKNI sesuai jenjang kualifikasi?',
                    'Apakah rumusan sikap pada Capaian Pembelajaran Lulusan memuat tambahan unsur akhlak mulia, keshalehan spiritual, bermuamalah sesuai syari\'at Islam, dan pengembangan Iptek dan Imtaq?',
                    'Apakah Program Studi memiliki rumusan keterampilan khusus sesuai bidang keilmuan program studi?',
                    'Apakah rumusan keterampilan umum pada Capaian Pembelajaran Lulusan memuat kemampuan baca tulis Al-Qur\'an, ibadah sesuai tuntunan Al-Qur\'an dan Hadits, imam/khatib/pendakwah, pemulasaraan jenazah, dan penerapan nilai-nilai Islam?',
                    'Apakah skripsi atau laporan tugas akhir mahasiswa telah diunggah pada repositori perguruan tinggi yang terintegrasi dengan portal Repositori Tugas Akhir Mahasiswa Kemenristekdikti?',
                    'Apakah terdapat bukti penetapan rumusan pengetahuan dan keterampilan khusus yang mengacu pada forum program studi sejenis atau pengelola program studi?',
                    'Apakah Program Studi memiliki analisis pemenuhan capaian pembelajaran lulusan yang mencakup keserbacukupan, kedalaman, dan kebermanfaatan dalam 3 tahun terakhir?',
                    'Apakah rata-rata IPK lulusan program sarjana mencapai ≥3.0?',
                    'Apakah rata-rata masa studi lulusan program sarjana adalah 4 tahun?',
                    'Apakah persentase kelulusan tepat waktu mencapai ≥75%?',
                    'Apakah persentase keberhasilan program studi mencapai ≥85%?',
                    'Apakah waktu tunggu lulusan untuk bekerja/berwirausaha yang relevan dengan bidang studi ≤6 bulan?',
                    'Apakah kesesuaian bidang kerja lulusan terhadap kompetensi bidang studi mencapai ≥80%?',
                    'Apakah kesesuaian bidang kerja lulusan dengan bidang studi mencapai ≥60%?',
                    'Apakah tingkat kepuasan pengguna lulusan mencapai ≥3.25?',
                    'Apakah persentase lulusan yang bekerja di tingkat internasional/multinasional mencapai ≥1% dan tingkat nasional/berwirausaha berizin mencapai ≥20%?',
                    'Apakah persentase lulusan mahasiswa yang memperoleh nilai A pada Pendidikan Aqidah Ahlu As-Sunnah Wa Al-Jama\'ah mencapai ≥75%?',
                    'Apakah persentase lulusan mahasiswa yang memperoleh nilai A pada Tahfizh Al-Qur\'an mencapai ≥75%?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST02 — Standar Isi Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST02',
                'name' => 'Standar Isi Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ka. SPMI, Ka. UPT Bahasa & Asrama, Ka. UPT Perpustakaan',
                'questions' => [
                    'Apakah tersedia kebijakan pengembangan kurikulum yang mempertimbangkan keterkaitan dengan visi dan misi (mandat) perguruan tinggi?',
                    'Apakah kebijakan pengembangan kurikulum mempertimbangkan pengembangan ilmu pengetahuan dan kebutuhan stakeholders?',
                    'Apakah kebijakan pengembangan kurikulum mempertimbangkan perubahan di masa depan?',
                    'Apakah tersedia pedoman pengembangan kurikulum yang memuat profil lulusan?',
                    'Apakah pedoman pengembangan kurikulum memuat capaian pembelajaran lulusan (CPL)?',
                    'Apakah pedoman pengembangan kurikulum memuat bahan kajian?',
                    'Apakah pedoman pengembangan kurikulum memuat struktur kurikulum?',
                    'Apakah pedoman pengembangan kurikulum memuat Rencana Pembelajaran Semester (RPS)?',
                    'Apakah profil lulusan, CPL, bahan kajian, struktur kurikulum dan RPS mengacu kepada KKNI dan peraturan-peraturan terkini?',
                    'Apakah kurikulum memiliki kepekaan terhadap isu-isu terkini seperti pendidikan karakter 3M, bahaya radikalisme dan terorisme, NAPZA, dan pendidikan anti korupsi?',
                    'Apakah tersedia mekanisme penetapan kurikulum dalam pedoman pengembangan kurikulum?',
                    'Apakah mekanisme penetapan kurikulum dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman implementasi kurikulum yang mencakup perencanaan kurikulum?',
                    'Apakah pedoman implementasi kurikulum mencakup pelaksanaan kurikulum?',
                    'Apakah pedoman implementasi kurikulum mencakup pemantauan kurikulum?',
                    'Apakah pedoman implementasi kurikulum mencakup peninjauan kurikulum?',
                    'Apakah implementasi kurikulum menginternalisasikan nilai-nilai Islam Ahlu Sunnah Wal Jamaah?',
                    'Apakah implementasi kurikulum mempertimbangkan isu-isu strategis dan masukan dari stakeholders?',
                    'Apakah evaluasi dan pemutakhiran kurikulum dilakukan secara berkala setiap 2 sd 5 tahun?',
                    'Apakah evaluasi dan pemutakhiran kurikulum melibatkan pemangku kepentingan internal dan eksternal?',
                    'Apakah kurikulum direview oleh pakar bidang ilmu program studi dan asosiasi?',
                    'Apakah evaluasi dan pemutakhiran kurikulum mempertimbangkan perkembangan IPTEKS dan kebutuhan pengguna?',
                    'Apakah lulusan program sarjana paling sedikit menguasai konsep teoretis bidang pengetahuan dan keterampilan tertentu secara umum dan konsep teoretis bagian khusus secara mendalam sesuai CPL prodi?',
                    'Apakah tersedia peta kurikulum yang menunjukkan hubungan antara mata kuliah dan capaian pembelajaran?',
                    'Apakah setiap mata kuliah terpetakan secara jelas terhadap capaian pembelajaran yang didukung?',
                    'Apakah kurikulum dikembangkan dengan mengintegrasikan pendidikan, penelitian, dan pengabdian kepada masyarakat?',
                    'Apakah struktur kurikulum sesuai dengan urutan capaian pembelajaran yang ditetapkan?',
                    'Apakah struktur kurikulum mendukung daya saing Internasional?',
                    'Apakah kurikulum memberikan fleksibilitas untuk memfasilitasi keberagaman minat dan bakat melalui Mata Kuliah Pilihan?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Fiqih?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Tajwid?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Tauhid?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Ulum Al-Qur\'an?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Aqidah Ahlu As-Sunnah Wa Al-Jama\'ah?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Ulum Al-Hadits?',
                    'Apakah kurikulum memuat mata kuliah Pendidikan Tazkiyah An-Nufus?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST03 — Standar Proses Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST03',
                'name' => 'Standar Proses Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ka. UPT Perpustakaan, Ka. UPT Bahasa & Asrama, Ka. SPMI',
                'questions' => [
                    'Apakah tersedia pedoman penugasan dosen yang mengatur kesesuaian kebutuhan mata kuliah dengan kualifikasi, keahlian, dan pengalaman dosen?',
                    'Apakah tersedia pedoman penetapan strategi, metode, media, dan penilaian pembelajaran?',
                    'Apakah monitoring dan evaluasi mutu proses pembelajaran dilaksanakan, terdokumentasi, dan ditindaklanjuti secara berkelanjutan?',
                    'Apakah tersedia kebijakan dan pedoman integrasi penelitian dan PkM ke dalam pembelajaran?',
                    'Apakah tersedia pedoman pelaksanaan, evaluasi, pengendalian, dan peningkatan kualitas integrasi penelitian dan PkM ke dalam pembelajaran?',
                    'Apakah hasil monitoring dan evaluasi integrasi penelitian dan PkM terhadap pembelajaran terdokumentasi dan ditindaklanjuti?',
                    'Apakah pembelajaran telah menerapkan Student Centered Learning (SCL) sesuai karakteristik proses pembelajaran?',
                    'Apakah seluruh mata kuliah memiliki RPS yang ditetapkan dan dapat diakses mahasiswa?',
                    'Apakah komponen RPS telah memuat seluruh unsur yang dipersyaratkan?',
                    'Apakah peninjauan RPS dilakukan minimal 2 kali setiap tahun untuk minimal 75% mata kuliah?',
                    'Apakah metode pembelajaran yang diterapkan sesuai dengan capaian pembelajaran pada minimal 75% mata kuliah?',
                    'Apakah materi pembelajaran yang disampaikan sesuai dengan RPS pada minimal 80% mata kuliah?',
                    'Apakah tersedia kebijakan suasana akademik yang mencakup otonomi keilmuan, kebebasan akademik, dan kebebasan mimbar akademik?',
                    'Apakah survei kepuasan suasana akademik dilaksanakan setiap tahun dan ditindaklanjuti?',
                    'Apakah terdapat analisis dan perencanaan strategis pengembangan suasana akademik beserta implementasinya?',
                    'Apakah tersedia kebijakan, pedoman, dan prosedur pelaksanaan Blended Learning?',
                    'Apakah bentuk-bentuk pembelajaran dilaksanakan dan terdokumentasi?',
                    'Apakah persentase jam pembelajaran praktikum/praktik/praktik lapangan mencapai ≥ 20%?',
                    'Apakah program sarjana melaksanakan bentuk pembelajaran berupa penelitian, perancangan, atau pengembangan?',
                    'Apakah program sarjana melaksanakan bentuk pembelajaran berupa pengabdian kepada masyarakat?',
                    'Apakah pembelajaran berbasis penelitian dan pengabdian kepada masyarakat mengacu pada standar penelitian dan pengabdian kepada masyarakat?',
                    'Apakah tersedia kebijakan dan pedoman pelaksanaan pembelajaran di dalam dan di luar Prodi?',
                    'Apakah tersedia dokumen kerjasama dan mekanisme transfer SKS untuk pembelajaran di luar Prodi?',
                    'Apakah tersedia SK pembimbingan untuk pembelajaran di luar Program Studi?',
                    'Apakah pembelajaran di luar Program Studi hanya diterapkan pada program sarjana?',
                    'Apakah pengaturan semester dan SKS pembelajaran di luar Prodi sesuai ketentuan SN-Dikti?',
                    'Apakah tersedia pedoman masa studi dan beban belajar mahasiswa sesuai SN-Dikti?',
                    'Apakah proses pembelajaran dilaksanakan minimal 16 minggu per semester?',
                    'Apakah satu tahun akademik terdiri atas 2 semester?',
                    'Apakah masa studi dan beban belajar mahasiswa program sarjana sesuai ketentuan?',
                    'Apakah pelaksanaan 1 sks kuliah, responsi, atau tutorial sesuai ketentuan waktu pembelajaran?',
                    'Apakah pelaksanaan 1 sks seminar atau bentuk lain yang sejenis sesuai ketentuan waktu pembelajaran?',
                    'Apakah pelaksanaan 1 sks pembelajaran praktik dan sejenisnya sesuai ketentuan waktu pembelajaran?',
                    'Apakah tingkat kehadiran dosen dalam pembelajaran kelas mencapai ≥ 95%?',
                    'Apakah rata-rata kehadiran mahasiswa dalam perkuliahan mencapai ≥ 75%?',
                    'Apakah rata-rata kehadiran mahasiswa dalam praktikum mencapai ≥ 75%?',
                    'Apakah Indek Proses Pembelajaran (IPP) mencapai ≥ 3.25?',
                    'Apakah ≥ 75% mahasiswa menyatakan puas terhadap pengalaman belajar?',
                    'Apakah hasil pengukuran kepuasan mahasiswa ditindaklanjuti minimal 2 kali setiap semester?',
                    'Apakah ≥ 60% mahasiswa memiliki IPS minimum 3.00?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST04 — Standar Penilaian Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST04',
                'name' => 'Standar Penilaian Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI',
                'questions' => [
                    'Apakah tersedia pedoman penilaian proses dan hasil belajar mahasiswa yang memuat prinsip penilaian?',
                    'Apakah pedoman penilaian memuat teknik dan instrumen penilaian?',
                    'Apakah pedoman penilaian memuat mekanisme dan prosedur penilaian?',
                    'Apakah pedoman penilaian memuat pelaksanaan penilaian?',
                    'Apakah pedoman penilaian memuat pelaporan penilaian?',
                    'Apakah pedoman penilaian memuat ketentuan kelulusan mahasiswa?',
                    'Apakah dosen memberikan feedback hasil penilaian kepada mahasiswa?',
                    'Apakah penilaian dilaksanakan selama proses pembelajaran berlangsung?',
                    'Apakah standar penilaian disampaikan dan disepakati pada awal perkuliahan?',
                    'Apakah prosedur dan kriteria penilaian dilaksanakan sesuai kesepakatan awal kuliah?',
                    'Apakah prosedur dan hasil penilaian telah dimasukkan ke dalam SIAKAD?',
                    'Apakah minimal 70% mata kuliah memiliki rubrik dan/atau portofolio penilaian?',
                    'Apakah teknik penilaian sesuai dengan capaian pembelajaran lulusan pada minimal 75% mata kuliah?',
                    'Apakah instrumen penilaian sesuai dengan capaian pembelajaran lulusan pada minimal 75% mata kuliah?',
                    'Apakah dosen menyampaikan kontrak rencana penilaian pada awal perkuliahan?',
                    'Apakah pelaksanaan penilaian sesuai dengan kontrak rencana penilaian?',
                    'Apakah dosen memberikan kesempatan mahasiswa untuk mempertanyakan hasil penilaian?',
                    'Apakah dokumentasi penilaian memuat seluruh komponen penilaian secara akuntabel dan transparan?',
                    'Apakah dosen memiliki prosedur penilaian yang lengkap?',
                    'Apakah dosen melaporkan nilai akhir paling lambat 14 hari setelah UAS?',
                    'Apakah konversi nilai huruf dan angka sesuai ketentuan institusi?',
                    'Apakah terdapat tindak lanjut hasil monitoring dan evaluasi penilaian?',
                    'Apakah mahasiswa dapat mengakses hasil penilaian secara online?',
                    'Apakah lulusan memperoleh Transkrip Nilai yang memuat IPK?',
                    'Apakah predikat kelulusan "memuaskan" ditetapkan sesuai ketentuan IPK?',
                    'Apakah predikat kelulusan "sangat memuaskan" ditetapkan sesuai ketentuan IPK?',
                    'Apakah predikat kelulusan "pujian" ditetapkan sesuai ketentuan IPK?',
                    'Apakah minimal 50% lulusan memiliki IPK ≥ 3,00?',
                    'Apakah lulusan memperoleh Ijazah?',
                    'Apakah pemberian gelar sesuai program studi?',
                    'Apakah lulusan memperoleh Surat Keterangan Pendamping Ijazah (SKPI)?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST05 — Standar Dosen & Tendik */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST05',
                'name' => 'Standar Dosen & Tendik',
                'bidang' => 'WK2',
                'auditi' => 'Waket I, Waket II, Waket III, Ketua Prodi PGMI, Ka. BAAK, Dosen',
                'questions' => [
                    'Apakah seluruh dosen program sarjana memiliki kualifikasi akademik minimal S2 yang relevan dengan program studi?',
                    'Apakah persentase DTPS berpendidikan S3 telah mencapai minimal 30%?',
                    'Apakah jumlah dosen tetap dengan jabatan akademik Lektor Kepala minimal 3 orang?',
                    'Apakah persentase DTPS dengan jabatan akademik Lektor ≥ 30%?',
                    'Apakah persentase dosen tetap yang memiliki sertifikat pendidik ≥ 10%?',
                    'Apakah persentase DTPS yang memiliki sertifikat pendidik ≥ 30%?',
                    'Apakah setiap program studi memiliki minimal 5 dosen dengan keahlian sesuai disiplin ilmu?',
                    'Apakah persentase dosen tetap terhadap seluruh dosen ≥ 90%?',
                    'Apakah persentase dosen tidak tetap terhadap DTPS ≤ 10%?',
                    'Apakah rasio jumlah dosen tetap terhadap jumlah program studi ≥ 12?',
                    'Apakah jumlah DTPS yang terlibat dalam kegiatan pendidikan minimal 12 orang?',
                    'Apakah rasio jumlah mahasiswa terhadap dosen tetap berada pada rentang 20–30?',
                    'Apakah rasio jumlah mahasiswa terhadap DTPS berada pada rentang 20–30?',
                    'Apakah tersedia pedoman perhitungan beban kerja dosen yang mencakup tugas pokok, tugas tambahan, dan penunjang?',
                    'Apakah rata-rata jumlah mahasiswa bimbingan sebagai pembimbing utama ≤ 10?',
                    'Apakah EWMP DTPS berada pada rentang 12–16 sks?',
                    'Apakah Sekolah Tinggi memiliki dan melaksanakan rencana pengembangan dosen secara konsisten?',
                    'Apakah minimal 2 dosen tetap mengikuti studi lanjut S3 setiap tahun?',
                    'Apakah persentase dosen tetap yang mengikuti sertifikasi kompetensi ≥ 2%?',
                    'Apakah rata-rata rekognisi dosen tetap ≥ 0.17?',
                    'Apakah rata-rata rekognisi DTPS ≥ 0.17?',
                    'Apakah Indeks Kepuasan Kerja Dosen ≥ 3.25?',
                    'Apakah jumlah dan kualifikasi tenaga kependidikan telah memenuhi kebutuhan sesuai jenis pekerjaan?',
                    'Apakah tenaga kependidikan non-administrasi minimal D3 dan ≥50% S1?',
                    'Apakah seluruh pustakawan memiliki sertifikasi kompetensi pustakawan?',
                    'Apakah jumlah laboran memenuhi jumlah laboratorium program studi?',
                    'Apakah ≥50% tenaga administrasi memiliki kualifikasi S1?',
                    'Apakah ≥50% tenaga administrasi memiliki sertifikasi sesuai bidang tugas?',
                    'Apakah terdapat rencana pengembangan tenaga kependidikan dalam Renstra?',
                    'Apakah minimal 1 tenaga kependidikan mengikuti studi lanjut per tahun?',
                    'Apakah minimal 1 tenaga kependidikan mengikuti sertifikasi per tahun?',
                    'Apakah Indeks Kepuasan Tenaga Kependidikan ≥ 3.00?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST06 — Standar Sarana & Prasarana Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST06',
                'name' => 'Standar Sarana & Prasarana Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Waket II, Ketua Prodi PGMI, Kabag. Sarpras, Ka. SPMI',
                'questions' => [
                    'Apakah setiap ruang kelas telah dilengkapi kursi dan/atau meja mahasiswa sesuai kapasitas kelas serta meja dan kursi dosen?',
                    'Apakah setiap ruang kelas tersedia white board, proyektor, dan akses internet?',
                    'Apakah tersedia software berlisensi untuk mata kuliah yang memerlukan software tertentu?',
                    'Apakah tersedia buku, teks, referensi, eBook, dan Repository untuk setiap program studi?',
                    'Apakah tersedia jurnal ilmiah nasional terakreditasi dan jurnal internasional dalam bentuk e-journal sesuai program studi?',
                    'Apakah tersedia koleksi prosiding seminar untuk setiap program studi?',
                    'Apakah bandwidth dan jaringan internet tersedia dan dapat digunakan oleh seluruh sivitas akademika di seluruh area kampus?',
                    'Apakah layanan e-learning menggunakan Platform LMS Moodle 3 tersedia dan digunakan?',
                    'Apakah sistem informasi perpustakaan berupa e-journal, e-book, dan e-repository tersedia dan berfungsi?',
                    'Apakah Sistem Informasi Akademik tersedia dan digunakan?',
                    'Apakah Sistem Informasi Mahasiswa, Dosen dan Karyawan tersedia dan digunakan?',
                    'Apakah Sistem Informasi Penelitian dan PkM tersedia dan digunakan?',
                    'Apakah Sistem Informasi Kepuasan Layanan tersedia dan digunakan?',
                    'Apakah Sistem Informasi pengajuan anggaran tersedia dan digunakan?',
                    'Apakah Sistem Informasi pengajuan dan perawatan barang tersedia dan digunakan?',
                    'Apakah peralatan dan material eksperimen di laboratorium memadai sesuai pemanfaatannya dan memenuhi capaian pembelajaran lulusan?',
                    'Apakah tersedia peralatan olahraga dan seni yang memenuhi kebutuhan Unit Kegiatan Mahasiswa?',
                    'Apakah sarana fasilitas umum tersedia dan dapat digunakan?',
                    'Apakah sarana kebersihan tersedia memadai di seluruh area kampus?',
                    'Apakah tersedia peralatan kesehatan dan keselamatan yang mudah diakses di seluruh area kampus?',
                    'Apakah peralatan keamanan tersedia dan dapat memantau seluruh area kampus?',
                    'Apakah lahan kampus berada pada lingkungan yang nyaman dan sehat untuk proses pembelajaran?',
                    'Apakah lahan kampus merupakan milik Yayasan Pendidikan STIT Hidayatunnajah?',
                    'Apakah gedung kampus memiliki kualitas bangunan kelas A atau setara?',
                    'Apakah bangunan kampus memenuhi persyaratan keselamatan, kesehatan, kenyamanan, keamanan, instalasi listrik, dan instalasi limbah?',
                    'Apakah ruang kelas memenuhi rasio luas 1–1.5 m2 per mahasiswa?',
                    'Apakah perpustakaan memiliki luas 4000 m2?',
                    'Apakah laboratorium/studio memadai sesuai jenis mata kuliah dan memenuhi capaian pembelajaran lulusan?',
                    'Apakah fasilitas olahraga dapat digunakan dan memenuhi kebutuhan Unit Kegiatan Mahasiswa?',
                    'Apakah ruang berkesenian tersedia dan memenuhi kebutuhan Unit Kegiatan Mahasiswa?',
                    'Apakah ruang Unit Kegiatan Mahasiswa tersedia secara mencukupi dan memadai?',
                    'Apakah ruang pimpinan memenuhi minimal 10m2 per ruangan per pimpinan?',
                    'Apakah ruang dosen memenuhi minimal 4m2 per dosen?',
                    'Apakah ruang tata usaha memenuhi minimal 4m2 per karyawan?',
                    'Apakah fasilitas umum tersedia dalam jumlah memadai dan kondisi baik?',
                    'Apakah tersedia jalan penghubung antar gedung yang nyaman dilalui?',
                    'Apakah area parkir tersedia dengan jumlah dan luas yang memadai?',
                    'Apakah tersedia instalasi air bersih dan pembuangan air kotor?',
                    'Apakah instalasi listrik tersedia dengan daya memadai?',
                    'Apakah tersedia instalasi pengolahan limbah domestik?',
                    'Apakah tersedia tempat pembuangan limbah padat sementara?',
                    'Apakah tersedia jalur evakuasi dan titik kumpul keselamatan?',
                    'Apakah sarana dan prasarana bagi mahasiswa berkebutuhan khusus tersedia dan dapat digunakan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST07 — Standar Pengelolaan Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST07',
                'name' => 'Standar Pengelolaan Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Waket II, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI',
                'questions' => [
                    'Apakah tersedia dokumen tata kelola yang menetapkan pelaksana standar pengelolaan pembelajaran oleh Sekolah Tinggi sebagai UPPS?',
                    'Apakah setiap program studi memiliki dokumen kurikulum dan rencana pembelajaran setiap mata kuliah?',
                    'Apakah tersedia dokumen pelaksanaan penyelenggaraan program pembelajaran yang sesuai dengan standar isi, standar proses, dan standar penilaian?',
                    'Apakah terdapat bukti pelaksanaan kegiatan sistemik untuk menciptakan suasana akademik dan budaya mutu yang baik?',
                    'Apakah dilakukan kegiatan pemantauan dan evaluasi proses pembelajaran secara periodik?',
                    'Apakah tersedia laporan hasil program pembelajaran setiap semester yang digunakan untuk perbaikan dan pengembangan mutu pembelajaran?',
                    'Apakah tersedia dokumen kebijakan, rencana strategis, dan operasional terkait pembelajaran yang dapat diakses sivitas akademika dan pemangku kepentingan?',
                    'Apakah terdapat bukti penyelenggaraan standar kompetensi lulusan, standar isi pembelajaran, standar proses pembelajaran, dan standar penilaian pembelajaran?',
                    'Apakah tersedia Rencana Tindak Lanjut (RTL) hasil RTM untuk peningkatan mutu pengelolaan program studi dalam melaksanakan program pembelajaran?',
                    'Apakah tersedia hasil Audit Mutu Internal (AMI) terkait pelaksanaan program pembelajaran?',
                    'Apakah tersedia panduan terkait perencanaan, pelaksanaan, evaluasi, pengawasan, penjaminan mutu, dan pengembangan kegiatan pembelajaran dan dosen?',
                    'Apakah program studi menyampaikan laporan kinerja program pembelajaran melalui pangkalan data pendidikan tinggi setiap semester secara tepat waktu?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST08 — Standar Pembiayaan Pembelajaran */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST08',
                'name' => 'Standar Pembiayaan Pembelajaran',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Waket II, Kabag. Keuangan, Ketua Prodi PGMI, Ka. SPI',
                'questions' => [
                    'Apakah besaran satuan biaya operasional per mahasiswa per tahun telah memenuhi ≥ 20 juta?',
                    'Apakah penyusunan RAPB tahunan dan penetapan biaya yang ditanggung oleh mahasiswa telah mengacu pada standar satuan biaya operasional?',
                    'Apakah tersedia laporan keuangan yang teraudit internal maupun eksternal dengan status WTP?',
                    'Apakah institusi melaksanakan analisis biaya operasional secara periodik?',
                    'Apakah hasil analisis biaya operasional digunakan sebagai acuan dalam penyusunan rencana kerja dan anggaran tahunan?',
                    'Apakah dilakukan evaluasi tingkat ketercapaian standar satuan biaya operasional pada setiap tahun anggaran?',
                    'Apakah tersedia laporan tahunan penggunaan dana pendidikan dan pelaksanaan kegiatan sesuai dengan Rencana Anggaran Kegiatan Tahunan?',
                    'Apakah terdapat pos penerimaan di luar mahasiswa berupa hibah, kerja sama, atau hasil usaha sesuai kompetensi universitas?',
                    'Apakah persentase perolehan dana yang bersumber dari mahasiswa terhadap total perolehan dana perguruan tinggi ≤ 75%?',
                    'Apakah persentase perolehan dana yang bersumber selain dari mahasiswa dan dari kementerian/lembaga mencapai ≥10% dari total perolehan dana perguruan tinggi?',
                    'Apakah tersedia dokumen kebijakan, mekanisme, dan prosedur penggalangan sumber dana lain secara akuntabel dan transparan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST09 — Standar Persiapan Perkuliahan */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST09',
                'name' => 'Standar Persiapan Perkuliahan',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI',
                'questions' => [
                    'Apakah KRS telah tersedia lebih dari atau sama dengan 7 hari sebelum pelaksanaan perwalian?',
                    'Apakah mahasiswa melaksanakan perwalian tepat waktu dengan tingkat ketercapaian ≥ 90%?',
                    'Apakah tersedia berita acara perwalian pada setiap pelaksanaan perwalian?',
                    'Apakah perwalian dilaksanakan minimal 3 kali dalam 1 semester?',
                    'Apakah Buku Panduan Akademik tersedia dan telah didistribusikan kepada 100% mahasiswa baru pada saat PBAK?',
                    'Apakah Jadwal Kuliah tersedia lebih dari atau sama dengan 2 minggu sebelum perkuliahan dan dapat diakses oleh mahasiswa?',
                    'Apakah Jadwal Praktikum tersedia kurang dari 2 minggu setelah perkuliahan dan dapat diakses oleh mahasiswa?',
                    'Apakah DHMD tersedia 100% paling lambat 2 minggu sebelum perkuliahan dimulai?',
                    'Apakah BAP tersedia 100% paling lambat 2 minggu sebelum perkuliahan dimulai?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST10 — Standar Pelaksanaan Ujian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST10',
                'name' => 'Standar Pelaksanaan Ujian',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI',
                'questions' => [
                    'Apakah Jadwal Ujian telah tersedia lebih dari atau sama dengan 7 hari sebelum pelaksanaan ujian?',
                    'Apakah Jadwal Pengawas ujian telah tersedia lebih dari atau sama dengan 7 hari sebelum pelaksanaan ujian?',
                    'Apakah Soal Ujian telah tersedia 100% lebih dari atau sama dengan 7 hari sebelum pelaksanaan ujian?',
                    'Apakah lebih dari 85% soal ujian telah diverifikasi?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST11 — Standar Hasil Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST11',
                'name' => 'Standar Hasil Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ka. P4M, Ketua Prodi PGMI, Ka. SPMI, Dosen',
                'questions' => [
                    'Apakah terdapat bukti hasil penelitian dosen dan mahasiswa memenuhi kaidah dan metode ilmiah secara sistematis sesuai otonomi keilmuan dan budaya akademik?',
                    'Apakah 100% hasil penelitian (tugas akhir) mahasiswa sesuai dengan Capaian Pembelajaran Lulusan?',
                    'Apakah rasio publikasi di jurnal internasional bereputasi terhadap dosen tetap mencapai ≥ 2 %?',
                    'Apakah rasio publikasi di jurnal Nasional terakreditasi atau jurnal internasional terhadap dosen tetap mencapai ≥ 25%?',
                    'Apakah rasio jumlah publikasi di konferensi dan media masa terhadap dosen tetap mencapai ≥ 25%?',
                    'Apakah rasio jumlah artikel karya ilmiah yang disitasi terhadap dosen tetap mencapai ≥ 10%?',
                    'Apakah terdapat inkubator hasil penelitian di STIT HN?',
                    'Apakah terdapat unit bisnis hasil penelitian di STIT HN?',
                    'Apakah terdapat seminar nasional tahunan call for paper yang dilaksanakan oleh STIT HN dengan cakupan seluruh bidang keilmuan yang ada di STIT HN?',
                    'Apakah lebih dari 20% dari seluruh hasil penelitian dosen dikembangkan menjadi buku ajar/buku teks, sub bab dalam buku ajar, studi kasus, tambahan materi perkuliahan atau bentuk lain yang relevan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST12 — Standar Isi Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST12',
                'name' => 'Standar Isi Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ka. P4M, Ketua Prodi PGMI, Ka. SPMI, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah terdapat bukti penelitian mempunyai tingkat kualitas dan relevansi tujuan, permasalahan, state of the art, metode, dan kebaruan yang signifikan?',
                    'Apakah terdapat bukti materi pada penelitian dasar berorientasi pada luaran penelitian yang berupa penjelasan atau penemuan untuk mengantisipasi suatu gejala, fenomena, kaidah, model, atau postulat baru?',
                    'Apakah terdapat bukti materi pada penelitian terapan berorientasi pada luaran penelitian yang berupa inovasi serta pengembangan ilmu pengetahuan yang bermanfaat bagi masyarakat, dunia usaha, dan/atau dunia kerja dan mengantisipasi kebutuhan masa mendatang?',
                    'Apakah terdapat bukti materi pada penelitian dasar dan penelitian terapan memuat prinsip-prinsip kemanfaatan, kemutakhiran, dan mengantisipasi kebutuhan masa mendatang?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST13 — Standar Proses Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST13',
                'name' => 'Standar Proses Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ka. P4M, Ketua Prodi PGMI, Ka. SPMI, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah tersedia dokumen formal renstra penelitian yang memuat landasan pengembangan, peta jalan penelitian, sumber daya, sasaran program strategis, dan indikator kinerja yang mengacu pada visi misi dengan memperhatikan perkembangan ilmu pengetahuan dan teknologi di masa yang akan datang serta mempertimbangkan masukan dari stakeholders?',
                    'Apakah terdapat bukti kebijakan, pedoman dan prosedur tentang perencanaan, pelaksanaan, dan pelaporan penelitian yang disosialisasikan, mudah diakses, sesuai dengan rencana strategis penelitian, serta dipahami oleh semua pemangku kepentingan?',
                    'Apakah terdapat bukti kebijakan, pedoman dan prosedur tentang penelitian dosen yang melibatkan mahasiswa?',
                    'Apakah tersedia peta jalan penelitian Program Studi yang mengacu pada peta jalan penelitian di dalam Renstra Penelitian Sekolah Tinggi?',
                    'Apakah tersedia peta jalan penelitian dosen/tim dosen yang mengacu pada peta jalan penelitian di dalam Renstra Penelitian?',
                    'Apakah terdapat dokumen proposal penelitian sesuai dengan jenis penelitian yang akan dilakukan?',
                    'Apakah 75 % penelitian dosen melibatkan mahasiswa?',
                    'Apakah ≥ 60 % proposal penelitian dosen sesuai dengan peta jalan penelitian?',
                    'Apakah ≥ 50% proposal penelitian dosen sesuai dengan peta jalan penelitian?',
                    'Apakah terdapat bukti hasil penilaian proposal penelitian dosen?',
                    'Apakah terdapat bukti pengumuman usulan penelitian dosen yang diterima?',
                    'Apakah terdapat bukti legalitas penugasan pelaksana penelitian?',
                    'Apakah terdapat bukti dokumen proposal penelitian?',
                    'Apakah ≥ 50 % penelitian mahasiswa sesuai dengan peta jalan penelitian?',
                    'Apakah terdapat bukti legalitas pengangkatan reviewer?',
                    'Apakah terdapat bukti hasil penilaian proposal penelitian mahasiswa?',
                    'Apakah penelitian dilaksanakan sesuai dengan Proposal Penelitian yang sudah direvisi atas masukan reviewer?',
                    'Apakah pelaksanaan penelitian dilengkapi dengan catatan penelitian (Log Book)?',
                    'Apakah penyerapan anggaran penelitian sesuai dengan ajuan proposal?',
                    'Apakah terdapat bukti kegiatan penelitian dosen telah mempertimbangkan standar mutu, keselamatan kerja, kesehatan, kenyamanan, serta keamanan peneliti, masyarakat, dan lingkungan?',
                    'Apakah terdapat bukti kegiatan penelitian mahasiswa telah mempertimbangkan standar mutu, keselamatan kerja, kesehatan, kenyamanan, serta keamanan peneliti, masyarakat, dan lingkungan?',
                    'Apakah sebanyak 80 % penelitian mahasiswa sudah memenuhi Capaian Pembelajaran Lulusan?',
                    'Apakah kegiatan penelitian yang dilakukan oleh mahasiswa dinyatakan dalam besaran sks minimal 4 sks?',
                    'Apakah terdapat bukti dokumen laporan kemajuan minimal berisi pencapaian sementara, log book, penggunaan anggaran, dan rencana kegiatan sampai penelitian selesai?',
                    'Apakah terdapat berita acara hasil review laporan kemajuan sebagai masukan bagi penelitian?',
                    'Apakah terdapat bukti dokumen laporan akhir yang disertai dengan laporan keuangan sesuai dengan pedoman yang ditetapkan?',
                    'Apakah terdapat bukti luaran penelitian sesuai dengan yang dijanjikan sebagaimana tertuang dalam proposal?',
                    'Apakah terdapat berita acara hasil penilaian terhadap pencapaian penelitian sebagai dasar keberlanjutan penelitian?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST14 — Standar Penilaian Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST14',
                'name' => 'Standar Penilaian Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ka. P4M, Ketua Prodi PGMI, Ka. SPMI, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah terdapat instrumen penilaian proses dan hasil penelitian yang komprehensif, meliputi kualitas penelitian, ketepatan metode, pencapaian hasil, kesesuaian dengan rencana, luaran penelitian, dokumen penelitian, laporan keuangan, dan keberlanjutan penelitian?',
                    'Apakah terdapat bukti pelaksanaan penilaian proses dan hasil penelitian dilakukan untuk memotivasi pelaksana agar terus meningkatkan mutu penelitian?',
                    'Apakah terdapat bukti pelaksanaan penilaian proses dan hasil penelitian bebas dari pengaruh subjektivitas?',
                    'Apakah terdapat bukti pelaksanaan penilaian proses dan hasil penelitian dilakukan dengan kriteria dan prosedur yang jelas dan dipahami oleh peneliti?',
                    'Apakah terdapat bukti pelaksanaan penilaian proses dan hasil penelitian dilakukan secara transparan dimana hasil penilaiannya dapat diakses oleh pihak yang berkepentingan?',
                    'Apakah terdapat bukti penilaian proses dan hasil penelitian yang memperhatikan kesesuaian dengan standar hasil, standar isi dan standar proses penelitian?',
                    'Apakah terdapat bukti penilaian proses dan hasil penelitian menggunakan metode dan instrumen yang mampu mengukur dengan baik?',
                    'Apakah tersedia instrumen penilaian hasil penelitian mahasiswa yang merujuk pada capaian pembelajaran lulusan?',
                    'Apakah terdapat bukti penilaian hasil penelitian mahasiswa yang merujuk pada capaian pembelajaran lulusan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST15 — Standar Peneliti Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST15',
                'name' => 'Standar Peneliti Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ka. P4M, Ketua Prodi PGMI, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah peneliti memiliki kualifikasi akademik minimal S2?',
                    'Apakah peneliti memiliki road map penelitian yang sesuai dengan bidang unggulan STIT HN, PRN, dan RIRN?',
                    'Apakah peneliti memiliki sertifikat Pelatihan Metodologi Penelitian atau pengalaman penelitian?',
                    'Apakah terdapat bukti legal formal keberadaan kelompok riset?',
                    'Apakah kelompok riset aktif dalam jejaring tingkat nasional maupun internasional?',
                    'Apakah kelompok riset menghasilkan produk riset yang bermanfaat untuk menyelesaikan permasalahan masyarakat?',
                    'Apakah kelompok riset menghasilkan produk riset yang berdaya saing internasional?',
                    'Apakah rasio dosen yang mendapat rekognisi per tahun terhadap dosen tetap > 10%?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST16 — Standar Sarpras Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST16',
                'name' => 'Standar Sarpras Penelitian',
                'bidang' => 'WK2',
                'auditi' => 'Ketua, Waket I, Waket II, Ka. P4M, Ketua Prodi PGMI',
                'questions' => [
                    'Apakah tersedia perpustakaan yang dilengkapi e-journal untuk mendukung kegiatan penelitian?',
                    'Apakah e-journal yang tersedia dapat diakses oleh dosen/peneliti untuk kegiatan penelitian?',
                    'Apakah pemanfaatan e-journal mendukung pelaksanaan penelitian sesuai roadmap penelitian?',
                    'Apakah terdapat pengaturan penggunaan perpustakaan dan e-journal?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST17 — Standar Pengelolaan Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST17',
                'name' => 'Standar Pengelolaan Penelitian',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ka. P4M, Ketua Prodi PGMI',
                'questions' => [
                    'Apakah tersedia dokumen tata kelola yang menetapkan dan mengatur pelaksana standar pengelolaan penelitian oleh P3M sebagai Unit Pengelola penelitian?',
                    'Apakah tersedia dokumen rencana program penelitian yang sesuai dengan rencana strategis penelitian?',
                    'Apakah tersedia peraturan, panduan, dan sistem penjaminan mutu internal kegiatan penelitian?',
                    'Apakah terdapat bukti peran P3M dalam memfasilitasi pelaksanaan penelitian?',
                    'Apakah terdapat bukti pelaksanaan pemantauan dan evaluasi pelaksanaan penelitian?',
                    'Apakah terdapat penyelenggaraan seminar internasional hasil penelitian yang terindeks?',
                    'Apakah terdapat jurnal nasional yang terakreditasi?',
                    'Apakah terdapat penyelenggaraan pelatihan/workshop penyusunan proposal dan penulisan karya ilmiah serta pencapaian HAKI?',
                    'Apakah terdapat bukti pemberian insentif kepada peneliti yang berprestasi?',
                    'Apakah laporan kegiatan penelitian memenuhi aspek komprehensif, terperinci, relevan, mutakhir, dan disampaikan tepat waktu?',
                    'Apakah tersedia Kebijakan, Pedoman, dan Prosedur tentang Pengelolaan Penelitian?',
                    'Apakah rencana strategis penelitian merujuk kepada rencana strategis perguruan tinggi?',
                    'Apakah tersedia pedoman kriteria dan prosedur penilaian penelitian yang merujuk pada Pedoman Penilaian Kinerja Penelitian?',
                    'Apakah terdapat bukti pelaksanaan pemantauan dan evaluasi terhadap lembaga atau fungsi penelitian?',
                    'Apakah terdapat bukti tindak lanjut untuk menjaga dan meningkatkan mutu pengelolaan penelitian?',
                    'Apakah tersedia panduan tentang kriteria peneliti yang mengacu pada standar hasil, standar isi, dan standar proses penelitian?',
                    'Apakah terdapat hasil pelaksanaan kerjasama dengan mendayagunakan sarana dan prasarana penelitian pada lembaga lain?',
                    'Apakah kebutuhan jumlah, jenis, dan spesifikasi sarana dan prasarana penelitian terpenuhi?',
                    'Apakah tersedia Laporan Tahunan Kerja Ketua yang disampaikan kepada stakeholders?',
                    'Apakah rasio kerjasama bidang penelitian di tingkat internasional terhadap dosen tetap per tahun ≥ 0,7%?',
                    'Apakah rasio kerjasama bidang penelitian di tingkat nasional terhadap dosen tetap per tahun ≥ 1%?',
                    'Apakah tersedia instrumen pengukuran tingkat kepuasan pemangku kepentingan internal dan eksternal?',
                    'Apakah survei tingkat kepuasan dilaksanakan secara berkala dan datanya terekam?',
                    'Apakah dilakukan analisis hasil survei tingkat kepuasan dengan metode yang tepat?',
                    'Apakah hasil survei dimanfaatkan untuk peningkatan mutu penelitian?',
                    'Apakah tersedia Dokumen Formal kebijakan dan pedoman integrasi penelitian dalam pembelajaran?',
                    'Apakah terdapat bukti sahih implementasi siklus PPEPP tentang integrasi penelitian dalam pembelajaran?',
                    'Apakah monev pelaksanaan integrasi penelitian dalam pembelajaran oleh PSBPM dilaksanakan?',
                    'Apakah isi web dengan info-info terkini bidang penelitian diperbarui?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST18 — Standar Pendanaan Penelitian */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST18',
                'name' => 'Standar Pendanaan Penelitian',
                'bidang' => 'WK2',
                'auditi' => 'Ketua, Waket II, Ka. P4M, Ketua Prodi PGMI, Kabag. Keuangan',
                'questions' => [
                    'Apakah tersedia alokasi dana penelitian dari internal universitas sebesar 2,5% dari RAB?',
                    'Apakah dana penelitian digunakan untuk membiayai perencanaan penelitian, pelaksanaan penelitian, pengendalian penelitian, pemantauan dan evaluasi penelitian, pelaporan hasil penelitian, dan diseminasi hasil penelitian?',
                    'Apakah tersedia kebijakan yang mengatur mekanisme pendanaan dan pembiayaan penelitian?',
                    'Apakah rasio jumlah penelitian dengan biaya dari PT atau mandiri per tahun terhadap dosen tetap ≥ 50%?',
                    'Apakah rata-rata dana penelitian per dosen per tahun ≥ Rp 10 juta?',
                    'Apakah tersedia dana pengelolaan penelitian dalam Rencana Kerja dan Belanja Tahunan Sekolah Tinggi sebesar 1% dari RAB?',
                    'Apakah dana pengelolaan penelitian digunakan untuk manajemen penelitian (seleksi proposal, pemantauan dan evaluasi, pelaporan, diseminasi hasil penelitian), peningkatan kapasitas peneliti, dan insentif publikasi ilmiah atau insentif kekayaan intelektual (KI)?',
                    'Apakah terdapat unit bisnis yang mengelola komersialisasi hasil penelitian?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST19 — Standar Hasil PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST19',
                'name' => 'Standar Hasil PKM',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ketua Prodi PGMI, Ka. P4M, Dosen',
                'questions' => [
                    'Apakah ≥ 75% hasil PkM berupa penyelesaian masalah yang dihadapi masyarakat?',
                    'Apakah ≥ 60% kegiatan PkM sesuai dengan keahlian dan bidang ilmu pelaksana?',
                    'Apakah rasio jumlah judul PkM dosen tetap yang sesuai dengan keilmuan Program Studi terhadap jumlah dosen tetap ≥ 1?',
                    'Apakah rasio publikasi di jurnal Nasional terakreditasi terhadap dosen tetap ≥ 25%?',
                    'Apakah rasio jumlah publikasi di seminar dan media masa terhadap dosen tetap ≥ 25%?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST20 — Standar Isi PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST20',
                'name' => 'Standar Isi PKM',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ka. P4M, Ketua Prodi PGMI, Dosen',
                'questions' => [
                    'Apakah lebih dari 10% program PkM merupakan penerapan hasil penelitian?',
                    'Apakah lebih dari 10% program PkM merupakan pengembangan ilmu pengetahuan dalam rangka pemberdayaan masyarakat?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST21 — Standar Proses PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST21',
                'name' => 'Standar Proses PKM',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ka. P4M, Ketua Prodi PGMI, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah tersedia dokumen formal renstra PkM yang memuat landasan pengembangan, peta jalan pengabdian kepada masyarakat, sumber daya, sasaran program strategis, dan indikator kinerja?',
                    'Apakah tersedia pedoman PkM yang memuat perencanaan, pelaksanaan, dan pelaporan yang mudah diakses dan dipahami oleh semua pemangku kepentingan?',
                    'Apakah tersedia peta jalan PkM Sekolah Tinggi/Program Studi yang mengacu pada Renstra PkM?',
                    'Apakah dokumen proposal PkM disusun sesuai pedoman PkM dan Renstra PkM?',
                    'Apakah ≥ 80% PkM dosen melibatkan mahasiswa?',
                    'Apakah ≥ 60% proposal PkM sesuai dengan Renstra Pengabdian Kepada Masyarakat?',
                    'Apakah terdapat bukti legalitas pengangkatan reviewer?',
                    'Apakah terdapat bukti hasil penilaian proposal PkM dosen?',
                    'Apakah terdapat bukti pengumuman usulan PkM dosen?',
                    'Apakah terdapat bukti legalitas penugasan pelaksana PkM/kerja sama PkM?',
                    'Apakah pelaksanaan PkM sesuai dengan proposal yang telah direvisi berdasarkan masukan reviewer?',
                    'Apakah pelaksanaan PkM dilengkapi dengan Log Book?',
                    'Apakah penyerapan anggaran PkM sesuai dengan ajuan proposal?',
                    'Apakah pelaksanaan PkM mempertimbangkan standar mutu, keselamatan kerja, kesehatan, kenyamanan, serta keamanan?',
                    'Apakah ≥ 50% kegiatan PkM mahasiswa memenuhi capaian pembelajaran lulusan?',
                    'Apakah kegiatan PkM mahasiswa dinyatakan dalam besaran sks minimal 1 sks (170 menit/pertemuan/minggu)?',
                    'Apakah > 50% kegiatan PkM sesuai dengan Rencana Induk Penelitian Sekolah Tinggi?',
                    'Apakah > 50% kegiatan PkM memiliki pengukuran keberhasilan yang jelas?',
                    'Apakah > 75% kegiatan PkM memiliki roadmap yang jelas?',
                    'Apakah laporan kemajuan memuat pencapaian sementara, log book, penggunaan anggaran, dan rencana kegiatan?',
                    'Apakah terdapat berita acara hasil review laporan kemajuan?',
                    'Apakah laporan akhir disertai laporan keuangan sesuai pedoman?',
                    'Apakah luaran PkM sesuai dengan yang dijanjikan dalam proposal?',
                    'Apakah terdapat berita acara hasil penilaian terhadap pencapaian peneliti kegiatan PkM?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST22 — Standar Penilaian PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST22',
                'name' => 'Standar Penilaian PKM',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ketua Prodi PGMI, Ketua Prodi PBA, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah tersedia instrumen penilaian proses dan hasil PkM yang mencakup seluruh aspek tersebut?',
                    'Apakah pelaksanaan penilaian digunakan untuk memotivasi pelaksana meningkatkan mutu PkM?',
                    'Apakah pelaksanaan penilaian dilakukan bebas dari pengaruh subjektivitas?',
                    'Apakah kriteria dan prosedur penilaian jelas dan dipahami oleh pelaksana PkM?',
                    'Apakah hasil penilaian dapat diakses oleh semua pemangku kepentingan?',
                    'Apakah penilaian memperhatikan kesesuaian dengan standar hasil, standar isi, dan standar proses PkM?',
                    'Apakah tingkat kepuasan masyarakat mencapai ≥3.5?',
                    'Apakah terdapat perubahan sikap pada ≥50% peserta PkM?',
                    'Apakah terdapat perubahan pengetahuan pada ≥50% peserta PkM?',
                    'Apakah terdapat perubahan keterampilan pada ≥50% peserta PkM?',
                    'Apakah ≥50% peserta tetap memanfaatkan ilmu pengetahuan setelah kegiatan?',
                    'Apakah terdapat umpan balik pengayaan sumber belajar dari hasil PkM?',
                    'Apakah terdapat bukti teratasinya masalah sosial dan rekomendasi yang dihasilkan?',
                    'Apakah ≥70% penilaian menggunakan metode dan instrumen yang relevan?',
                    'Apakah kriteria dan prosedur penilaian mudah dipahami?',
                    'Apakah instrumen evaluasi telah melalui uji validitas dan reliabilitas?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST23 — Standar Pelaksana PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST23',
                'name' => 'Standar Pelaksana PKM',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Ketua Prodi PGMI, Ketua Prodi PBA, Ka. P4M, Dosen, Mahasiswa',
                'questions' => [
                    'Apakah tersedia pedoman yang mengatur kualifikasi akademik pelaksana Pengabdian kepada Masyarakat?',
                    'Apakah Ketua pelaksana PkM memiliki jabatan akademik sekurang-kurangnya Asisten Ahli?',
                    'Apakah pelaksana PkM memiliki kapabilitas rekam jejak keilmuan yang sesuai dengan kegiatan yang diusulkan/masalah yang ditangani?',
                    'Apakah terdapat bukti legal formal keberadaan pelaksana pengabdian?',
                    'Apakah terdapat bukti aktif kelompok pelaksana pengabdian dalam jejaring tingkat nasional?',
                    'Apakah terdapat bukti kelompok pelaksana pengabdian menghasilkan produk PkM yang bermanfaat untuk menyelesaikan permasalahan masyarakat?',
                    'Apakah terdapat bukti kelompok pelaksana pengabdian menghasilkan produk yang berdaya saing di tingkat nasional?',
                    'Apakah pelaksana PkM melibatkan minimal 2 orang mahasiswa?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST24 — Standar Sarpras PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST24',
                'name' => 'Standar Sarpras PKM',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Waket II, Ketua Prodi PGMI, Ketua Prodi PBA, Ka. BAAK, Ketua',
                'questions' => [
                    'Apakah tersedia perpustakaan yang dilengkapi e-journal yang dapat mendukung kegiatan penelitian dan PkM?',
                    'Apakah tersedia ruangan yang memadai untuk pengelola penelitian dan PkM?',
                    'Apakah sarana dan prasarana PkM memenuhi standar mutu, keselamatan kerja, kesehatan, kenyamanan, dan keamanan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST25 — Standar Pengelolaan PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST25',
                'name' => 'Standar Pengelolaan PKM',
                'bidang' => 'WK1',
                'auditi' => 'Waket I, Ketua Prodi PGMI, Ketua Prodi PBA, Ka. P4M, Ketua',
                'questions' => [
                    'Apakah tersedia dokumen tata kelola yang menetapkan dan mengatur pelaksana standar pengelolaan pengabdian kepada masyarakat oleh P3M sebagai Unit Pengelola PkM?',
                    'Apakah tersedia dokumen rencana program pengabdian kepada masyarakat sesuai dengan rencana strategis pengabdian kepada masyarakat sekolah tinggi?',
                    'Apakah tersedia peraturan, panduan, dan sistem penjaminan mutu internal kegiatan pengabdian kepada masyarakat?',
                    'Apakah terdapat bukti keterlibatan P3M dalam memfasilitasi pelaksanaan kegiatan pengabdian kepada masyarakat?',
                    'Apakah tersedia bukti pelaksanaan pemantauan dan evaluasi pelaksanaan pengabdian kepada masyarakat?',
                    'Apakah tersedia bukti pelaksanaan diseminasi hasil pengabdian kepada masyarakat?',
                    'Apakah tersedia bukti pelaksanaan kegiatan peningkatan kemampuan pelaksana pengabdian kepada masyarakat?',
                    'Apakah tersedia bukti pemberian penghargaan kepada pelaksana pengabdian kepada masyarakat yang berprestasi?',
                    'Apakah terdapat bukti kerja sama dalam mendayagunakan sarana dan prasarana pengabdian kepada masyarakat pada lembaga lain?',
                    'Apakah terdapat bukti hasil analisis kebutuhan yang menyangkut jumlah, jenis, dan spesifikasi sarana dan prasarana pengabdian kepada masyarakat?',
                    'Apakah laporan kegiatan pengabdian pada masyarakat memenuhi aspek komprehensif, rinci, relevan, mutakhir, dan disampaikan tepat waktu?',
                    'Apakah tersedia dokumen rencana strategis pengabdian kepada masyarakat yang merupakan bagian dari rencana strategis perguruan tinggi?',
                    'Apakah tersedia dokumen tentang kriteria dan prosedur penilaian pengabdian kepada masyarakat yang mencakup aspek hasil pengabdian kepada masyarakat?',
                    'Apakah terdapat bukti upaya menjaga dan meningkatkan mutu pengelolaan lembaga atau fungsi pengabdian kepada masyarakat secara berkelanjutan?',
                    'Apakah tersedia bukti pelaksanaan pemantauan dan evaluasi terhadap lembaga atau fungsi pengabdian kepada masyarakat?',
                    'Apakah tersedia panduan tentang kriteria pelaksana pengabdian kepada masyarakat yang mengacu pada standar hasil, standar isi, dan standar proses pengabdian kepada masyarakat?',
                    'Apakah terdapat bukti kerja sama dalam mendayagunakan sarana dan prasarana pengabdian kepada masyarakat pada lembaga lain?',
                    'Apakah terdapat bukti hasil analisis kebutuhan yang menyangkut jumlah, jenis, dan spesifikasi sarana dan prasarana pengabdian kepada masyarakat?',
                    'Apakah terdapat bukti penyampaian laporan kinerja lembaga atau fungsi pengabdian kepada masyarakat melalui pangkalan data pendidikan tinggi?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST26 — Standar Pendanaan PKM */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST26',
                'name' => 'Standar Pendanaan PKM',
                'bidang' => 'WK2',
                'auditi' => 'Waket II, Ketua Prodi PGMI, Ketua Prodi PBA, Ka. P4M, Kabag. Keuangan, Ketua',
                'questions' => [
                    'Apakah tersedia alokasi dana pengabdian kepada masyarakat dari internal universitas sebesar 0.75% dari RAPB?',
                    'Apakah tersedia pedoman yang mengatur mekanisme pendanaan dan pembiayaan pengabdian kepada masyarakat yang ditetapkan oleh ketua?',
                    'Apakah tersedia dana pengelolaan pengabdian kepada masyarakat sebesar 0.25% yang dituangkan dalam Rencana Kerja dan Belanja Tahunan Sekolah Tinggi?',
                    'Apakah dana pengelolaan pengabdian kepada masyarakat digunakan untuk membiayai manajemen pengabdian kepada masyarakat yang terdiri atas seleksi proposal, pemantauan dan evaluasi, pelaporan, dan diseminasi hasil pengabdian kepada masyarakat?',
                    'Apakah dana pengelolaan pengabdian kepada masyarakat digunakan untuk membiayai peningkatan kapasitas pelaksana?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST27 — Standar Penerimaan Mahasiswa Baru */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST27',
                'name' => 'Standar Penerimaan Mahasiswa Baru',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Ketua Prodi PGMI, Ketua Prodi PBA',
                'questions' => [
                    'Apakah tersedia kebijakan penerimaan mahasiswa baru dan dilaksanakan secara konsisten?',
                    'Apakah penerimaan mahasiswa baru tidak membeda-bedakan suku, agama, ras, golongan, gender, status sosial, dan politik?',
                    'Apakah STIT HN menerima mahasiswa baru yang memilki potensi akademik tapi kurang mampu secara ekonomi?',
                    'Apakah STIT HN menerima mahasiswa baru yang memilki potensi akademik namun memiliki keterbatasan fisik tertentu?',
                    'Apakah tersedia kebijakan penerimaan mahasiswa asing dan dilaksanakan secara konsisten?',
                    'Apakah tersedia pedoman Penerimaan Mahasiswa Baru dan dilaksanakan secara konsisten?',
                    'Apakah pedoman PMB memuat kebijakan/pendekatan penerimaan mahasiswa baru?',
                    'Apakah pedoman PMB memuat kriteria penerimaan mahasiswa baru?',
                    'Apakah pedoman PMB memuat prosedur penerimaan mahasiswa baru?',
                    'Apakah pedoman PMB memuat instrumen penerimaan mahasiswa baru?',
                    'Apakah sistem pengambilan keputusan dilaksanakan secara konsisten?',
                    'Apakah tersedia sistem penerimaan mahasiswa baru secara online dari pendaftaran, pengumuman hasil seleksi, hingga registrasi?',
                    'Apakah jadwal, jalur, dan metode ditetapkan paling lambat 3 bulan sebelum dimulainya penerimaan mahasiswa baru?',
                    'Apakah informasi disampaikan melalui website atau media promosi lainnya paling lambat 1 bulan sebelum masa penerimaan mahasiswa baru?',
                    'Apakah penetapan jumlah daya tampung mengacu pada rasio dosen dan mahasiswa serta ketersediaan sarana dan prasarana?',
                    'Apakah pengumuman kelulusan dilakukan melalui website dan ditetapkan melalui Surat Keputusan Ketua dengan melibatkan Ketua Prodi dan anggota senat Sekolah Tinggi melalui rapat pimpinan?',
                    'Apakah presentasi jumlah mahasiswa yang registrasi terhadap yang diterima ≥ 95%?',
                    'Apakah presentasi jumlah mahasiswa asing terhadap seluruh mahasiswa ≥ 0,5%?',
                    'Apakah presentasi jumlah mahasiswa baru yang mendapatkan beasiswa ≥ 5%?',
                    'Apakah terdapat upaya yang dilakukan Sekolah Tinggi dan Program Studi untuk meningkatkan animo calon mahasiswa?',
                    'Apakah terjadi peningkatan animo calon mahasiswa selama 3 tahun terakhir ≥ 10%?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST28 — Standar SDM Kemahasiswaan */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST28',
                'name' => 'Standar SDM Kemahasiswaan',
                'bidang' => 'WK3',
                'auditi' => 'Ketua, Waket III',
                'questions' => [
                    'Apakah tersedia Peraturan Ketua tentang pengelolaan kegiatan kemahasiswaan yang mencakup seluruh unsur tersebut?',
                    'Apakah terdapat SK pengelola/unit khusus pengelola beasiswa serta SK penyaluran dan distribusi beasiswa?',
                    'Apakah tersedia Standar Operasional Prosedur (SOP) pengajuan beasiswa?',
                    'Apakah terdapat laporan pengelolaan secara rutin terkait penyaluran dan distribusi beasiswa dan penggunaan sistem informasi?',
                    'Apakah persentase jumlah penerima beasiswa Non APBN dari total mahasiswa aktif ≥ 5%?',
                    'Apakah tersedia SK unit pengelola layanan kesehatan mahasiswa, klinik kesehatan, tenaga kesehatan/jaga, dan ambulance milik institusi Perguruan Tinggi?',
                    'Apakah tersedia gedung/ruangan layanan konseling, SOP konseling, SK pengelola, SDM konselor, dan sistem informasi layanan konseling?',
                    'Apakah terdapat bukti pelaksanaan seluruh kegiatan tersebut?',
                    'Apakah tersedia SK unit pengelola mentoring BTAQ dan bukti pelaksanaan Placement Test, mentoring, serta UAM?',
                    'Apakah tersedia SK unit kewirausahaan, program terstruktur, kegiatan, dan fasilitas gallery produk?',
                    'Apakah tersedia SK pusat karir, program kerja, pelaksanaan kegiatan, dan sistem informasi karier serta tracer study?',
                    'Apakah pelacakan data lulusan mencakup seluruh aspek tersebut dan Response Rate ≥ 30%?',
                    'Apakah tersedia hasil survey pengukuran kepuasan pengguna lulusan yang mencakup seluruh aspek tersebut?',
                    'Apakah terdapat laporan analisis tracer study yang disosialisasikan dan digunakan untuk pengembangan kurikulum dan pembelajaran?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST29 — Standar SDM Kemahasiswaan (Lembaga) */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST29',
                'name' => 'Standar SDM Kemahasiswaan (Lembaga)',
                'bidang' => 'WK3',
                'auditi' => 'Ketua, Waket III',
                'questions' => [
                    'Apakah tersedia SK pengelola dan pengembangan kegiatan kemahasiswaan yang masih berlaku?',
                    'Apakah SK pengelola dan pengembangan kegiatan kemahasiswaan ditetapkan oleh pimpinan perguruan tinggi sesuai kewenangan?',
                    'Apakah Bidang Kemahasiswaan memiliki struktur organisasi minimal setingkat direktur sesuai ketentuan institusi?',
                    'Apakah posisi penanggung jawab Bidang Kemahasiswaan tercantum secara jelas dalam struktur organisasi institusi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang pengembangan penalaran dan kreativitas yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang kesejahteraan dan kewirausahaan yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang minat, bakat, dan organisasi kemahasiswaan yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang penyelarasan dan pengembangan karier yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang pengembangan mental spritual kebangsaan yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang internasionalisasi yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah tersedia SK penanggung jawab kegiatan bidang pemberdayaan alumni yang dilengkapi tugas pokok dan fungsi?',
                    'Apakah seluruh bidang kegiatan kemahasiswaan memiliki penanggung jawab yang ditetapkan secara formal melalui SK?',
                    'Apakah tugas pokok dan fungsi masing-masing penanggung jawab kegiatan kemahasiswaan terdokumentasi dengan jelas dan sesuai bidangnya?',
                    'Apakah dokumen kelembagaan kemahasiswaan terdokumentasi dan mudah ditelusuri saat audit berlangsung?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST30 — Standar Sarpras Kemahasiswaan */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST30',
                'name' => 'Standar Sarpras Kemahasiswaan',
                'bidang' => 'WK3',
                'auditi' => 'Ketua, Waket III',
                'questions' => [
                    'Apakah setiap organisasi kemahasiswaan memiliki sekretariat?',
                    'Apakah tersedia laman/portal resmi kemahasiswaan yang terintegrasi dengan portal utama dan/atau menggunakan domain resmi Perguruan Tinggi?',
                    'Apakah tersedia sarana prasarana kegiatan/latihan mahasiswa dalam bidang olahraga?',
                    'Apakah tersedia sarana prasarana kegiatan dan pengembangan kompetensi mahasiswa bidang penalaran dan kreativitas?',
                    'Apakah tersedia sarana prasarana kegiatan pengembangan kerohanian mahasiswa?',
                    'Apakah tersedia sarana prasarana pertemuan guna pengembangan kegiatan minat bakat mahasiswa?',
                    'Apakah tersedia sarana prasarana pertunjukan/pentas seni/pameran karya mahasiswa?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST31 — Standar Pembiayaan Kegiatan Mahasiswa */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST31',
                'name' => 'Standar Pembiayaan Kegiatan Mahasiswa',
                'bidang' => 'WK2',
                'auditi' => 'Ketua, Waket II, Waket III, Kabag. Keuangan',
                'questions' => [
                    'Apakah tersedia alokasi anggaran kemahasiswaan minimal 5% dari total anggaran Perguruan Tinggi?',
                    'Apakah alokasi anggaran kemahasiswaan digunakan untuk mendukung kegiatan organisasi kemahasiswaan dan kegiatan pengembangan mahasiswa?',
                    'Apakah terdapat bukti pengesahan alokasi anggaran kemahasiswaan dalam dokumen anggaran Perguruan Tinggi?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST32 — Standar Penghargaan & Prestasi Mahasiswa */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST32',
                'name' => 'Standar Penghargaan & Prestasi Mahasiswa',
                'bidang' => 'WK2',
                'auditi' => 'Ketua, Waket II, Waket III',
                'questions' => [
                    'Apakah terdapat peraturan Ketua yang mengatur tentang pemberian penghargaan terhadap prestasi mahasiswa?',
                    'Apakah terdapat SK pemberian penghargaan prestasi mahasiswa sebagai implementasi peraturan Ketua?',
                    'Apakah persentase mahasiswa yang mengikuti pertukaran mahasiswa dari total mahasiswa aktif minimal 0,5%?',
                    'Apakah persentase mahasiswa yang melaksanakan kegiatan pengabdian kepada masyarakat dari total mahasiswa aktif minimal 0,5%?',
                    'Apakah jumlah poin rekognisi mahasiswa dihitung sesuai ketentuan dan mencapai ≤ 10 %?',
                    'Apakah terdapat bukti pendaftaran paten oleh mahasiswa?',
                    'Apakah terdapat bukti Hak Cipta/Buku mahasiswa?',
                    'Apakah terdapat mahasiswa yang menjadi juri/pelatih internasional?',
                    'Apakah terdapat mahasiswa yang menjadi juri/pelatih nasional?',
                    'Apakah terdapat mahasiswa yang mengikuti conference/seminar internasional sebagai peserta atau pemakalah?',
                    'Apakah terdapat mahasiswa yang mengikuti conference/seminar nasional sebagai peserta atau pemakalah?',
                    'Apakah terdapat mahasiswa yang menjadi peserta pameran internasional?',
                    'Apakah terdapat mahasiswa yang menjadi peserta pameran nasional?',
                    'Apakah terdapat bukti perolehan prestasi mahasiswa pada ko dan ekstrakulikuler secara mandiri?',
                    'Apakah Perguruan Tinggi menjadi tuan rumah kegiatan tingkat nasional/wilayah?',
                    'Apakah terdapat mahasiswa peserta ONMIPA Tingkat Nasional/Wilayah?',
                    'Apakah terdapat mahasiswa peserta NUDC?',
                    'Apakah terdapat mahasiswa peserta KDMI?',
                    'Apakah terdapat mahasiswa peserta PILMAPRES?',
                    'Apakah terdapat mahasiswa peserta PKM/PIMNAS?',
                    'Apakah terdapat mahasiswa peserta PBBT?',
                    'Apakah terdapat mahasiswa peserta KBMI?',
                    'Apakah terdapat mahasiswa peserta Expo KMI?',
                    'Apakah terdapat mahasiswa peserta POMNAS?',
                    'Apakah terdapat mahasiswa peserta MTQMN?',
                    'Apakah terdapat mahasiswa peserta PEKSIMINAS?',
                    'Apakah terdapat mahasiswa peserta PHBD?',
                    'Apakah terdapat mahasiswa peserta FFMI?',
                    'Apakah terdapat mahasiswa peserta KPKM?',
                    'Apakah terdapat mahasiswa peserta WUDC?',
                    'Apakah persentase penghargaan atau prestasi akademik mahasiswa tingkat nasional ≥ 0,25% dari jumlah mahasiswa aktif?',
                    'Apakah persentase penghargaan atau prestasi non akademik mahasiswa tingkat nasional ≥ 0,5% dari jumlah mahasiswa aktif?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST33 — Standar Kebijakan Kerja Sama */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST33',
                'name' => 'Standar Kebijakan Kerja Sama',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI, Ka. P4M',
                'questions' => [
                    'Apakah tersedia Kebijakan STIT HN untuk kerja sama Dalam Negeri?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur pengembangan Ilmu Pengetahuan dan Teknologi?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur pengembangan Jejaring Pendidikan, Penelitian, dan PkM?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur peningkatan kapasitas Institusi?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur kesamaan persepsi dan tujuan ke depan?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur pengembangan dan peningkatan kemampuan dosen serta mahasiswa?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Dalam Negeri memuat unsur pemanfaatan sarana prasarana?',
                    'Apakah tersedia Kebijakan STIT HN untuk kerja sama Luar Negeri?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Luar Negeri memuat unsur pengembangan Ilmu Pengetahuan dan Teknologi untuk Go Internasional?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Luar Negeri memuat unsur pengembangan Jejaring Pendidikan dan Joint Research?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Luar Negeri memuat unsur peningkatan kapasitas Institusi untuk Dosen Asing?',
                    'Apakah Kebijakan STIT HN untuk kerja sama Luar Negeri memuat unsur pengembangan dan peningkatan kemampuan dosen serta mahasiswa?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur misi dan tujuan kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur prinsip kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur manfaat kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur tantangan dalam pelaksanaan kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur kepemilikan hak cipta dan paten?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur mekanisme resprokal?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Dalam Negeri mengatur keberlanjutan kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Luar Negeri mengatur misi dan tujuan kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Luar Negeri mengatur prinsip kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Luar Negeri mengatur manfaat kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Luar Negeri mengatur tantangan dalam pelaksanaan kerja sama?',
                    'Apakah pedoman kerja sama dengan Institusi atau Lembaga Luar Negeri mengatur kriteria lembaga atau institusi yang menjadi partner?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST34 — Standar Lembaga Kerja Sama */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST34',
                'name' => 'Standar Lembaga Kerja Sama',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III, Ketua Prodi PGMI, Ka. BAAK, Ka. SPMI, Ka. P4M',
                'questions' => [
                    'Apakah tersedia Peraturan Rektor/Ketua tentang kelembagaan kerja sama di STIT Hidayatunnajah Bekasi?',
                    'Apakah Peraturan Rektor/Ketua mengatur Kelembagaan Kerja Sama Dalam Negeri bidang Akademik?',
                    'Apakah Peraturan Rektor/Ketua mengatur Kelembagaan Kerja Sama Dalam Negeri bidang Non-Akademik?',
                    'Apakah Peraturan Rektor/Ketua mengatur Kelembagaan Kerja Sama Luar Negeri bidang Akademik?',
                    'Apakah Peraturan Rektor/Ketua mengatur Kelembagaan Kerja Sama Luar Negeri bidang Non-Akademik?',
                    'Apakah tersedia Pedoman Sistem dan Mekanisme Pengelolaan Kelembagaan Kerja Sama STIT HN?',
                    'Apakah Pedoman Sistem dan Mekanisme Pengelolaan Kelembagaan Kerja Sama STIT HN memuat Pedoman Sistem Kelembagaan?',
                    'Apakah Pedoman Sistem dan Mekanisme Pengelolaan Kelembagaan Kerja Sama STIT HN memuat Pedoman Mekanisme Pengelolaan Kelembagaan?',
                    'Apakah Pedoman Sistem dan Mekanisme Pengelolaan Kelembagaan Kerja Sama STIT HN memuat Pedoman Job Description SDM Kelembagaan Kerja Sama?',
                    'Apakah tersedia Kelembagaan Kerja Sama di STIT Hidayatunnajah Bekasi?',
                    'Apakah struktur Kelembagaan Kerja Sama mencakup kerja sama Dalam Negeri dan Luar Negeri?',
                    'Apakah struktur Kelembagaan Kerja Sama mencakup bidang Akademik dan Non-Akademik?',
                    'Apakah tersedia Job Description Kelembagaan Kerja Sama?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST35 — Standar SDM untuk Kerja Sama */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST35',
                'name' => 'Standar SDM untuk Kerja Sama',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III',
                'questions' => [
                    'Apakah tersedia SK pengelola dan pengembangan kegiatan jejaring kerja sama di STIT Hidayatunnajah Bekasi?',
                    'Apakah bidang kerja sama memiliki struktur organisasi yang ditetapkan dalam SOTK STIT Hidayatunnajah Bekasi?',
                    'Apakah tersedia SK penanggung jawab kegiatan kerja sama?',
                    'Apakah SK penanggung jawab kegiatan kerja sama memuat tugas pokok dan fungsi yang menangani Kerja Sama Dalam Negeri?',
                    'Apakah SK penanggung jawab kegiatan kerja sama memuat tugas pokok dan fungsi yang menangani Kerja Sama Luar Negeri?',
                    'Apakah tersedia SK dan Peraturan Ketua yang mengatur Kriteria dan Kompetensi SDM Pengelola Bidang Kerja Sama?',
                    'Apakah dokumen Kriteria dan Kompetensi SDM Pengelola Bidang Kerja Sama mencakup pengelola kerja sama Dalam Negeri?',
                    'Apakah dokumen Kriteria dan Kompetensi SDM Pengelola Bidang Kerja Sama mencakup pengelola kerja sama Luar Negeri?',
                    'Apakah tersedia SK Pengangkatan Kepala Bagian Kerja sama?',
                    'Apakah SK Pengangkatan memuat Kepala Bidang Kerja Sama?',
                    'Apakah SK Pengangkatan memuat Kepala Seksi Bidang Kerja Sama Dalam dan Luar Negeri?',
                    'Apakah SK Pengangkatan memuat Staf Bagian Kerja Sama?',
                    'Apakah tersedia SK Kelembagaan Pengelola Bidang Kerja Sama?',
                    'Apakah SK Kelembagaan Pengelola Bidang Kerja Sama memuat Kriteria dan Kompetensi SDM?',
                    'Apakah SK Kelembagaan Pengelola Bidang Kerja Sama memuat Tugas Fungsi Kelembagaan Kerja Sama?',
                    'Apakah tersedia pedoman yang mengatur kewajiban dosen terlibat dalam bidang kerja sama dalam pendidikan, penelitian dan PkM Dalam dan Luar Negeri?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST36 — Standar Prosedur Pelaksanaan Kerja Sama */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST36',
                'name' => 'Standar Prosedur Pelaksanaan Kerja Sama',
                'bidang' => 'WK3',
                'auditi' => '-',
                'questions' => [
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama Pendidikan yang berlaku di STIT Hidayatunnajah Bekasi?',
                    'Apakah SOP Pelaksanaan Kerja Sama Pendidikan telah digunakan dalam pelaksanaan kerja sama pendidikan?',
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama Penelitian dan Joint Research?',
                    'Apakah SOP Pelaksanaan Kerja Sama Penelitian dan Joint Research telah diimplementasikan?',
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama PkM?',
                    'Apakah SOP Pelaksanaan Kerja Sama PkM telah digunakan dalam pelaksanaan PkM?',
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama Pengelolaan Beasiswa?',
                    'Apakah SOP Pelaksanaan Kerja Sama Pengelolaan Beasiswa telah diimplementasikan?',
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama Seni Budaya bagi mahasiswa?',
                    'Apakah SOP Pelaksanaan Kerja Sama Seni Budaya bagi mahasiswa telah diimplementasikan?',
                    'Apakah tersedia SOP Pelaksanaan Kerja Sama Pertukaran Dosen?',
                    'Apakah SOP Pelaksanaan Kerja Sama Pertukaran Dosen telah diimplementasikan?',
                    'Apakah tersedia SOP Pencairan Dana Kerja sama?',
                    'Apakah SOP Pencairan Dana Kerja sama telah digunakan dalam pencairan dana kerja sama?',
                    'Apakah tersedia kurikulum di prodi yang mendukung untuk kerja sama dalam dan luar negeri?',
                    'Apakah kurikulum di prodi telah mengakomodasi implementasi kerja sama dalam dan luar negeri?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST37 — Standar Pelaksanaan Kegiatan Kerja Sama */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST37',
                'name' => 'Standar Pelaksanaan Kegiatan Kerja Sama',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket III, Ketua Prodi PGMI, Ketua Prodi PBA',
                'questions' => [
                    'Apakah kerja sama memberikan peningkatan kinerja tridarma dan fasilitas pendukung?',
                    'Apakah terdapat bukti peningkatan kinerja tridarma dan fasilitas pendukung dari kerja sama?',
                    'Apakah kerja sama memberikan manfaat dan kepuasan kepada mitra?',
                    'Apakah terdapat data kepuasan mitra terhadap pelaksanaan kerja sama?',
                    'Apakah kerja sama menjamin keberlanjutan kerja sama dan hasilnya?',
                    'Apakah terdapat bukti keberlanjutan hasil kerja sama?',
                    'Apakah lembaga bekerja sama dengan Lembaga mitra yang mampu dan saling memberikan kontribusi terhadap lembaga?',
                    'Apakah terdapat bukti kontribusi timbal balik antara lembaga dan mitra?',
                    'Apakah lingkup kerja sama mencakup skala Lokal/Wilayah/Nasional/Internasional?',
                    'Apakah terdapat variasi lingkup kerja sama pada tingkat Lokal/Wilayah/Nasional/Internasional?',
                    'Apakah menggunakan instrumen kepuasan yang sahih, andal, dan mudah digunakan?',
                    'Apakah pengukuran kepuasan dilaksanakan secara berkala dan datanya terekam secara komprehensif?',
                    'Apakah data kepuasan dianalisis dengan metode yang tepat serta bermanfaat untuk pengambilan keputusan?',
                    'Apakah dilakukan review terhadap pelaksanaan pengukuran kepuasan para pemangku kepentingan?',
                    'Apakah hasil pengukuran kepuasan dipublikasikan dan mudah diakses oleh para pemangku kepentingan?',
                    'Apakah hasil pengukuran kepuasan ditindaklanjuti untuk perbaikan dan peningkatan mutu secara berkala dan tersistem?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST38 — Standar Kebijakan Tata Pamong & Tata Kelola */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST38',
                'name' => 'Standar Kebijakan Tata Pamong & Tata Kelola',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III',
                'questions' => [
                    'Apakah tersedia SK Pendirian Yayasan WHB sebagai dasar untuk pelaksanaan pendidikan di Perguruan Tinggi STIT Hidayatunnajah Bekasi?',
                    'Apakah organisasi dan pengelolaan STIT Hidayatunnajah Bekasi telah sesuai dengan Statuta Yayasan WHB?',
                    'Apakah tersedia SK Pengangkatan Ketua STIT Hidayatunnajah Bekasi?',
                    'Apakah kebijakan terkait pengelolaan organisasi berjalan melalui struktur organisasi yang memadai?',
                    'Apakah Pimpinan STIT HN terdiri dari 1 Orang Ketua dan 3 Orang Wakil Ketua?',
                    'Apakah Susunan Organisasi STIT HN telah sesuai (Senat Sekolah Tinggi, Satuan Pengawas Internal, Unsur Pimpinan Sekolah Tinggi, Dewan Penyantun, Unsur Pelaksana Akademik, Lembaga dan Badan (P3M, PSBPM), Unsur pelaksana Administrasi, Unit Pelaksana Teknis)?',
                    'Apakah unsur pelaksana Administrasi telah lengkap sesuai struktur (Bagian Akademik dan Karier Dosen, Bagian Pengembangan Sistem Informasi dan Teknologi, Bagian Keuangan, Perencanaan dan Pengembangan Kelembagaan, Bagian Pengembangan Sumberdaya Manusia dan Umum, Bagian Kemahasiswaan dan Alumni, Bagian Kerja Sama, Bagian Komunikasi, Informasi dan Promosi)?',
                    'Apakah Unit Pelaksana Teknis terdiri dari UPT Perpustakaan dan UPT Bahasa?',
                    'Apakah tersedia SK yang Mengangkat dan Mengatur Senat STIT Hidayatunnajah?',
                    'Apakah tersedia Tugas Pokok dan Fungsi Senat STIT HN?',
                    'Apakah keanggotaan Senat STIT HN telah sesuai (Guru Besar, Pimpinan Sekolah Tinggi, Kepala Program Studi, Ketua Lembaga, Dosen Tetap Wakil setiap Program Studi)?',
                    'Apakah tersedia SK Ketua yang mengatur tentang Dewan Penyantun STIT HN?',
                    'Apakah Tugas Pokok dan Fungsi Dewan Penyantun STIT HN berjalan?',
                    'Apakah tersedia SK yang Mengangkat dan Mengatur Pelaksana Akademik dalam Bidang Pendidikan & Pengajaran, Bidang Penelitian dan Bidang PKM?',
                    'Apakah susunan Pelaksana Akademik telah sesuai (Ketua, Wakil Ketua I, Wakil Ketua II, Wakil Ketua III, Kepala Bidang Akademik, Kepala Bidang Keuangan, Ketua Program Studi, Ketua Lembaga (P3M, PSBM), Ketua UPT (Bahasa, Perpustakaan, Pangkalan Data))?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST39 — Standar Pelaksanaan Tata Pamong & Tata Kelola */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST39',
                'name' => 'Standar Pelaksanaan Tata Pamong & Tata Kelola',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III',
                'questions' => [
                    'Apakah STIT Hidayatunnajah Bekasi telah melakukan identifikasi resiko dalam pengelolaan institusi?',
                    'Apakah identifikasi resiko mencakup seluruh aspek pengelolaan (akademik dan non-akademik)?',
                    'Apakah STIT Hidayatunnajah Bekasi melakukan pengukuran tingkat resiko terhadap resiko yang telah diidentifikasi?',
                    'Apakah terdapat kriteria atau skala dalam pengukuran tingkat resiko?',
                    'Apakah STIT Hidayatunnajah Bekasi menyusun rencana pengendalian atas resiko yang telah diukur?',
                    'Apakah rencana pengendalian resiko terintegrasi dalam Rencana Strategis STIT Hidayatunnajah Bekasi?',
                    'Apakah dilakukan evaluasi terhadap pelaksanaan rencana pengendalian resiko?',
                    'Apakah hasil evaluasi digunakan untuk perbaikan pengelolaan?',
                    'Apakah pengelolaan institusi mencerminkan kredibilitas sesuai Statuta STIT Hidayatunnajah Bekasi?',
                    'Apakah terdapat keterbukaan informasi dalam pengelolaan sesuai ketentuan?',
                    'Apakah pengelolaan dapat dipertanggungjawabkan sesuai tugas dan fungsi dalam SOTK STIT Hidayatunnajah Bekasi?',
                    'Apakah setiap unit melaksanakan tanggung jawab sesuai Peraturan Kepegawaian STIT Hidayatunnajah Bekasi?',
                    'Apakah pengelolaan dilakukan secara berkeadilan dalam penempatan dan perlakuan terhadap pegawai?',
                    'Apakah perencanaan dilakukan mengacu pada Rencana Strategis STIT Hidayatunnajah Bekasi?',
                    'Apakah pengorganisasian mengacu pada Struktur Organisasi dan Tugas Pengelola (SOTK) STIT Hidayatunnajah Bekasi?',
                    'Apakah penempatan personil sesuai dengan Peraturan Kepegawaian STIT Hidayatunnajah Bekasi?',
                    'Apakah pimpinan melakukan pengarahan dalam pelaksanaan tugas sesuai struktur organisasi?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST40 — Standar Tata Pamong Administrasi & Unit Teknis */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST40',
                'name' => 'Standar Tata Pamong Administrasi & Unit Teknis',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III, Ketua Prodi PGMI, Ketua Prodi PBA, Ka. P4M, Ka. UPT Bahasa & Asrama',
                'questions' => [
                    'Apakah Tata Pamong dan Tata Kelola pelaksanaan administrasi berjalan dengan mengedepankan prinsip-prinsip Good University Governance bernafaskan nilai-nilai Islam?',
                    'Apakah tersedia Bagian Akademik dan Karier Dosen dalam struktur organisasi?',
                    'Apakah tersedia Bagian Pengembangan Sistem Informasi dan Teknologi dalam struktur organisasi?',
                    'Apakah tersedia Bagian Keuangan dalam struktur organisasi?',
                    'Apakah tersedia Perencanaan dan Pengembangan Kelembagaan dalam struktur organisasi?',
                    'Apakah tersedia Bagian Pengembangan Sumber Daya Manusia dan Umum dalam struktur organisasi?',
                    'Apakah tersedia Bagian Kemahasiswaan, Alumni, dan Pengembangan Ruhul Islam dalam struktur organisasi?',
                    'Apakah tersedia Bagian Kerja Sama dalam struktur organisasi?',
                    'Apakah tersedia Bagian Komunikasi, Informasi dan Promosi dalam struktur organisasi?',
                    'Apakah di bawah bagian-bagian terdapat seksi-seksi sesuai struktur?',
                    'Apakah Bagian Administrasi Akademik dan Karier Dosen membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Pengembangan Sumberdaya Manusia dan Administrasi Umum membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Kerja Sama membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Keuangan membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Perencanaan dan Pengembangan Kelembagaan membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Kerja Sama, Alumni dan Peningkatan Ruhul Islam membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Pengembangan Sistem Informasi dan Teknologi membawahi seksi sesuai ketentuan?',
                    'Apakah Bagian Administrasi Komunikasi, Informasi dan Promosi membawahi seksi sesuai ketentuan?',
                    'Apakah Tata Pamong dan Tata Kelola pelaksanaan pelayanan teknis berjalan sesuai prinsip GUG bernafaskan nilai-nilai Islam?',
                    'Apakah Unit Teknis berfungsi sebagai penunjang kegiatan Tridarma STIT Hidayatunnajah Bekasi?',
                    'Apakah UPT Perpustakaan membawahi seksi sesuai ketentuan?',
                    'Apakah UPT Bahasa membawahi seksi sesuai ketentuan?',
                    'Apakah UPT Pelayanan Kesehatan membawahi seksi sesuai ketentuan?',
                ],
            ],

            /* ------------------------------------------------------------------ */
            /* ST41 — Standar Tupoksi & Jobdesk Tata Pamong */
            /* ------------------------------------------------------------------ */
            [
                'code' => 'ST41',
                'name' => 'Standar Tupoksi & Jobdesk Tata Pamong',
                'bidang' => 'WK1',
                'auditi' => 'Ketua, Waket I, Waket II, Waket III, Ketua Prodi PGMI, Ketua Prodi PBA',
                'questions' => [
                    'Apakah tersedia pedoman Tupoksi dan JobDesc di Tingkat Sekolah Tinggi STIT Hidayatunnajah Bekasi?',
                    'Apakah Tupoksi dan JobDesc di Tingkat Sekolah Tinggi dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman Tupoksi dan JobDesc di Tingkat Program Studi?',
                    'Apakah Tupoksi dan JobDesc di Program Studi dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman Tupoksi dan JobDesc pada P3M?',
                    'Apakah Tupoksi dan JobDesc P3M dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman Tupoksi dan JobDesc pada PSBPM?',
                    'Apakah Tupoksi dan JobDesc PSBPM dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman Tupoksi dan JobDesc pada Unit Pelaksana Administrasi?',
                    'Apakah Tupoksi dan JobDesc Unit Pelaksana Administrasi dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia pedoman Tupoksi dan JobDesc pada Unit Pelaksana Teknis?',
                    'Apakah Tupoksi dan JobDesc Unit Pelaksana Teknis dilaksanakan sesuai ketentuan?',
                    'Apakah tersedia SOP di Tingkat Sekolah Tinggi yang mengatur tata pamong dan tata kelola?',
                    'Apakah SOP di Tingkat Sekolah Tinggi dilaksanakan secara konsisten?',
                    'Apakah tersedia SOP di Tingkat Program Studi?',
                    'Apakah SOP Program Studi dilaksanakan secara konsisten?',
                    'Apakah tersedia SOP pada P3M?',
                    'Apakah SOP P3M dilaksanakan secara konsisten?',
                    'Apakah tersedia SOP pada PSBPM?',
                    'Apakah SOP PSBPM dilaksanakan secara konsisten?',
                    'Apakah tersedia SOP pada Unit Pelaksana Administrasi?',
                    'Apakah SOP Unit Pelaksana Administrasi dilaksanakan secara konsisten?',
                    'Apakah tersedia SOP pada Unit Pelaksana Teknis?',
                    'Apakah SOP Unit Pelaksana Teknis dilaksanakan secara konsisten?',
                ],
            ],
        ];
    }
}
