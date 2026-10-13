<?php

namespace Database\Factories;

use App\Enums\StatusDoacao;
use App\Models\Doacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doacao>
 */
class DoacaoFactory extends Factory
{
    protected $model = Doacao::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nome' => fake()->words(3, true),
            'descricao' => fake()->sentence(),
            'status' => StatusDoacao::Disponivel,
        ];
    }

    public function reservada(?User $coletor = null): static
    {
        return $this->state(fn () => [
            'coletor_id' => $coletor !== null ? $coletor->id : User::factory(),
            'status' => StatusDoacao::Reservado,
            'reservado_em' => now(),
        ]);
    }

    public function coletada(?User $coletor = null): static
    {
        return $this->state(fn () => [
            'coletor_id' => $coletor !== null ? $coletor->id : User::factory(),
            'status' => StatusDoacao::Coletado,
            'reservado_em' => now()->subHour(),
            'coletado_em' => now(),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn () => ['status' => StatusDoacao::Cancelado]);
    }
}
