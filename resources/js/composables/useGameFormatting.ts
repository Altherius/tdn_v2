import type { Game } from '@/types/models';

export function useGameFormatting(teamId?: number) {
    function formatLegResult(game: Game, leg: 1 | 2): string {
        const team1Score = leg === 1 ? game.leg1_team1_score : game.leg2_team1_score;
        const team2Score = leg === 1 ? game.leg1_team2_score : game.leg2_team2_score;

        if (team1Score === null || team2Score === null) {
            return '-';
        }

        return `${team1Score} - ${team2Score}`;
    }

    function formatTieResult(game: Game): string {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            return '-';
        }

        const team1Total = game.leg1_team1_score + game.leg2_team1_score;
        const team2Total = game.leg1_team2_score + game.leg2_team2_score;

        return `${team1Total} - ${team2Total}`;
    }

    function getTieResultClass(game: Game): string {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            return '';
        }

        const team1Total = game.leg1_team1_score + game.leg2_team1_score;
        const team2Total = game.leg1_team2_score + game.leg2_team2_score;

        if (teamId !== undefined) {
            const isTeam1 = teamId === game.team1_id;
            const teamTotal = isTeam1 ? team1Total : team2Total;
            const opponentTotal = isTeam1 ? team2Total : team1Total;

            if (teamTotal > opponentTotal) {
                return 'text-green-600 dark:text-green-400';
            } else if (teamTotal < opponentTotal) {
                return 'text-red-600 dark:text-red-400';
            }
        } else {
            if (team1Total > team2Total) {
                return 'text-green-600 dark:text-green-400';
            } else if (team1Total < team2Total) {
                return 'text-red-600 dark:text-red-400';
            }
        }

        return 'text-yellow-600 dark:text-yellow-400';
    }

    return { formatLegResult, formatTieResult, getTieResultClass };
}
