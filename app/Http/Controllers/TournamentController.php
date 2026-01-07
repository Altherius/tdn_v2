<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
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

    public function show(Tournament $tournament): Response
    {
        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament->load(['winner', 'secondPlace', 'thirdPlace']),
            'games' => $tournament->games()->with(['team1', 'team2'])->get(),
        ]);
    }
}
