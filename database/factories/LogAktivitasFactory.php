<?php

namespace Database\Factories;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogAktivitas>
 */
class LogAktivitasFactory extends Factory
{
    protected $model = LogAktivitas::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'aktivitas' => $this->faker->randomElement(['create', 'update', 'delete', 'login']),
            'deskripsi' => $this->faker->sentence(),
            'tipe_subjek' => 'App\Models\DokumenInformasi',
            'subjek_id' => $this->faker->numberBetween(1, 100),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'properties' => ['action' => 'test_audit'],
            'created_at' => now(),
        ];
    }
}
