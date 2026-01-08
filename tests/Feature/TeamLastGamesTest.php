<?php

use App\Enums\GameResult;
use App\Models\Game;
use App\Models\Team;
use Inertia\Testing\AssertableInertia;

it('returns empty array when team has no games', function () {
    $team = Team::factory()->create();

    $results = $team->getLastGameResults();

    expect($results)->toBeArray();
    expect($results)->toBeEmpty();
});

it('returns win when team scores more than opponent', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Team wins: 3-1 + 2-1 = 5-2
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 3,
            'leg1_team2_score' => 1,
            'leg2_team1_score' => 2,
            'leg2_team2_score' => 1,
        ]);

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(1);
    expect($results[0])->toBe(GameResult::Win);
});

it('returns loss when team scores less than opponent', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Team loses: 1-3 + 1-2 = 2-5
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 1,
            'leg1_team2_score' => 3,
            'leg2_team1_score' => 1,
            'leg2_team2_score' => 2,
        ]);

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(1);
    expect($results[0])->toBe(GameResult::Loss);
});

it('returns draw when scores are equal', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Draw: 2-1 + 1-2 = 3-3
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 2,
            'leg1_team2_score' => 1,
            'leg2_team1_score' => 1,
            'leg2_team2_score' => 2,
        ]);

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(1);
    expect($results[0])->toBe(GameResult::Draw);
});

it('correctly calculates result when team is team2', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Team (as team2) wins: opponent 1-3, team 2-1 in leg1; opponent 1-2, team 2-1 in leg2
    // Total: opponent 2-4, team 4-2. Team wins.
    Game::factory()
        ->for($opponent, 'team1')
        ->for($team, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 1,
            'leg1_team2_score' => 3,
            'leg2_team1_score' => 1,
            'leg2_team2_score' => 2,
        ]);

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(1);
    expect($results[0])->toBe(GameResult::Win);
});

it('returns last 5 games by default', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Create 7 games
    for ($i = 0; $i < 7; $i++) {
        Game::factory()
            ->for($team, 'team1')
            ->for($opponent, 'team2')
            ->withoutTournament()
            ->create([
                'leg1_team1_score' => 3,
                'leg1_team2_score' => 1,
                'leg2_team1_score' => 2,
                'leg2_team2_score' => 1,
            ]);
    }

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(5);
});

it('can customize number of games returned', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Create 5 games
    for ($i = 0; $i < 5; $i++) {
        Game::factory()
            ->for($team, 'team1')
            ->for($opponent, 'team2')
            ->withoutTournament()
            ->create([
                'leg1_team1_score' => 3,
                'leg1_team2_score' => 1,
                'leg2_team1_score' => 2,
                'leg2_team2_score' => 1,
            ]);
    }

    $results = $team->getLastGameResults(3);

    expect($results)->toHaveCount(3);
});

it('ignores incomplete games', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Complete game
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 3,
            'leg1_team2_score' => 1,
            'leg2_team1_score' => 2,
            'leg2_team2_score' => 1,
        ]);

    // Incomplete game (no scores)
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create();

    $results = $team->getLastGameResults();

    expect($results)->toHaveCount(1);
});

it('displays last games on home page', function () {
    $team = Team::factory()->create();
    $opponent = Team::factory()->create();

    // Create a game that team wins
    Game::factory()
        ->for($team, 'team1')
        ->for($opponent, 'team2')
        ->withoutTournament()
        ->create([
            'leg1_team1_score' => 3,
            'leg1_team2_score' => 1,
            'leg2_team1_score' => 2,
            'leg2_team2_score' => 1,
        ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Welcome')
        ->has('teams', 2)
        ->where('teams.0.lastGames', ['win'])
    );
});
