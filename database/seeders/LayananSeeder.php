<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layananList = [
            [
                'nama' => 'Rekomendasi Surat Keterangan Tidak Mampu (SKTM)',
                'slug' => 'rekomendasi-sktm',
                'ringkasan' => 'Pelayanan penerbitan surat rekomendasi keterangan tidak mampu untuk keperluan keringanan biaya pendidikan, beasiswa, atau pengobatan rumah sakit.',
                'persyaratan' => '<ul>
    <li>Surat Pengantar dari RT/RW setempat</li>
    <li>Surat Keterangan Tidak Mampu dari Kepala Desa / Lurah</li>
    <li>Fotokopi Kartu Tanda Penduduk (KTP) pemohon & orang tua</li>
    <li>Fotokopi Kartu Keluarga (KK) yang masih berlaku</li>
    <li>Surat pernyataan penghasilan orang tua bermaterai (untuk beasiswa)</li>
</ul>',
                'alur' => '<ol>
    <li>Pemohon datang ke loket Pelayanan Administrasi Terpadu (PATEN) dengan membawa berkas persyaratan lengkap.</li>
    <li>Petugas loket memverifikasi keabsahan dan kelengkapan dokumen persyaratan.</li>
    <li>Kepala Seksi Pelayanan Umum memvalidasi dan memaraf draf surat rekomendasi.</li>
    <li>Camat / Sekretaris Camat menandatangani berkas rekomendasi secara elektronik (TTE).</li>
    <li>Petugas menyerahkan surat rekomendasi SKTM yang telah dilegalisasi kepada pemohon.</li>
</ol>',
                'biaya' => 'Gratis (Rp 0)',
                'waktu_penyelesaian' => '15 - 30 Menit',
                'penanggung_jawab' => 'Seksi Kesejahteraan Sosial & Pelayanan Umum',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'nama' => 'Perekaman & Penerbitan KTP Elektronik (KTP-el)',
                'slug' => 'perekaman-ktp-el',
                'ringkasan' => 'Layanan perekaman biometrik (iris mata, sidik jari, pas foto) dan cetak ulang KTP-el bagi pemula usia 17 tahun atau penggantian KTP rusak/hilang.',
                'persyaratan' => '<ul>
    <li>Fotokopi Kartu Keluarga (KK) terbaru</li>
    <li>KTP lama (bagi penggantian KTP rusak)</li>
    <li>Surat Keterangan Kehilangan dari Kepolisian (bagi KTP hilang)</li>
    <li>Berusia minimal 17 tahun atau sudah pernah menikah</li>
</ul>',
                'alur' => '<ol>
    <li>Pemohon mengambil nomor antrean loket administrasi kependudukan.</li>
    <li>Petugas mencocokkan data pemohon dengan database SIAK Dukcapil.</li>
    <li>Pemohon melakukan perekaman biometrik (foto wajah, retina, 10 sidik jari, dan tanda tangan digital).</li>
    <li>Penerbitan surat bukti perekaman dan pencetakan fisik KTP-el.</li>
</ol>',
                'biaya' => 'Gratis (Rp 0)',
                'waktu_penyelesaian' => '1 Hari Kerja',
                'penanggung_jawab' => 'Loket Pelayanan Kependudukan PATEN',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'nama' => 'Rekomendasi Izin Usaha Mikro dan Kecil (IUMK)',
                'slug' => 'rekomendasi-iumk',
                'ringkasan' => 'Penerbitan surat rekomendasi legalitas usaha mikro dan kecil untuk mempermudah akses permodalan perbankan dan izin berusaha OSS.',
                'persyaratan' => '<ul>
    <li>Surat Pengantar RT/RW dan Kepala Desa/Lurah</li>
    <li>Fotokopi KTP dan Kartu Keluarga pemilik usaha</li>
    <li>Pas foto berwarna ukuran 4x6 sebanyak 2 lembar</li>
    <li>Foto lokasi atau tempat aktivitas usaha</li>
    <li>Mengisi formulir permohonan izin usaha mikro</li>
</ul>',
                'alur' => '<ol>
    <li>Penyerahan berkas permohonan dan foto usaha di loket PATEN.</li>
    <li>Pemeriksaan kesesuaian zonasi dan bidang usaha oleh Seksi Ekonomi Pembangunan.</li>
    <li>Verifikasi berkas dan penerbitan nomor rekomendasi.</li>
    <li>Penyerahan dokumen rekomendasi IUMK resmi kepada pelaku usaha.</li>
</ol>',
                'biaya' => 'Gratis (Rp 0)',
                'waktu_penyelesaian' => '1 Hari Kerja',
                'penanggung_jawab' => 'Seksi Pemberdayaan Masyarakat & Ekonomi Pembangunan',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'nama' => 'Surat Pengantar Dispensasi & Administrasi Nikah',
                'slug' => 'pengantar-nikah',
                'ringkasan' => 'Pelayanan rekomendasi pengantar perkawinan (Model N1-N4) serta surat dispensasi waktu pernikahan ke Kantor Urusan Agama (KUA).',
                'persyaratan' => '<ul>
    <li>Surat Pengantar dari Kepala Desa / Lurah setempat (Model N1, N2, N4)</li>
    <li>Fotokopi KTP dan KK calon mempelai serta orang tua</li>
    <li>Fotokopi Akta Kelahiran dan Ijazah Terakhir</li>
    <li>Surat Keterangan Kematian / Akta Cerai (bagi janda/duda)</li>
    <li>Pas foto berdampingan latar biru 2x3 dan 3x4</li>
</ul>',
                'alur' => '<ol>
    <li>Pemohon mendaftarkan permohonan di loket pelayanan umum.</li>
    <li>Pemeriksaan berkas kelengkapan formulir model N1-N4.</li>
    <li>Penandatanganan surat rekomendasi oleh Pejabat Pelayanan.</li>
    <li>Penyerahan surat rekomendasi pengantar pernikahan untuk diajukan ke KUA.</li>
</ol>',
                'biaya' => 'Gratis (Rp 0)',
                'waktu_penyelesaian' => '1 Hari Kerja',
                'penanggung_jawab' => 'Seksi Kesejahteraan Sosial PATEN',
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'nama' => 'Surat Keterangan Domisili Lembaga / Organisasi',
                'slug' => 'keterangan-domisili-lembaga',
                'ringkasan' => 'Penerbitan surat keterangan domisili untuk pendirian yayasan, koperasi, badan usaha, lembaga swadaya masyarakat, atau organisasi keagamaan.',
                'persyaratan' => '<ul>
    <li>Surat Pengantar RT/RW dan Surat Pengantar Desa/Kelurahan</li>
    <li>Akta Notaris Pendirian Badan Hukum / Lembaga</li>
    <li>Fotokopi KTP & KK Ketua / Penanggung Jawab Lembaga</li>
    <li>Bukti kepemilikan atau sewa tempat sekretariat / kantor</li>
    <li>Foto gedung / kantor tampak depan</li>
</ul>',
                'alur' => '<ol>
    <li>Pengajuan berkas permohonan di loket pelayanan.</li>
    <li>Verifikasi keabsahan dokumen pendirian dan pengecekan lokasi.</li>
    <li>Penandatanganan Surat Keterangan Domisili oleh Camat.</li>
    <li>Penyerahan surat keterangan domisili resmi kepada pengurus lembaga.</li>
</ol>',
                'biaya' => 'Gratis (Rp 0)',
                'waktu_penyelesaian' => '1 - 2 Hari Kerja',
                'penanggung_jawab' => 'Seksi Pemerintahan & Pelayanan Umum',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($layananList as $item) {
            Layanan::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
