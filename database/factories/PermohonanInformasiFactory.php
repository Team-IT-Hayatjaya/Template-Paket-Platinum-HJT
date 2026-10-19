<?php

namespace Database\Factories;

use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PermohonanInformasi>
 */
class PermohonanInformasiFactory extends Factory
{
    protected $model = PermohonanInformasi::class;

    public function definition(): array
    {
        return [
            'nomor_registrasi' => 'PPID/' . date('Y/m') . '/' . Str::padLeft($this->faker->unique()->numberBetween(1, 9999), 4, '0'),
            'status_id' => StatusPermohonan::factory(),
            'kategori_pemohon' => $this->faker->randomElement(['perorangan', 'badan_hukum', 'kelompok']),
            'nama_pemohon' => $this->faker->name(),
            'nik' => '3201' . $this->faker->numerify('############'),
            'alamat' => $this->faker->address(),
            'telepon' => '08' . $this->faker->numerify('##########'),
            'email' => $this->faker->safeEmail(),
            'identitas_file_path' => 'private/identitas/ktp_' . Str::random(12) . '.jpg',
            'surat_kuasa_file_path' => null,
            'akta_badan_hukum_file_path' => null,
            'rincian_informasi' => $this->faker->paragraph(),
            'tujuan_penggunaan' => $this->faker->sentence(),
            'cara_memperoleh_informasi' => 'salinan',
            'cara_mendapat_salinan' => 'email',
            'tenggat_jawab' => null, // Disiapkan kosong untuk paket Silver
            'tenggat_diperpanjang' => null,
            'catatan_internal' => null,
        ];
    }
}
