<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Cup',
            'is_major' => $this->faker->boolean(30),
            'is_balancing' => $this->faker->boolean(20),
            'is_over' => false,
        ];
    }

    public function major(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_major' => true,
        ]);
    }

    public function balancing(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_balancing' => true,
        ]);
    }

    public function withPlacements(): static
    {
        return $this->state(fn (array $attributes) => [
            'winner_team_id' => Team::factory(),
            'second_place_team_id' => Team::factory(),
            'third_place_team_id' => Team::factory(),
        ]);
    }

    public function over(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_over' => true,
        ]);
    }
}
