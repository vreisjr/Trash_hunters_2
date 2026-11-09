<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'content'    => fake()->paragraph(),
            'post_type'  => 'feed',
            'price_type' => 'free',
            'price'      => null,
            'latitude'   => fake()->latitude(-8.2, -7.9),
            'longitude'  => fake()->longitude(-35.1, -34.8),
            'address'    => fake()->streetAddress(),
            'district'   => fake()->city(),
        ];
    }

    public function marketplace(): static
    {
        return $this->state(fn () => ['post_type' => 'marketplace']);
    }

    public function paid(float $price = 25.00): static
    {
        return $this->state(fn () => [
            'post_type'  => 'marketplace',
            'price_type' => 'paid',
            'price'      => $price,
        ]);
    }

    public function edited(): static
    {
        return $this->state(fn () => ['edited_at' => now()]);
    }
}
