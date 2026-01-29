<?php

use App\Models\Country;
use App\Models\EloHistory;
use App\Models\Game;
use App\Models\Region;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('can render create team page', function () {
    $user = User::factory()->create();
    Region::factory()->count(3)->create();

    $response = $this->actingAs($user)->get('/teams/new');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Teams/Create')
        ->has('regions', 3)
    );
});

it('can create a team', function () {
    $user = User::factory()->create();
    $region = Region::factory()->create();

    $response = $this->actingAs($user)->post('/teams', [
        'name' => 'New Team',
        'region_id' => $region->id,
    ]);

    $team = Team::where('name', 'New Team')->first();
    expect($team)->not->toBeNull();
    expect($team->region_id)->toBe($region->id);
    expect($team->elo_rating)->toBe(1000);

    $response->assertRedirect("/teams/{$team->id}");
});

it('validates required fields when creating a team', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/teams', []);

    $response->assertSessionHasErrors(['name', 'region_id']);
});

it('validates region exists when creating a team', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/teams', [
        'name' => 'New Team',
        'region_id' => 999,
    ]);

    $response->assertSessionHasErrors(['region_id']);
});

it('can render edit team page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Region::factory()->count(3)->create();

    $response = $this->actingAs($user)->get("/teams/edit/{$team->id}");

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Teams/Edit')
        ->has('team')
        ->where('team.id', $team->id)
        ->has('regions', 4) // 3 + the team's region
    );
});

it('can update a team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Old Name']);
    $newRegion = Region::factory()->create();

    $response = $this->actingAs($user)->put("/teams/{$team->id}", [
        'name' => 'New Name',
        'region_id' => $newRegion->id,
    ]);

    $team->refresh();
    expect($team->name)->toBe('New Name');
    expect($team->region_id)->toBe($newRegion->id);

    $response->assertRedirect("/teams/{$team->id}");
});

it('validates required fields when updating a team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $response = $this->actingAs($user)->put("/teams/{$team->id}", []);

    $response->assertSessionHasErrors(['name', 'region_id']);
});

it('does not change elo rating when updating a team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['elo_rating' => 1200]);
    $region = Region::factory()->create();

    $this->actingAs($user)->put("/teams/{$team->id}", [
        'name' => 'Updated Name',
        'region_id' => $region->id,
    ]);

    $team->refresh();
    expect($team->elo_rating)->toBe(1200);
});

it('redirects guests to login when accessing team create', function () {
    $response = $this->get('/teams/new');

    $response->assertRedirect('/login');
});

it('redirects guests to login when accessing team edit', function () {
    $team = Team::factory()->create();

    $response = $this->get("/teams/edit/{$team->id}");

    $response->assertRedirect('/login');
});

it('can render team show page with elo history including opponent country', function () {
    $country1 = Country::factory()->create(['code' => 'FR']);
    $country2 = Country::factory()->create(['code' => 'DE']);

    $team = Team::factory()->create(['country_id' => $country1->id]);
    $opponent = Team::factory()->create(['country_id' => $country2->id]);

    $game = Game::factory()->create([
        'team1_id' => $team->id,
        'team2_id' => $opponent->id,
    ]);

    EloHistory::factory()->create([
        'team_id' => $team->id,
        'game_id' => $game->id,
        'rating' => 1020,
    ]);

    $response = $this->get("/teams/{$team->id}");

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Teams/Show')
        ->has('team')
        ->has('eloHistory', 1)
        ->has('eloHistory.0.game')
        ->has('eloHistory.0.game.team2.country')
        ->where('eloHistory.0.game.team2.country.code', 'DE')
    );
});
