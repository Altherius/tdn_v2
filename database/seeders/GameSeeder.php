<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();

        if ($teams->count() < 2) {
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            $selectedTeams = $teams->random(2);

            Game::factory()->played()->create([
                'team1_id' => $selectedTeams->first()->id,
                'team2_id' => $selectedTeams->last()->id,
            ]);
        }
    }
}
