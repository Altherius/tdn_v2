<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'region_id',
        'country_id',
        'elo_rating',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Region, $this>
     */
    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Country, $this>
     */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all games where this team participated.
     *
     * @return Builder<Game>
     */
    public function games(): Builder
    {
        return Game::where('team1_id', $this->id)
            ->orWhere('team2_id', $this->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\EloHistory, $this>
     */
    public function eloHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EloHistory::class);
    }

    /**
     * Get the results of the last N completed games for this team.
     *
     * @return array<\App\Enums\GameResult>
     */
    public function getLastGameResults(int $count = 5): array
    {
        $games = Game::query()
            ->where(fn ($query) => $query->where('team1_id', $this->id)->orWhere('team2_id', $this->id))
            ->whereNotNull('leg1_team1_score')
            ->whereNotNull('leg1_team2_score')
            ->whereNotNull('leg2_team1_score')
            ->whereNotNull('leg2_team2_score')
            ->orderByDesc('created_at')
            ->limit($count)
            ->get();

        return $games->map(function (Game $game) {
            $team1Total = $game->totalTeam1Score();
            $team2Total = $game->totalTeam2Score();

            $isTeam1 = $this->id === $game->team1_id;
            $teamTotal = $isTeam1 ? $team1Total : $team2Total;
            $opponentTotal = $isTeam1 ? $team2Total : $team1Total;

            if ($teamTotal > $opponentTotal) {
                return \App\Enums\GameResult::Win;
            }

            if ($teamTotal < $opponentTotal) {
                return \App\Enums\GameResult::Loss;
            }

            return \App\Enums\GameResult::Draw;
        })->all();
    }
}
