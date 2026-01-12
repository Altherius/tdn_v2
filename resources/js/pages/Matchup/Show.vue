<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from '@/components/Breadcrumb.vue';
import Navbar from '@/components/Navbar.vue';
import { index as indexMatchup } from '@/actions/App/Http/Controllers/MatchupController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { show as showTournament } from '@/actions/App/Http/Controllers/TournamentController';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import { computed } from 'vue';
import { Pie } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

interface Team {
    id: number;
    name: string;
    elo_rating: number;
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

interface Analysis {
    team1WinProbability: number;
    drawProbability: number;
    team2WinProbability: number;
    team1GainOnWin: number;
    team1LossOnLoss: number;
    team2GainOnWin: number;
    team2LossOnLoss: number;
}

const props = defineProps<{
    team1: Team;
    team2: Team;
    games: Game[];
    analysis: Analysis;
}>();

const { resolvedAppearance } = useAppearance();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Classement', href: home().url },
    { label: 'Analyse de confrontation', href: indexMatchup().url },
    { label: `${props.team1.name} vs ${props.team2.name}` },
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

    // Determine winner from perspective of our team1 (props.team1)
    const isOurTeam1AsGameTeam1 = game.team1_id === props.team1.id;
    const ourTeam1Total = isOurTeam1AsGameTeam1 ? team1Total : team2Total;
    const ourTeam2Total = isOurTeam1AsGameTeam1 ? team2Total : team1Total;

    if (ourTeam1Total > ourTeam2Total) {
        return 'text-green-600 dark:text-green-400';
    } else if (ourTeam1Total < ourTeam2Total) {
        return 'text-red-600 dark:text-red-400';
    }

    return 'text-yellow-600 dark:text-yellow-400';
}

const gameStats = computed(() => {
    let team1Wins = 0;
    let draws = 0;
    let team2Wins = 0;

    for (const game of props.games) {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            continue;
        }

        const gameTeam1Total = game.leg1_team1_score + game.leg2_team1_score;
        const gameTeam2Total = game.leg1_team2_score + game.leg2_team2_score;

        // Determine if our team1 was game's team1 or team2
        const isOurTeam1AsGameTeam1 = game.team1_id === props.team1.id;
        const ourTeam1Total = isOurTeam1AsGameTeam1 ? gameTeam1Total : gameTeam2Total;
        const ourTeam2Total = isOurTeam1AsGameTeam1 ? gameTeam2Total : gameTeam1Total;

        if (ourTeam1Total > ourTeam2Total) {
            team1Wins++;
        } else if (ourTeam1Total < ourTeam2Total) {
            team2Wins++;
        } else {
            draws++;
        }
    }

    return { team1Wins, draws, team2Wins };
});

const goalStats = computed(() => {
    let team1Goals = 0;
    let team2Goals = 0;

    for (const game of props.games) {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            continue;
        }

        const gameTeam1Total = game.leg1_team1_score + game.leg2_team1_score;
        const gameTeam2Total = game.leg1_team2_score + game.leg2_team2_score;

        // Determine if our team1 was game's team1 or team2
        const isOurTeam1AsGameTeam1 = game.team1_id === props.team1.id;

        if (isOurTeam1AsGameTeam1) {
            team1Goals += gameTeam1Total;
            team2Goals += gameTeam2Total;
        } else {
            team1Goals += gameTeam2Total;
            team2Goals += gameTeam1Total;
        }
    }

    return { team1Goals, team2Goals };
});

const resultsChartData = computed(() => {
    return {
        labels: [`Victoires ${props.team1.name}`, 'Nuls', `Victoires ${props.team2.name}`],
        datasets: [
            {
                data: [gameStats.value.team1Wins, gameStats.value.draws, gameStats.value.team2Wins],
                backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                borderColor: resolvedAppearance.value === 'dark' ? '#161615' : '#ffffff',
                borderWidth: 2,
            },
        ],
    };
});

const goalsChartData = computed(() => {
    return {
        labels: [props.team1.name, props.team2.name],
        datasets: [
            {
                data: [goalStats.value.team1Goals, goalStats.value.team2Goals],
                backgroundColor: ['#3b82f6', '#f97316'],
                borderColor: resolvedAppearance.value === 'dark' ? '#161615' : '#ffffff',
                borderWidth: 2,
            },
        ],
    };
});

