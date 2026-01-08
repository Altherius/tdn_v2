<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from '@/components/Breadcrumb.vue';
import Navbar from '@/components/Navbar.vue';
import { edit as editTournament, index as indexTournaments } from '@/actions/App/Http/Controllers/TournamentController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

interface Team {
    id: number;
    name: string;
}

interface Tournament {
    id: number;
    name: string;
    winner: Team | null;
    secondPlace: Team | null;
    thirdPlace: Team | null;
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
    tournament: Tournament;
    games: Game[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: props.tournament.name },
];

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

    if (team1Total > team2Total) {
        return 'text-green-600 dark:text-green-400';
    } else if (team1Total < team2Total) {
        return 'text-red-600 dark:text-red-400';
    }

    return 'text-yellow-600 dark:text-yellow-400';
}
</script>

<template>
    <Head :title="tournament.name" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">{{ tournament.name }}</h1>
                <Link
                    :href="editTournament(tournament.id).url"
                    class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Éditer le tournoi
                </Link>
            </div>

            <div class="mb-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <p class="mb-2 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Vainqueur</p>
                    <Link
                        v-if="tournament.winner"
                        :href="showTeam(tournament.winner.id).url"
                        class="text-lg font-semibold hover:underline"
                    >
                        {{ tournament.winner.name }}
                    </Link>
                    <p v-else class="text-lg text-[#706f6c] dark:text-[#A1A09A]">-</p>
                </div>

                <div class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <p class="mb-2 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Deuxième</p>
                    <Link
                        v-if="tournament.secondPlace"
                        :href="showTeam(tournament.secondPlace.id).url"
                        class="text-lg font-semibold hover:underline"
                    >
                        {{ tournament.secondPlace.name }}
                    </Link>
                    <p v-else class="text-lg text-[#706f6c] dark:text-[#A1A09A]">-</p>
                </div>

                <div class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <p class="mb-2 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Troisième</p>
                    <Link
                        v-if="tournament.thirdPlace"
                        :href="showTeam(tournament.thirdPlace.id).url"
                        class="text-lg font-semibold hover:underline"
                    >
                        {{ tournament.thirdPlace.name }}
                    </Link>
                    <p v-else class="text-lg text-[#706f6c] dark:text-[#A1A09A]">-</p>
                </div>
            </div>

            <h2 class="mb-4 text-lg font-semibold">Liste des matchs</h2>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Équipes</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Aller</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Retour</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="games.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                Aucun match joué dans ce tournoi.
                            </td>
                        </tr>
                        <tr
                            v-for="game in games"
                            :key="game.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm">
                                <Link
                                    :href="showTeam(game.team1.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.team1.name }}
                                </Link>
                                <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">vs</span>
                                <Link
                                    :href="showTeam(game.team2.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.team2.name }}
                                </Link>
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
