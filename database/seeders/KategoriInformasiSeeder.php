<?php

namespace Database\Seeders;

use App\Models\KategoriInformasi;
use Illuminate\Database\Seeder;

class KategoriInformasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriList = [
            [
                'nama' => 'Informasi Berkala',
                'slug' => 'berkala',
                'deskripsi' => 'Informasi publik yang wajib disediakan dan diumumkan secara berkala sekurang-kurangnya 6 bulan sekali.',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'nama' => 'Informasi Serta Merta',
                'slug' => 'serta_merta',
                'deskripsi' => 'Informasi yang dapat mengancam hajat hidup orang banyak dan ketertiban umum serta wajib diumumkan segera tanpa penundaan.',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'nama' => 'Informasi Setiap Saat',
                'slug' => 'setiap_saat',
                'deskripsi' => 'Informasi publik yang wajib disediakan oleh Badan Publik dan siap tersedia setiap saat diminta oleh pemohon informasi.',
                'urutan' => 3,
                'is_active' => true,
            ],
            [
                'nama' => 'Informasi Dikecualikan',
                'slug' => 'dikecualikan',
                'deskripsi' => 'Informasi yang bersifat ketat dan terbatas setelah melalui pengujian konsekuensi sesuai Pasal 17 UU No. 14 Tahun 2008.',
                'urutan' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($kategoriList as $item) {
            KategoriInformasi::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
