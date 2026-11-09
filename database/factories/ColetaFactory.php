<?php

namespace Database\Factories;

use App\Models\Coleta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coleta>
 */
class ColetaFactory extends Factory
{
    protected $model = Coleta::class;

    public function definition(): array
    {
        return [
            'doador_id' => User::factory(),
            'coletor_id' => User::factory(),
            'post_id' => null,
            'item' => fake()->words(2, true),
            'data_hora' => now(),
        ];
    }
}