const pieChartOptions = computed(() => {
    const isDark = resolvedAppearance.value === 'dark';
    const textColor = isDark ? '#A1A09A' : '#706f6c';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom' as const,
                labels: {
                    color: textColor,
                    padding: 16,
                },
            },
        },
    };
});

const hasCompletedGames = computed(() => {
    return props.games.some(
        (g) =>
            g.leg1_team1_score !== null &&
            g.leg1_team2_score !== null &&
            g.leg2_team1_score !== null &&
            g.leg2_team2_score !== null
    );
});
</script>

<template>
    <Head :title="`${team1.name} vs ${team2.name}`" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">
                    <Link :href="showTeam(team1.id).url" class="hover:underline">{{ team1.name }}</Link>
                    <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">vs</span>
                    <Link :href="showTeam(team2.id).url" class="hover:underline">{{ team2.name }}</Link>
                </h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    {{ team1.elo_rating }} ELO vs {{ team2.elo_rating }} ELO
                </p>
            </div>

            <!-- Probabilities Card -->
            <div class="mb-4 overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <h3 class="mb-4 text-center font-semibold">Probabilités</h3>
                <div class="flex items-center justify-center gap-8 text-center">
                    <div>
                        <div class="text-2xl font-bold">{{ analysis.team1WinProbability }}%</div>
                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ team1.name }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ analysis.drawProbability }}%</div>
                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Nul</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ analysis.team2WinProbability }}%</div>
                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ team2.name }}</div>
                    </div>
                </div>
            </div>

            <!-- ELO Changes Cards -->
            <div class="mb-8 grid gap-4 md:grid-cols-2">
                <div
                    class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <h3 class="mb-4 text-center font-semibold">{{ team1.name }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">ELO en cas de victoire</span>
                            <span class="font-mono font-semibold text-green-600 dark:text-green-400">+{{ analysis.team1GainOnWin }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">ELO en cas de défaite</span>
                            <span class="font-mono font-semibold text-red-600 dark:text-red-400">{{ analysis.team1LossOnLoss }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <h3 class="mb-4 text-center font-semibold">{{ team2.name }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">ELO en cas de victoire</span>
                            <span class="font-mono font-semibold text-green-600 dark:text-green-400">+{{ analysis.team2GainOnWin }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">ELO en cas de défaite</span>
                            <span class="font-mono font-semibold text-red-600 dark:text-red-400">{{ analysis.team2LossOnLoss }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts (only if there are completed games) -->
            <div v-if="hasCompletedGames" class="mb-8 grid gap-6 md:grid-cols-2">
                <div
                    class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <h3 class="mb-4 text-center text-sm font-semibold">Résultats des confrontations</h3>
                    <div class="h-48">
                        <Pie :data="resultsChartData" :options="pieChartOptions" />
                    </div>
                </div>
                <div
                    class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <h3 class="mb-4 text-center text-sm font-semibold">Buts marqués</h3>
                    <div class="h-48">
                        <Pie :data="goalsChartData" :options="pieChartOptions" />
                    </div>
                </div>
            </div>

            <!-- Games History -->
            <h2 class="mb-4 text-lg font-semibold">Historique des confrontations</h2>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Équipes</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Tournoi</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Résultat</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Aller</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold">Retour</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="games.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                Ces équipes ne se sont jamais affrontées.
                            </td>
                        </tr>
                        <tr
                            v-for="game in games"
                            :key="game.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm">
                                <span :class="game.team1_id === team1.id ? 'font-semibold' : ''">
                                    {{ game.team1.name }}
                                </span>
                                <span class="mx-2 text-[#706f6c] dark:text-[#A1A09A]">vs</span>
                                <span :class="game.team2_id === team1.id ? 'font-semibold' : ''">
                                    {{ game.team2.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <Link
                                    v-if="game.tournament"
                                    :href="showTournament(game.tournament.id).url"
                                    class="hover:underline"
                                >
                                    {{ game.tournament.name }}
                                </Link>
                                <span class="dark:text-[#444444] text-[#AAA]" v-else><i>Amical</i></span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono font-semibold" :class="getTieResultClass(game)">
                                {{ formatTieResult(game) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono dark:text-[#444444] text-[#AAA]">
                                {{ formatLegResult(game, 1) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-mono dark:text-[#444444] text-[#AAA]">
                                {{ formatLegResult(game, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
