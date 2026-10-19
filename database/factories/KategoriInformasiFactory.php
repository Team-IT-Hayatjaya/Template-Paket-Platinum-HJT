<?php

namespace Database\Factories;

use App\Models\KategoriInformasi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriInformasi>
 */
class KategoriInformasiFactory extends Factory
{
    protected $model = KategoriInformasi::class;

    public function definition(): array
    {
        $nama = $this->faker->words(2, true);
        return [
            'nama' => ucfirst($nama),
            'slug' => Str::slug($nama) . '-' . Str::random(4),
            'deskripsi' => $this->faker->sentence(),
            'urutan' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
