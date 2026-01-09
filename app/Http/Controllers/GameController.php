<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Games/Create', [
            'tournaments' => Tournament::where('is_over', false)->orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function store(GameRequest $request): RedirectResponse
    {
        Game::create($request->validated());

        return redirect()->route('games.create')->with('success', 'Match créé avec succès.');
    }
}
