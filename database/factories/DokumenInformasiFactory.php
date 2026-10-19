<?php

namespace Database\Factories;

use App\Models\DokumenInformasi;
use App\Models\KategoriInformasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DokumenInformasi>
 */
class DokumenInformasiFactory extends Factory
{
    protected $model = DokumenInformasi::class;

    public function definition(): array
    {
        $judul = $this->faker->sentence(4);
        return [
            'kategori_id' => KategoriInformasi::factory(),
            'judul' => $judul,
            'slug' => Str::slug($judul) . '-' . Str::random(4),
            'ringkasan' => $this->faker->paragraph(),
            'tahun' => (int) $this->faker->year(),
            'file_path' => 'dokumen/sample_' . Str::random(8) . '.pdf',
            'tipe_file' => 'pdf',
            'ukuran_file' => $this->faker->randomElement(['1.2 MB', '850 KB', '2.4 MB', '5.1 MB']),
            'dilihat' => $this->faker->numberBetween(0, 500),
            'diunduh' => $this->faker->numberBetween(0, 200),
            'is_published' => true,
            'tanggal_terbit' => $this->faker->date(),
            'created_by' => User::factory(),
        ];
    }
}
