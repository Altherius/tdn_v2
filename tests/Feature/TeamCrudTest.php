<?php

use App\Models\Region;
use App\Models\Team;
use Inertia\Testing\AssertableInertia;

it('can render create team page', function () {
    Region::factory()->count(3)->create();

    $response = $this->get('/teams/new');

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Teams/Create')
        ->has('regions', 3)
    );
});

it('can create a team', function () {
    $region = Region::factory()->create();

    $response = $this->post('/teams', [
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
    $response = $this->post('/teams', []);

    $response->assertSessionHasErrors(['name', 'region_id']);
});

it('validates region exists when creating a team', function () {
    $response = $this->post('/teams', [
        'name' => 'New Team',
        'region_id' => 999,
    ]);

    $response->assertSessionHasErrors(['region_id']);
});

it('can render edit team page', function () {
    $team = Team::factory()->create();
    Region::factory()->count(3)->create();

    $response = $this->get("/teams/edit/{$team->id}");

    $response->assertSuccessful();
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Teams/Edit')
        ->has('team')
        ->where('team.id', $team->id)
        ->has('regions', 4) // 3 + the team's region
    );
});

it('can update a team', function () {
    $team = Team::factory()->create(['name' => 'Old Name']);
    $newRegion = Region::factory()->create();

    $response = $this->put("/teams/{$team->id}", [
        'name' => 'New Name',
        'region_id' => $newRegion->id,
    ]);

    $team->refresh();
    expect($team->name)->toBe('New Name');
    expect($team->region_id)->toBe($newRegion->id);

    $response->assertRedirect("/teams/{$team->id}");
});

it('validates required fields when updating a team', function () {
    $team = Team::factory()->create();

    $response = $this->put("/teams/{$team->id}", []);

    $response->assertSessionHasErrors(['name', 'region_id']);
});

it('does not change elo rating when updating a team', function () {
    $team = Team::factory()->create(['elo_rating' => 1200]);
    $region = Region::factory()->create();

    $this->put("/teams/{$team->id}", [
        'name' => 'Updated Name',
        'region_id' => $region->id,
    ]);

    $team->refresh();
    expect($team->elo_rating)->toBe(1200);
});
