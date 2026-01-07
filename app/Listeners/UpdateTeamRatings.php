<?php

namespace App\Listeners;

use App\Enums\GameResult;
use App\Events\GameCreated;
use App\Services\EloRatingService;

class UpdateTeamRatings
{
    /**
     * Create the event listener.
     */
    public function __construct(public EloRatingService $eloRatingService) {}

    /**
     * Handle the event.
     */
    public function handle(GameCreated $event): void
    {
        $game = $event->game;
        $team1 = $game->team1;
        $team2 = $game->team2;

        $winner = $game->winner();

        if ($winner === null) {
            $team1Result = GameResult::Draw;
            $team2Result = GameResult::Draw;
        } elseif ($winner->id === $team1->id) {
            $team1Result = GameResult::Win;
            $team2Result = GameResult::Loss;
        } else {
            $team1Result = GameResult::Loss;
            $team2Result = GameResult::Win;
        }

        $team1Points = $this->eloRatingService->calculatePointsExchange(
            $team1->elo_rating,
            $team2->elo_rating,
            $team1Result
        );

        $team2Points = $this->eloRatingService->calculatePointsExchange(
            $team2->elo_rating,
            $team1->elo_rating,
            $team2Result
        );

        $team1->update(['elo_rating' => $team1->elo_rating + $team1Points]);
        $team2->update(['elo_rating' => $team2->elo_rating + $team2Points]);
    }
}
