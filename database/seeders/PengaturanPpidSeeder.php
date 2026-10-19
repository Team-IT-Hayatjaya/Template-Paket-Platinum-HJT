<?php

namespace Database\Seeders;

use App\Models\PengaturanPpid;
use Illuminate\Database\Seeder;

class PengaturanPpidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengaturanPpid::firstOrCreate(
            ['id' => 1],
            [
                'kode_instansi' => 'KEC-LK',
                'nama_instansi' => 'Pemerintah Kabupaten / Kota',
                'sub_nama_instansi' => 'Dinas Komunikasi dan Informatika',
                'nama_ppid' => 'PPID Utama Pemerintah Daerah',
                'warna_utama' => '#1e3a8a',
                'warna_sekunder' => '#b91c1c',
                'alamat' => 'Gedung Sekretariat Daerah Lt. 2, Jl. Pahlawan No. 45, Kompleks Perkantoran Pemerintah',
                'telepon' => '(021) 1234-5678',
                'email' => 'ppid@instansi.go.id',
                'email_atasan_ppid' => 'atasan.ppid@instansi.go.id',
                'jam_layanan' => "Senin s.d. Kamis: 08.00 - 15.30 WIB\nJumat: 08.00 - 15.00 WIB",
                'maklumat_pelayanan' => 'Kami berkomitmen memberikan pelayanan informasi publik dengan sungguh-sungguh, transparan, cepat, tepat waktu, dan tanpa diskriminasi demi terwujudnya keterbukaan informasi di lingkungan instansi kami.',
            ]
        );
    }
}
