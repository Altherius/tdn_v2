<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Region;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Teams/Create', [
            'regions' => Region::orderBy('name')->get(),
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
            'team' => $team->load('region'),
            'games' => $team->games()->with(['team1', 'team2', 'tournament'])->get(),
            'eloHistory' => $team->eloHistory()->orderBy('created_at')->get(),
        ]);
    }

    public function edit(Team $team): Response
    {
        return Inertia::render('Teams/Edit', [
            'team' => $team,
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return redirect()->route('teams.show', $team);
    }
}
