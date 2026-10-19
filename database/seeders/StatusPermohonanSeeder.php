<?php

namespace Database\Seeders;

use App\Models\StatusPermohonan;
use Illuminate\Database\Seeder;

class StatusPermohonanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusList = [
            [
                'nama' => 'Diterima',
                'slug' => 'diterima',
                'deskripsi' => 'Permohonan informasi telah masuk ke sistem dan tercatat secara resmi.',
                'warna' => '#3b82f6',
                'urutan' => 1,
            ],
            [
                'nama' => 'Diverifikasi',
                'slug' => 'diverifikasi',
                'deskripsi' => 'Kelengkapan berkas identitas dan permohonan telah diverifikasi oleh petugas PPID.',
                'warna' => '#6366f1',
                'urutan' => 2,
            ],
            [
                'nama' => 'Diproses',
                'slug' => 'diproses',
                'deskripsi' => 'Dokumen informasi sedang dikoordinasikan dengan unit perangkat daerah terkait.',
                'warna' => '#eab308',
                'urutan' => 3,
            ],
            [
                'nama' => 'Dijawab',
                'slug' => 'dijawab',
                'deskripsi' => 'Tanggapan dan dokumen informasi resmi telah dikirimkan kepada pemohon di luar sistem (email/surat).',
                'warna' => '#10b981',
                'urutan' => 4,
            ],
            [
                'nama' => 'Ditolak',
                'slug' => 'ditolak',
                'deskripsi' => 'Permohonan ditolak karena termasuk informasi yang dikecualikan atau tidak dikuasai badan publik.',
                'warna' => '#ef4444',
                'urutan' => 5,
            ],
            [
                'nama' => 'Selesai',
                'slug' => 'selesai',
                'deskripsi' => 'Proses pelayanan permohonan informasi telah selesai seluruhnya.',
                'warna' => '#64748b',
                'urutan' => 6,
            ],
        ];

        foreach ($statusList as $item) {
            StatusPermohonan::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
