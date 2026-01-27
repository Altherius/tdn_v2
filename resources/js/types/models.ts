export interface Region {
    id: number;
    name: string;
}

export interface Country {
    id: number;
    name: string;
    code: string;
}

export interface Team {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
    region_id?: number;
    country_id?: number | null;
    country?: Country | null;
}

export interface Tournament {
    id: number;
    name: string;
    is_major?: boolean;
    is_balancing?: boolean;
    is_over?: boolean;
    winner?: Team | null;
    winner_team_id?: number | null;
    second_place?: Team | null;
    second_place_team_id?: number | null;
    third_place?: Team | null;
    third_place_team_id?: number | null;
}

export interface Game {
    id: number;
    team1_id: number;
    team2_id: number;
    team1: Team;
    team2: Team;
    leg1_team1_score: number | null;
    leg1_team2_score: number | null;
    leg2_team1_score: number | null;
    leg2_team2_score: number | null;
    tournament?: Tournament | null;
}

export interface EloHistory {
    id: number;
    team_id: number;
    game_id: number;
    rating: number;
    created_at: string;
}

export interface MatchupAnalysis {
    team1WinProbability: number;
    drawProbability: number;
    team2WinProbability: number;
    team1GainOnWin: number;
    team1LossOnLoss: number;
    team2GainOnWin: number;
    team2LossOnLoss: number;
}

export type GameResult = 'win' | 'draw' | 'loss';

export interface TicketEntry {
    team: Team | undefined;
    tickets: number;
}
