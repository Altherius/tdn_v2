<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team1_id' => Team::factory(),
            'team2_id' => Team::factory(),
            'leg1_team1_score' => null,
            'leg1_team2_score' => null,
            'leg2_team1_score' => null,
            'leg2_team2_score' => null,
        ];
    }

    public function played(): static
    {
        return $this->state(fn (array $attributes) => [
            'leg1_team1_score' => fake()->numberBetween(0, 5),
            'leg1_team2_score' => fake()->numberBetween(0, 5),
            'leg2_team1_score' => fake()->numberBetween(0, 5),
            'leg2_team2_score' => fake()->numberBetween(0, 5),
        ]);
    }
}
