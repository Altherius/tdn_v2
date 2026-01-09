<?php

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('can render create game page', function () {
    $user = User::factory()->create();
    Team::factory()->count(3)->create();
    Tournament::factory()->count(2)->create();

    $response = $this->actingAs($user)->get('/games/new');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Games/Create')
        ->has('teams', 3)
        ->has('tournaments', 2)
    );
});

it('can create a game with all scores', function () {
    $user = User::factory()->create();
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $tournament = Tournament::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'tournament_id' => $tournament->id,
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'leg1_team1_score' => 2,
        'leg1_team2_score' => 1,
        'leg2_team1_score' => 1,
        'leg2_team2_score' => 3,
    ]);

    $game = Game::first();
    expect($game)->not->toBeNull();
    expect($game->team1_id)->toBe($team1->id);
    expect($game->team2_id)->toBe($team2->id);
    expect($game->tournament_id)->toBe($tournament->id);
    expect($game->leg1_team1_score)->toBe(2);
    expect($game->leg1_team2_score)->toBe(1);
    expect($game->leg2_team1_score)->toBe(1);
    expect($game->leg2_team2_score)->toBe(3);

    $response->assertRedirect('/games/new');
    $response->assertSessionHas('success', 'Match créé avec succès.');
});

it('can create a game without tournament', function () {
    $user = User::factory()->create();
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'tournament_id' => null,
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'leg1_team1_score' => 0,
        'leg1_team2_score' => 0,
        'leg2_team1_score' => 0,
        'leg2_team2_score' => 0,
    ]);

    $game = Game::first();
    expect($game)->not->toBeNull();
    expect($game->tournament_id)->toBeNull();

    $response->assertRedirect('/games/new');
});

it('validates required fields when creating a game', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/games', []);

    $response->assertSessionHasErrors([
        'team1_id',
        'team2_id',
        'leg1_team1_score',
        'leg1_team2_score',
        'leg2_team1_score',
        'leg2_team2_score',
    ]);
});

it('validates teams exist when creating a game', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'team1_id' => 999,
        'team2_id' => 998,
        'leg1_team1_score' => 1,
        'leg1_team2_score' => 1,
        'leg2_team1_score' => 1,
        'leg2_team2_score' => 1,
    ]);

    $response->assertSessionHasErrors(['team1_id', 'team2_id']);
});

it('validates teams are different when creating a game', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'team1_id' => $team->id,
        'team2_id' => $team->id,
        'leg1_team1_score' => 1,
        'leg1_team2_score' => 1,
        'leg2_team1_score' => 1,
        'leg2_team2_score' => 1,
    ]);

    $response->assertSessionHasErrors(['team1_id', 'team2_id']);
});

it('validates scores are non-negative integers', function () {
    $user = User::factory()->create();
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'leg1_team1_score' => -1,
        'leg1_team2_score' => 1,
        'leg2_team1_score' => 1,
        'leg2_team2_score' => 1,
    ]);

    $response->assertSessionHasErrors(['leg1_team1_score']);
});

it('validates tournament exists when provided', function () {
    $user = User::factory()->create();
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();

    $response = $this->actingAs($user)->post('/games', [
        'tournament_id' => 999,
        'team1_id' => $team1->id,
        'team2_id' => $team2->id,
        'leg1_team1_score' => 1,
        'leg1_team2_score' => 1,
        'leg2_team1_score' => 1,
        'leg2_team2_score' => 1,
    ]);

    $response->assertSessionHasErrors(['tournament_id']);
});

it('excludes over tournaments from create game page', function () {
    $user = User::factory()->create();
    Tournament::factory()->count(2)->create();
    Tournament::factory()->over()->create();

    $response = $this->actingAs($user)->get('/games/new');

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Games/Create')
        ->has('tournaments', 2)
    );
});

it('redirects guests to login when accessing game create', function () {
    $response = $this->get('/games/new');

    $response->assertRedirect('/login');
});
