<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function show(Team $team): Response
    {
        return Inertia::render('Teams/Show', [
            'team' => $team->load('region'),
            'games' => $team->games()->with(['team1', 'team2'])->get(),
        ]);
    }
}
