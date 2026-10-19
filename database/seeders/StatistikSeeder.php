<?php

namespace Database\Seeders;

use App\Models\Statistik;
use Illuminate\Database\Seeder;

class StatistikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statistikList = [
            [
                'judul' => 'Data Kependudukan Menurut Jenis Kelamin dan Jumlah Kepala Keluarga',
                'slug' => 'data-kependudukan-jenis-kelamin',
                'kategori' => 'Kependudukan',
                'periode' => 'Semester I 2026',
                'tahun' => 2026,
                'sumber' => 'BPS Kabupaten & Data Konsolidasi SIAK Dukcapil',
                'isi' => '<table class="table-auto w-full border-collapse border border-slate-300">
<thead>
<tr class="bg-slate-100">
<th class="border border-slate-300 p-2 text-left">Wilayah Desa / Kelurahan</th>
<th class="border border-slate-300 p-2 text-right">Laki-laki</th>
<th class="border border-slate-300 p-2 text-right">Perempuan</th>
<th class="border border-slate-300 p-2 text-right">Total Penduduk</th>
<th class="border border-slate-300 p-2 text-right">Jumlah KK</th>
</tr>
</thead>
<tbody>
<tr><td class="border border-slate-300 p-2">Loa Kulu Kota</td><td class="border border-slate-300 p-2 text-right">6.240</td><td class="border border-slate-300 p-2 text-right">5.980</td><td class="border border-slate-300 p-2 text-right">12.220</td><td class="border border-slate-300 p-2 text-right">3.810</td></tr>
<tr><td class="border border-slate-300 p-2">Loh Sumber</td><td class="border border-slate-300 p-2 text-right">3.410</td><td class="border border-slate-300 p-2 text-right">3.250</td><td class="border border-slate-300 p-2 text-right">6.660</td><td class="border border-slate-300 p-2 text-right">2.015</td></tr>
<tr><td class="border border-slate-300 p-2">Ponoragan</td><td class="border border-slate-300 p-2 text-right">4.120</td><td class="border border-slate-300 p-2 text-right">3.980</td><td class="border border-slate-300 p-2 text-right">8.100</td><td class="border border-slate-300 p-2 text-right">2.490</td></tr>
<tr><td class="border border-slate-300 p-2">Rempanga</td><td class="border border-slate-300 p-2 text-right">2.870</td><td class="border border-slate-300 p-2 text-right">2.740</td><td class="border border-slate-300 p-2 text-right">5.610</td><td class="border border-slate-300 p-2 text-right">1.730</td></tr>
<tr><td class="border border-slate-300 p-2">Sepakat</td><td class="border border-slate-300 p-2 text-right">3.650</td><td class="border border-slate-300 p-2 text-right">3.510</td><td class="border border-slate-300 p-2 text-right">7.160</td><td class="border border-slate-300 p-2 text-right">2.180</td></tr>
<tr class="font-bold bg-slate-50"><td class="border border-slate-300 p-2">Total Kecamatan</td><td class="border border-slate-300 p-2 text-right">20.290</td><td class="border border-slate-300 p-2 text-right">19.460</td><td class="border border-slate-300 p-2 text-right">39.750</td><td class="border border-slate-300 p-2 text-right">12.225</td></tr>
</tbody>
</table>',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Data Sarana Prasarana Sekolah dan Lembaga Pendidikan Formal',
                'slug' => 'data-sarana-pendidikan',
                'kategori' => 'Pendidikan',
                'periode' => 'Tahun Ajaran 2025/2026',
                'tahun' => 2025,
                'sumber' => 'Dinas Pendidikan & Kebudayaan Kabupaten',
                'isi' => '<table class="table-auto w-full border-collapse border border-slate-300">
<thead>
<tr class="bg-slate-100">
<th class="border border-slate-300 p-2 text-left">Jenjang Pendidikan</th>
<th class="border border-slate-300 p-2 text-right">Negeri</th>
<th class="border border-slate-300 p-2 text-right">Swasta</th>
<th class="border border-slate-300 p-2 text-right">Total Sekolah</th>
<th class="border border-slate-300 p-2 text-right">Total Guru</th>
<th class="border border-slate-300 p-2 text-right">Total Siswa</th>
</tr>
</thead>
<tbody>
<tr><td class="border border-slate-300 p-2">PAUD / TK / RA</td><td class="border border-slate-300 p-2 text-right">4</td><td class="border border-slate-300 p-2 text-right">18</td><td class="border border-slate-300 p-2 text-right">22</td><td class="border border-slate-300 p-2 text-right">86</td><td class="border border-slate-300 p-2 text-right">980</td></tr>
<tr><td class="border border-slate-300 p-2">Sekolah Dasar (SD/MI)</td><td class="border border-slate-300 p-2 text-right">16</td><td class="border border-slate-300 p-2 text-right">5</td><td class="border border-slate-300 p-2 text-right">21</td><td class="border border-slate-300 p-2 text-right">240</td><td class="border border-slate-300 p-2 text-right">4.620</td></tr>
<tr><td class="border border-slate-300 p-2">SMP / MTs</td><td class="border border-slate-300 p-2 text-right">6</td><td class="border border-slate-300 p-2 text-right">3</td><td class="border border-slate-300 p-2 text-right">9</td><td class="border border-slate-300 p-2 text-right">135</td><td class="border border-slate-300 p-2 text-right">2.310</td></tr>
<tr><td class="border border-slate-300 p-2">SMA / SMK / MA</td><td class="border border-slate-300 p-2 text-right">3</td><td class="border border-slate-300 p-2 text-right">2</td><td class="border border-slate-300 p-2 text-right">5</td><td class="border border-slate-300 p-2 text-right">112</td><td class="border border-slate-300 p-2 text-right">1.840</td></tr>
<tr class="font-bold bg-slate-50"><td class="border border-slate-300 p-2">Total Seluruh Jenjang</td><td class="border border-slate-300 p-2 text-right">29</td><td class="border border-slate-300 p-2 text-right">28</td><td class="border border-slate-300 p-2 text-right">57</td><td class="border border-slate-300 p-2 text-right">573</td><td class="border border-slate-300 p-2 text-right">9.750</td></tr>
</tbody>
</table>',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Data Fasilitas Kesehatan, Tenaga Medis, dan Posyandu Aktif',
                'slug' => 'data-fasilitas-kesehatan',
                'kategori' => 'Kesehatan',
                'periode' => 'Tahun 2026',
                'tahun' => 2026,
                'sumber' => 'UPTD Puskesmas & Dinas Kesehatan',
                'isi' => '<table class="table-auto w-full border-collapse border border-slate-300">
<thead>
<tr class="bg-slate-100">
<th class="border border-slate-300 p-2 text-left">Nama Fasilitas / Sarana</th>
<th class="border border-slate-300 p-2 text-right">Jumlah Unit</th>
<th class="border border-slate-300 p-2 text-left">Status Operasional</th>
<th class="border border-slate-300 p-2 text-left">Keterangan</th>
</tr>
</thead>
<tbody>
<tr><td class="border border-slate-300 p-2">Puskesmas Rawat Inap</td><td class="border border-slate-300 p-2 text-right">1</td><td class="border border-slate-300 p-2">Aktif 24 Jam</td><td class="border border-slate-300 p-2">UGD & VK Bersalin</td></tr>
<tr><td class="border border-slate-300 p-2">Puskesmas Pembantu (Pustu)</td><td class="border border-slate-300 p-2 text-right">5</td><td class="border border-slate-300 p-2">Aktif Hari Kerja</td><td class="border border-slate-300 p-2">Pelayanan Dasar Desa</td></tr>
<tr><td class="border border-slate-300 p-2">Poskesdes / Polindes</td><td class="border border-slate-300 p-2 text-right">4</td><td class="border border-slate-300 p-2">Aktif</td><td class="border border-slate-300 p-2">Bidan Desa Siaga</td></tr>
<tr><td class="border border-slate-300 p-2">Posyandu Balita & Lansia</td><td class="border border-slate-300 p-2 text-right">38</td><td class="border border-slate-300 p-2">Aktif Rutin Bulanan</td><td class="border border-slate-300 p-2">Pencegahan Stunting</td></tr>
<tr><td class="border border-slate-300 p-2">Apotek & Klinik Swasta</td><td class="border border-slate-300 p-2 text-right">6</td><td class="border border-slate-300 p-2">Aktif</td><td class="border border-slate-300 p-2">Mitra BPJS Kesehatan</td></tr>
</tbody>
</table>',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'judul' => 'Data Luas Panen dan Produksi Tanaman Pangan & Perkebunan Rakyat',
                'slug' => 'data-produksi-pertanian',
                'kategori' => 'Pertanian & Perikanan',
                'periode' => 'Tahun 2025',
                'tahun' => 2025,
                'sumber' => 'Balai Penyuluhan Pertanian (BPP) Kecamatan',
                'isi' => '<table class="table-auto w-full border-collapse border border-slate-300">
<thead>
<tr class="bg-slate-100">
<th class="border border-slate-300 p-2 text-left">Komoditas Utama</th>
<th class="border border-slate-300 p-2 text-right">Luas Tanam (Ha)</th>
<th class="border border-slate-300 p-2 text-right">Luas Panen (Ha)</th>
<th class="border border-slate-300 p-2 text-right">Produktivitas (Ton/Ha)</th>
<th class="border border-slate-300 p-2 text-right">Total Produksi (Ton)</th>
</tr>
</thead>
<tbody>
<tr><td class="border border-slate-300 p-2">Padi Sawah</td><td class="border border-slate-300 p-2 text-right">840</td><td class="border border-slate-300 p-2 text-right">815</td><td class="border border-slate-300 p-2 text-right">5.2</td><td class="border border-slate-300 p-2 text-right">4.238</td></tr>
<tr><td class="border border-slate-300 p-2">Jagung Hibrida</td><td class="border border-slate-300 p-2 text-right">320</td><td class="border border-slate-300 p-2 text-right">310</td><td class="border border-slate-300 p-2 text-right">4.8</td><td class="border border-slate-300 p-2 text-right">1.488</td></tr>
<tr><td class="border border-slate-300 p-2">Kelapa Sawit Rakyat</td><td class="border border-slate-300 p-2 text-right">1.250</td><td class="border border-slate-300 p-2 text-right">1.180</td><td class="border border-slate-300 p-2 text-right">18.5</td><td class="border border-slate-300 p-2 text-right">21.830</td></tr>
<tr><td class="border border-slate-300 p-2">Karet Rakyat</td><td class="border border-slate-300 p-2 text-right">410</td><td class="border border-slate-300 p-2 text-right">385</td><td class="border border-slate-300 p-2 text-right">1.6</td><td class="border border-slate-300 p-2 text-right">616</td></tr>
<tr><td class="border border-slate-300 p-2">Perikanan Budidaya Keramba</td><td class="border border-slate-300 p-2 text-right">-</td><td class="border border-slate-300 p-2 text-right">-</td><td class="border border-slate-300 p-2 text-right">-</td><td class="border border-slate-300 p-2 text-right">895</td></tr>
</tbody>
</table>',
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'judul' => 'Data Realisasi Penerima Manfaat Program Bantuan Sosial',
                'slug' => 'data-bantuan-sosial',
                'kategori' => 'Sosial & Ekonomi',
                'periode' => 'Tahun 2026',
                'tahun' => 2026,
                'sumber' => 'Kementerian Sosial & Seksi Kesejahteraan Sosial',
                'isi' => '<table class="table-auto w-full border-collapse border border-slate-300">
<thead>
<tr class="bg-slate-100">
<th class="border border-slate-300 p-2 text-left">Nama Program Bantuan Sosial</th>
<th class="border border-slate-300 p-2 text-right">Keluarga Penerima Manfaat (KPM)</th>
<th class="border border-slate-300 p-2 text-left">Mekanisme Penyaluran</th>
<th class="border border-slate-300 p-2 text-left">Status Penyaluran</th>
</tr>
</thead>
<tbody>
<tr><td class="border border-slate-300 p-2">Program Keluarga Harapan (PKH)</td><td class="border border-slate-300 p-2 text-right">1.240 KPM</td><td class="border border-slate-300 p-2">Rekening Bank Himbara</td><td class="border border-slate-300 p-2">Tahap 1 Tersalurkan</td></tr>
<tr><td class="border border-slate-300 p-2">Bantuan Pangan Non Tunai (BPNT / Sembako)</td><td class="border border-slate-300 p-2 text-right">1.890 KPM</td><td class="border border-slate-300 p-2">Kartu KKS / PT Pos</td><td class="border border-slate-300 p-2">Rutin Setiap Bulan</td></tr>
<tr><td class="border border-slate-300 p-2">Bantuan Langsung Tunai Dana Desa (BLT-DD)</td><td class="border border-slate-300 p-2 text-right">620 KPM</td><td class="border border-slate-300 p-2">Pemerintah Desa Setempat</td><td class="border border-slate-300 p-2">Triwulan I Selesai</td></tr>
<tr><td class="border border-slate-300 p-2">Bantuan Cadangan Beras Pemerintah (CBP)</td><td class="border border-slate-300 p-2 text-right">2.150 KPM</td><td class="border border-slate-300 p-2">Bulog & Kantor Pos</td><td class="border border-slate-300 p-2">Alokasi Berjalan</td></tr>
</tbody>
</table>',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($statistikList as $item) {
            Statistik::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
