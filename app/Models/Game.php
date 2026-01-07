<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = [
        'team1_id',
        'team2_id',
        'leg1_team1_score',
        'leg1_team2_score',
        'leg2_team1_score',
        'leg2_team2_score',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Team, $this>
     */
    public function team1(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Team, $this>
     */
    public function team2(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function totalTeam1Score(): ?int
    {
        if ($this->leg1_team1_score === null || $this->leg2_team1_score === null) {
            return null;
        }

        return $this->leg1_team1_score + $this->leg2_team1_score;
    }

    public function totalTeam2Score(): ?int
    {
        if ($this->leg1_team2_score === null || $this->leg2_team2_score === null) {
            return null;
        }

        return $this->leg1_team2_score + $this->leg2_team2_score;
    }

    public function winner(): ?Team
    {
        $team1Total = $this->totalTeam1Score();
        $team2Total = $this->totalTeam2Score();

        if ($team1Total === null || $team2Total === null) {
            return null;
        }

        if ($team1Total > $team2Total) {
            return $this->team1;
        }

        if ($team2Total > $team1Total) {
            return $this->team2;
        }

        return null;
    }
}
