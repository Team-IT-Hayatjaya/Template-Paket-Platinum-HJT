<?php

namespace Database\Seeders;

use App\Models\Halaman;
use Illuminate\Database\Seeder;

class HalamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halamanList = [
            [
                'judul' => 'Profil Pejabat Pengelola PPID',
                'slug' => 'profil-pejabat',
                'ringkasan' => 'Susunan pimpinan, atasan PPID, dan profil pejabat badan publik.',
                'isi' => '<h2>Profil Pejabat Pengelola PPID</h2>
<p>Berdasarkan Keputusan Pimpinan Instansi, Pejabat Pengelola Informasi dan Dokumentasi (PPID) bertanggung jawab mengkoordinasikan penyimpanan, pendokumentasian, penyediaan, dan pelayanan informasi publik.</p>
<h3>Atasan PPID</h3>
<p><strong>Nama:</strong> H. Muhammad Rifki, S.STP, M.Si<br>
<strong>Jabatan:</strong> Pimpinan Instansi / Camat<br>
<strong>Tugas:</strong> Mengawasi dan menetapkan kebijakan strategis keterbukaan informasi publik instansi.</p>
<h3>Pejabat PPID Pelaksana</h3>
<p><strong>Nama:</strong> Siti Rahmawati, S.Sos<br>
<strong>Jabatan:</strong> Sekretaris PPID<br>
<strong>Tugas:</strong> Mengelola permohonan informasi publik, mengkoordinasikan dokumentasi, dan memelihara daftar informasi publik.</p>',
                'urutan' => 1,
                'is_active' => true,
                'show_in_menu' => true,
                'meta_title' => 'Profil Pejabat Pengelola PPID Resmi',
                'meta_description' => 'Susunan pimpinan, atasan PPID, dan profil pejabat pengelola informasi dan dokumentasi.',
            ],
            [
                'judul' => 'Laporan Keuangan',
                'slug' => 'laporan-keuangan',
                'ringkasan' => 'Transparansi neraca, realisasi anggaran, dan ringkasan audit BPK.',
                'isi' => '<h2>Laporan Keuangan & Akuntabilitas Anggaran</h2>
<p>Sesuai amanat UU Keterbukaan Informasi Publik, badan publik wajib mengumumkan laporan keuangan yang telah diaudit secara transparan.</p>
<h3>Ringkasan Realisasi Anggaran</h3>
<p>Anggaran Pendapatan dan Belanja telah direalisasikan secara akuntabel untuk mendukung pelayanan dasar masyarakat dan pembangunan infrastruktur.</p>
<ul>
    <li>Laporan Realisasi Anggaran (LRA) Tahun 2025/2026: Efektivitas penyerapan mencapai 96.4%.</li>
    <li>Neraca Keuangan: Pengelolaan aset daerah terdokumentasi tertib dan teratur.</li>
    <li>Opini BPK: Memperoleh opini Wajar Tanpa Pengecualian (WTP).</li>
</ul>',
                'urutan' => 2,
                'is_active' => true,
                'show_in_menu' => true,
                'meta_title' => 'Laporan Keuangan & DPA Resmi',
                'meta_description' => 'Transparansi laporan neraca, realisasi anggaran, dan akuntabilitas keuangan publik.',
            ],
            [
                'judul' => 'Rencana Strategis',
                'slug' => 'rencana-strategis',
                'ringkasan' => 'Dokumen arah pembangunan, target kinerja, dan indikator utama.',
                'isi' => '<h2>Rencana Strategis (Renstra) Instansi</h2>
<p>Rencana Strategis merupakan pedoman arah kebijakan pembangunan, target indikator kinerja utama, dan program prioritas 5 (lima) tahunan instansi.</p>
<h3>Visi Strategis</h3>
<p>Terwujudnya pelayanan publik yang prima, transparan, inklusif, dan berdaya saing tinggi.</p>
<h3>Misi Strategis</h3>
<ol>
    <li>Meningkatkan kualitas tata kelola pemerintahan yang bersih, akuntabel, dan transparan.</li>
    <li>Mengoptimalkan pelayanan administrasi terpadu berbasis teknologi informasi.</li>
    <li>Mendorong partisipasi aktif masyarakat dalam pengawasan pembangunan daerah.</li>
</ol>',
                'urutan' => 3,
                'is_active' => true,
                'show_in_menu' => true,
                'meta_title' => 'Rencana Strategis (Renstra) Instansi',
                'meta_description' => 'Dokumen rencana strategis pembangunan, target kinerja, dan indikator keberhasilan instansi.',
            ],
            [
                'judul' => 'Visi dan Misi',
                'slug' => 'visi-misi',
                'ringkasan' => 'Visi dan misi pembangunan dan tata kelola pemerintahan.',
                'isi' => '<h2>Visi dan Misi Resmi</h2>
<p>Arah dan tujuan penyelenggaraan pemerintahan dalam melayani masyarakat secara menyeluruh.</p>',
                'urutan' => 4,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'ringkasan' => 'Bagan susunan organisasi dan pembagian tugas bidang kerja.',
                'isi' => '<h2>Struktur Organisasi</h2>
<p>Bagan organisasi tata kelola instansi dan unit kerja pembantu pimpinan.</p>',
                'urutan' => 5,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Tugas Pokok dan Fungsi',
                'slug' => 'tugas-fungsi',
                'ringkasan' => 'Rincian tugas pokok, fungsi, dan wewenang perangkat daerah.',
                'isi' => '<h2>Tugas Pokok dan Fungsi (Tupoksi)</h2>
<p>Penjabaran tugas pokok dan fungsi dalam menyelenggarakan urusan pemerintahan umum.</p>',
                'urutan' => 6,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Sejarah',
                'slug' => 'sejarah',
                'ringkasan' => 'Sejarah berdirinya dan perkembangan wilayah pemerintahan.',
                'isi' => '<h2>Sejarah dan Perkembangan Wilayah</h2>
<p>Perjalanan sejarah perkembangan wilayah dalam melayani masyarakat dan membangun kemakmuran daerah dari masa ke masa.</p>',
                'urutan' => 7,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Unit Kerja',
                'slug' => 'unit-kerja',
                'ringkasan' => 'Daftar unit kerja teknis dan seksi pelayanan masyarakat.',
                'isi' => '<h2>Unit Kerja dan Seksi Pelayanan</h2>
<p>Susunan seksi dan sub-bagian yang bertugas mengoordinasikan pelayanan perizinan, trantib, sosial kemasyarakatan, dan administrasi umum.</p>',
                'urutan' => 8,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Dasar Hukum',
                'slug' => 'dasar-hukum',
                'ringkasan' => 'Landasan hukum pembentukan, wewenang, dan regulasi daerah.',
                'isi' => '<h2>Dasar Hukum dan Landasan Regulasi</h2>
<p>Kompilasi peraturan perundang-undangan dan peraturan daerah yang menjadi landasan operasional penyelenggaraan pemerintahan dan pelayanan publik.</p>',
                'urutan' => 9,
                'is_active' => true,
                'show_in_menu' => true,
            ],
            [
                'judul' => 'Profil PPID',
                'slug' => 'profil-ppid',
                'ringkasan' => 'Profil Pejabat Pengelola Informasi dan Dokumentasi (PPID).',
                'isi' => '<h2>Profil Lembaga PPID</h2>
<p>Pejabat Pengelola Informasi dan Dokumentasi (PPID) dibentuk untuk menjamin keterbukaan informasi publik dan akuntabilitas tata kelola pemerintahan sesuai UU No. 14 Tahun 2008.</p>',
                'urutan' => 10,
                'is_active' => true,
                'show_in_menu' => true,
            ],
        ];

        foreach ($halamanList as $item) {
            Halaman::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
