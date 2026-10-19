<?php

namespace Database\Factories;

use App\Models\Keberatan;
use App\Models\PermohonanInformasi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Keberatan>
 */
class KeberatanFactory extends Factory
{
    protected $model = Keberatan::class;

    public function definition(): array
    {
        $nomorReg = 'PPID/' . date('Y/m') . '/' . Str::padLeft($this->faker->numberBetween(1, 9999), 4, '0');
        return [
            'nomor_keberatan' => 'KBR/' . date('Y/m') . '/' . Str::padLeft($this->faker->unique()->numberBetween(1, 9999), 4, '0'),
            'permohonan_id' => PermohonanInformasi::factory(),
            'nomor_registrasi_permohonan' => $nomorReg,
            'nama_pemohon' => $this->faker->name(),
            'telepon' => '08' . $this->faker->numerify('##########'),
            'email' => $this->faker->safeEmail(),
            'alasan_keberatan' => 'Permohonan informasi tidak ditanggapi',
            'uraian_keberatan' => $this->faker->paragraph(),
            'status' => 'diterima',
            'catatan_internal' => null,
        ];
    }
}
