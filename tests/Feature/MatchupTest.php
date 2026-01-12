<?php

use App\Models\Game;
use App\Models\Region;
use App\Models\Team;

it('can render matchup index page', function () {
    $response = $this->get(route('matchup.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Matchup/Index'));
});

it('provides teams list on matchup index page', function () {
    $region = Region::factory()->create();
    $teams = Team::factory()->count(3)->for($region)->create();

    $response = $this->get(route('matchup.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Matchup/Index')
        ->has('teams', 3)
    );
});

it('can render matchup show page', function () {
    $region = Region::factory()->create();
    $team1 = Team::factory()->for($region)->create(['elo_rating' => 1200]);
    $team2 = Team::factory()->for($region)->create(['elo_rating' => 1000]);

    $response = $this->get(route('matchup.show', [$team1, $team2]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Matchup/Show')
        ->has('team1')
        ->has('team2')
        ->has('games')
        ->has('analysis')
    );
});

it('shows games between the two teams', function () {
    $region = Region::factory()->create();
    $team1 = Team::factory()->for($region)->create();
    $team2 = Team::factory()->for($region)->create();
    $team3 = Team::factory()->for($region)->create();

    // Game between team1 and team2
    Game::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
    ]);

    // Game between team2 and team1 (reversed)
    Game::factory()->create([
        'team1_id' => $team2->id,
        'team2_id' => $team1->id,
    ]);

    // Game not involving both teams
    Game::factory()->create([
        'team1_id' => $team1->id,
        'team2_id' => $team3->id,
    ]);

    $response = $this->get(route('matchup.show', [$team1, $team2]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('games', 2)
    );
});

it('calculates win probability based on elo', function () {
    $region = Region::factory()->create();
    $team1 = Team::factory()->for($region)->create(['elo_rating' => 1200]);
    $team2 = Team::factory()->for($region)->create(['elo_rating' => 1000]);

    $response = $this->get(route('matchup.show', [$team1, $team2]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->where('analysis.team1WinProbability', fn ($value) => $value > 50)
        ->where('analysis.team2WinProbability', fn ($value) => $value < 50)
        ->has('analysis.drawProbability')
    );
});

it('provides elo gain and loss predictions', function () {
    $region = Region::factory()->create();
    $team1 = Team::factory()->for($region)->create(['elo_rating' => 1200]);
    $team2 = Team::factory()->for($region)->create(['elo_rating' => 1000]);

    $response = $this->get(route('matchup.show', [$team1, $team2]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('analysis.team1GainOnWin')
        ->has('analysis.team1LossOnLoss')
        ->has('analysis.team2GainOnWin')
        ->has('analysis.team2LossOnLoss')
    );
});
