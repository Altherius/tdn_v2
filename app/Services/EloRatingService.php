<?php

namespace App\Services;

use App\Enums\GameResult;

class EloRatingService
{
    private const int K_FACTOR = 40;

    private const int RATING_DIVISOR = 400;

    /**
     * Calculate the points to exchange based on Elo rating formula.
     *
     * @return int Points to add to the team's rating (positive for gain, negative for loss)
     */
    public function calculatePointsExchange(int $teamRating, int $opponentRating, GameResult $result, int $goalsDiff): int
    {
        $expectedScore = $this->calculateExpectedScore($teamRating, $opponentRating);
        $actualScore = $this->getActualScore($result);

        return (int) round(self::K_FACTOR * ($actualScore - $expectedScore)) * $this->getGoalDiffMultiplier($goalsDiff);
    }

    /**
     * Calculate the expected score using the Elo formula.
     * E = 1 / (1 + 10^((opponent_rating - team_rating) / 400))
     */
    private function calculateExpectedScore(int $teamRating, int $opponentRating): float
    {
        $exponent = ($opponentRating - $teamRating) / self::RATING_DIVISOR;

        return 1 / (1 + pow(10, $exponent));
    }

    /**
     * Convert game result to actual score value.
     */
    private function getActualScore(GameResult $result): float
    {
        return match ($result) {
            GameResult::Win => 1.0,
            GameResult::Draw => 0.5,
            GameResult::Loss => 0.0,
        };
    }

    private function getGoalDiffMultiplier(int $goalsDiff): float
    {
        if ($goalsDiff < 4) {
            return 1.0;
        }

        if ($goalsDiff < 6) {
            return 1.5;
        }

        if ($goalsDiff < 8) {
            return 1.75;
        }

        return 1.75 + (ceil($goalsDiff - 7) * 0.0625);
    }
}
