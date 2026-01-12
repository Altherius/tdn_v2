<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Services\EloRatingService;
use Inertia\Inertia;
use Inertia\Response;

class MatchupController extends Controller
{
    public function __construct(private EloRatingService $eloRatingService) {}

    public function index(): Response
    {
        $teams = Team::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Matchup/Index', [
            'teams' => $teams,
        ]);
    }

    public function show(Team $team1, Team $team2): Response
    {
        $games = Game::query()
            ->with(['team1', 'team2', 'tournament'])
            ->where(function ($query) use ($team1, $team2) {
                $query->where(function ($q) use ($team1, $team2) {
                    $q->where('team1_id', $team1->id)->where('team2_id', $team2->id);
                })->orWhere(function ($q) use ($team1, $team2) {
                    $q->where('team1_id', $team2->id)->where('team2_id', $team1->id);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $odds = $this->eloRatingService->calculateOdds(
            $team1->elo_rating,
            $team2->elo_rating
        );

        return Inertia::render('Matchup/Show', [
            'team1' => $team1,
            'team2' => $team2,
            'games' => $games,
            'analysis' => [
                'team1WinProbability' => round($odds['home'] * 100, 1),
                'drawProbability' => round($odds['draw'] * 100, 1),
                'team2WinProbability' => round($odds['away'] * 100, 1),
                'team1GainOnWin' => $this->eloRatingService->calculateExpectedGainOnWin(
                    $team1->elo_rating,
                    $team2->elo_rating
                ),
                'team1LossOnLoss' => $this->eloRatingService->calculateExpectedLossOnLoss(
                    $team1->elo_rating,
                    $team2->elo_rating
                ),
                'team2GainOnWin' => $this->eloRatingService->calculateExpectedGainOnWin(
                    $team2->elo_rating,
                    $team1->elo_rating
                ),
                'team2LossOnLoss' => $this->eloRatingService->calculateExpectedLossOnLoss(
                    $team2->elo_rating,
                    $team1->elo_rating
                ),
            ],
        ]);
    }
}
