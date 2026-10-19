<?php

namespace Database\Factories;

use App\Models\StatusPermohonan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StatusPermohonan>
 */
class StatusPermohonanFactory extends Factory
{
    protected $model = StatusPermohonan::class;

    public function definition(): array
    {
        $nama = $this->faker->words(1, true);
        return [
            'nama' => ucfirst($nama),
            'slug' => Str::slug($nama) . '-' . Str::random(4),
            'deskripsi' => $this->faker->sentence(),
            'warna' => $this->faker->hexColor(),
            'urutan' => $this->faker->numberBetween(1, 10),
        ];
    }
}
