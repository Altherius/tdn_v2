<?php

namespace App\Http\Controllers;

use App\Http\Requests\TournamentRequest;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TournamentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tournaments/Index', [
            'tournaments' => Tournament::with(['winner', 'secondPlace', 'thirdPlace'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tournaments/Create');
    }

    public function store(TournamentRequest $request): RedirectResponse
    {
        $tournament = Tournament::create($request->validated());

        return redirect()->route('tournaments.show', $tournament);
    }

    public function show(Tournament $tournament): Response
    {
        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament->load(['winner', 'secondPlace', 'thirdPlace']),
            'games' => $tournament->games()->with(['team1', 'team2'])->get(),
        ]);
    }

    public function edit(Tournament $tournament): Response
    {
        return Inertia::render('Tournaments/Edit', [
            'tournament' => $tournament,
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function update(TournamentRequest $request, Tournament $tournament): RedirectResponse
    {
        $tournament->update($request->validated());

        return redirect()->route('tournaments.show', $tournament);
    }
}
