<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tournament;
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
            'tournament_id' => function () {
                if ($this->faker->boolean(90)) {
                    return Tournament::inRandomOrder()->first()?->id;
                }

                return null;
            },
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

    public function withoutTournament(): static
    {
        return $this->state(fn (array $attributes) => [
            'tournament_id' => null,
        ]);
    }
}
