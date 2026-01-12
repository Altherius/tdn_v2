<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Country;
use App\Models\Region;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $teams = Team::with(['region', 'country'])
            ->withCount([
                'tournamentsWon' => fn ($query) => $query->where('is_major', true),
                'tournamentsSecondPlace' => fn ($query) => $query->where('is_major', true),
                'tournamentsThirdPlace' => fn ($query) => $query->where('is_major', true),
            ])
            ->orderByDesc('elo_rating')
            ->get()
            ->map(fn (Team $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'elo_rating' => $team->elo_rating,
                'region' => $team->region,
                'country' => $team->country,
                'lastGames' => array_map(
                    fn ($result) => $result->value,
                    $team->getLastGameResults()
                ),
                'goldCount' => $team->tournaments_won_count,
                'silverCount' => $team->tournaments_second_place_count,
                'bronzeCount' => $team->tournaments_third_place_count,
            ]);

        return Inertia::render('Welcome', [
            'teams' => $teams,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Teams/Create', [
            'regions' => Region::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        $team = Team::create($request->validated());

        return redirect()->route('teams.show', $team);
    }

    public function show(Team $team): Response
    {
        return Inertia::render('Teams/Show', [
            'team' => $team->load(['region', 'country']),
            'games' => $team->games()->with(['team1', 'team2', 'tournament'])->orderByDesc('created_at')->get(),
            'eloHistory' => $team->eloHistory()->orderBy('created_at')->get(),
        ]);
    }

    public function edit(Team $team): Response
    {
        return Inertia::render('Teams/Edit', [
            'team' => $team->load('country'),
            'regions' => Region::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return redirect()->route('teams.show', $team);
    }
}
