<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';

interface Region {
    id: number;
    name: string;
}

interface Team {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
}

interface Game {
    id: number;
    team1_id: number;
    team2_id: number;
    team1: Team;
    team2: Team;
    leg1_team1_score: number | null;
    leg1_team2_score: number | null;
    leg2_team1_score: number | null;
    leg2_team2_score: number | null;
}

const props = defineProps<{
    team: Team;
    games: Game[];
}>();

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

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

    const isTeam1 = props.team.id === game.team1_id;
    const teamTotal = isTeam1 ? team1Total : team2Total;
    const opponentTotal = isTeam1 ? team2Total : team1Total;

    if (teamTotal > opponentTotal) {
        return 'text-green-600 dark:text-green-400';
    } else if (teamTotal < opponentTotal) {
        return 'text-red-600 dark:text-red-400';
    }

    return 'text-yellow-600 dark:text-yellow-400';
}
</script>

<template>
    <Head :title="team.name" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="w-full border-b border-[#e3e3e0] bg-white px-6 py-4 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <nav class="mx-auto flex max-w-4xl items-center justify-between">
                <div class="flex items-center gap-4">
                    <button
                        @click="toggleTheme"
                        class="flex h-9 w-9 items-center justify-center rounded-md border border-[#e3e3e0] bg-[#FDFDFC] transition-colors hover:bg-[#f5f5f4] dark:border-[#3E3E3A] dark:bg-[#1a1a19] dark:hover:bg-[#252524]"
                        :title="resolvedAppearance === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <Sun v-if="resolvedAppearance === 'dark'" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>
                    <Link
                        :href="home()"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        &larr; Back to rankings
                    </Link>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">{{ team.name }}</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    {{ team.region.name }} &middot; Rating: {{ team.elo_rating }}
                </p>
            </div>

            <h2 class="mb-4 text-lg font-semibold">Games</h2>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Teams</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Leg 1</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Leg 2</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Tie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="games.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                No games played yet.
                            </td>
                        </tr>
                        <tr
                            v-for="game in games"
                            :key="game.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm">
                                <span :class="{ 'font-semibold': team.id === game.team1_id }">
                                    {{ game.team1.name }}
                                </span>
                                <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">vs</span>
                                <span :class="{ 'font-semibold': team.id === game.team2_id }">
                                    {{ game.team2.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono">
                                {{ formatLegResult(game, 1) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono">
                                {{ formatLegResult(game, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono font-semibold" :class="getTieResultClass(game)">
                                {{ formatTieResult(game) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
