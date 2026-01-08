<?php

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use Inertia\Testing\AssertableInertia;

it('can create a tournament', function () {
    $tournament = Tournament::factory()->create([
        'name' => 'World Cup 2026',
        'is_major' => true,
        'is_balancing' => false,
    ]);

    expect($tournament->name)->toBe('World Cup 2026');
    expect($tournament->is_major)->toBeTrue();
    expect($tournament->is_balancing)->toBeFalse();
});

it('has winner relationship', function () {
    $team = Team::factory()->create();
    $tournament = Tournament::factory()->create([
        'winner_team_id' => $team->id,
    ]);

    expect($tournament->winner->id)->toBe($team->id);
});

it('has second place relationship', function () {
    $team = Team::factory()->create();
    $tournament = Tournament::factory()->create([
        'second_place_team_id' => $team->id,
    ]);

    expect($tournament->secondPlace->id)->toBe($team->id);
});

it('has third place relationship', function () {
    $team = Team::factory()->create();
    $tournament = Tournament::factory()->create([
        'third_place_team_id' => $team->id,
    ]);

    expect($tournament->thirdPlace->id)->toBe($team->id);
});

it('has games relationship', function () {
    $tournament = Tournament::factory()->create();
    $game = Game::factory()->create([
        'tournament_id' => $tournament->id,
    ]);

    expect($tournament->games)->toHaveCount(1);
    expect($tournament->games->first()->id)->toBe($game->id);
});

it('game belongs to tournament', function () {
    $tournament = Tournament::factory()->create();
    $game = Game::factory()->create([
        'tournament_id' => $tournament->id,
    ]);

    expect($game->tournament->id)->toBe($tournament->id);
});

it('game can have no tournament', function () {
    $game = Game::factory()->create([
        'tournament_id' => null,
    ]);

    expect($game->tournament)->toBeNull();
});

it('can have all three different team placements', function () {
    $winner = Team::factory()->create(['name' => 'Winner']);
    $second = Team::factory()->create(['name' => 'Second']);
    $third = Team::factory()->create(['name' => 'Third']);

    $tournament = Tournament::factory()->create([
        'winner_team_id' => $winner->id,
        'second_place_team_id' => $second->id,
        'third_place_team_id' => $third->id,
    ]);

    expect($tournament->winner->name)->toBe('Winner');
    expect($tournament->secondPlace->name)->toBe('Second');
    expect($tournament->thirdPlace->name)->toBe('Third');
});

it('can render generate roster page', function () {
    Team::factory()->count(5)->create();

    $response = $this->get('/tournaments/generate-roster');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Tournaments/GenerateRoster')
        ->has('teams', 5)
    );
});

it('generate roster page includes teams with regions', function () {
    $team = Team::factory()->create();

    $response = $this->get('/tournaments/generate-roster');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Tournaments/GenerateRoster')
        ->has('teams.0.region')
    );
});
