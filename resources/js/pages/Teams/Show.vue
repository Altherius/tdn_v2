<script setup lang="ts">
import { edit as editTeam, show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { index as indexTournaments, show as showTournament } from '@/actions/App/Http/Controllers/TournamentController';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import Navbar from '@/components/Navbar.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Classement', href: indexTournaments().url },
    { label: 'Détail' },
];

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

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

interface Tournament {
    id: number;
    name: string;
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
    tournament: Tournament | null;
}

interface EloHistory {
    id: number;
    team_id: number;
    game_id: number;
    rating: number;
    created_at: string;
}

const props = defineProps<{
    team: Team;
    games: Game[];
    eloHistory: EloHistory[];
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

const chartData = computed(() => {
    const labels = ['Initial', ...props.eloHistory.map((_, index) => `Match ${index + 1}`)];
    const ratings = [1000, ...props.eloHistory.map((h) => h.rating)];

    const isDark = resolvedAppearance.value === 'dark';
    const lineColor = isDark ? '#60a5fa' : '#2563eb';
    const pointColor = isDark ? '#93c5fd' : '#1d4ed8';

    return {
        labels,
        datasets: [
            {
                label: 'Classement',
                data: ratings,
                borderColor: lineColor,
                backgroundColor: pointColor,
                tension: 0.1,
                pointRadius: 4,
                pointHoverRadius: 6,
            },
        ],
    };
});

const chartOptions = computed(() => {
    const isDark = resolvedAppearance.value === 'dark';
    const textColor = isDark ? '#A1A09A' : '#706f6c';
    const gridColor = isDark ? '#3E3E3A' : '#e3e3e0';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            },
            title: {
                display: true,
                text: 'Historique du classement',
                color: textColor,
                font: {
                    size: 16,
                    weight: 'bold' as const,
                },
            },
        },
        scales: {
            x: {
                ticks: {
                    color: textColor,
                },
                grid: {
                    color: gridColor,
                },
            },
            y: {
                ticks: {
                    color: textColor,
                },
                grid: {
                    color: gridColor,
                },
            },
        },
    };
});
</script>

<template>
    <Head :title="team.name" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">{{ team.name }}</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    {{ team.region.name }} &middot; Classement : {{ team.elo_rating }}
                </p>
                <Link
                    :href="editTeam(team.id).url"
                    class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Éditer l'équipe
                </Link>
            </div>

            <div
                class="mb-8 overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <div class="h-64">
                    <Line :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <h2 class="mb-4 text-lg font-semibold">Historique des matchs</h2>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Équipes</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Tournoi</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Aller</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Retour</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="games.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                Aucun match joué.
                            </td>
                        </tr>
                        <tr
                            v-for="game in games"
                            :key="game.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm">
                                <span v-if="team.id === game.team1_id" class="font-semibold">
                                    {{ game.team1.name }}
                                </span>
                                <Link
                                    v-else
                                    :href="showTeam(game.team1.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.team1.name }}
                                </Link>
                                <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">vs</span>
                                <span v-if="team.id === game.team2_id" class="font-semibold">
                                    {{ game.team2.name }}
                                </span>
                                <Link
                                    v-else
                                    :href="showTeam(game.team2.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.team2.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <Link
                                    v-if="game.tournament"
                                    :href="showTournament(game.tournament.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.tournament.name }}
                                </Link>
                                <span class="dark:text-[#444444]" v-else><i>Amical</i></span>
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
