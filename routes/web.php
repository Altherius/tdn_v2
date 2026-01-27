<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\MatchupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TeamController::class, 'index'])->name('home');
Route::get('tournaments', [TournamentController::class, 'index'])->name('tournaments.index');

Route::middleware('auth')->group(function () {
    Route::get('tournaments/new', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('tournaments/generate-roster', [TournamentController::class, 'generateRoster'])->name('tournaments.generate-roster');
    Route::get('tournaments/edit/{tournament}', [TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournaments.update');

    Route::get('teams/new', [TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('teams/edit/{team}', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('teams/{team}', [TeamController::class, 'update'])->name('teams.update');

    Route::get('games/new', [GameController::class, 'create'])->name('games.create');
    Route::post('games', [GameController::class, 'store'])->name('games.store');
});

Route::get('tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');

Route::get('matchup', [MatchupController::class, 'index'])->name('matchup.index');
Route::get('matchup/{team1}/{team2}', [MatchupController::class, 'show'])->name('matchup.show');

require __DIR__.'/settings.php';
