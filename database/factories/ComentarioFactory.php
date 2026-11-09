<?php

namespace Database\Factories;

use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comentario>
 */
class ComentarioFactory extends Factory
{
    protected $model = Comentario::class;

    public function definition(): array
    {
        return [
            'post_id'   => Post::factory(),
            'user_id'   => User::factory(),
            'parent_id' => null,
            'texto'     => fake()->sentence(),
        ];
    }

    public function resposta(Comentario $pai): static
    {
        return $this->state(fn () => [
            'post_id'   => $pai->post_id,
            'parent_id' => $pai->id,
        ]);
    }
}