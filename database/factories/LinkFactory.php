<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::random(7),
            'destination' => fake()->url(),
        ];
    }

    public function visited(int $visits = 5): static
    {
        return $this->state(fn (): array => [
            'visits' => $visits,
            'last_visited_at' => now(),
        ]);
    }
}
