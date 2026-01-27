<script setup lang="ts">
import Breadcrumb from '@/components/Breadcrumb.vue';
import ContentCard from '@/components/ContentCard.vue';
import GamesHistoryTable from '@/components/GamesHistoryTable.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { index as indexMatchup } from '@/actions/App/Http/Controllers/MatchupController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { useAppearance } from '@/composables/useAppearance';
import { usePieChartOptions } from '@/composables/useChartConfig';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import { computed } from 'vue';
import { Pie } from 'vue-chartjs';
import type { BreadcrumbItem } from '@/types';
import type { Game, MatchupAnalysis, Team } from '@/types/models';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
    team1: Team;
    team2: Team;
    games: Game[];
    analysis: MatchupAnalysis;
}>();

const { resolvedAppearance } = useAppearance();
const { pieChartOptions } = useChartConfig();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Classement', href: home().url },
    { label: 'Analyse de confrontation', href: indexMatchup().url },
    { label: `${props.team1.name} vs ${props.team2.name}` },
];

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
    <PublicLayout>
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
        <ContentCard class="mb-4">
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
        </ContentCard>

        <!-- ELO Changes Cards -->
        <div class="mb-8 grid gap-4 md:grid-cols-2">
            <ContentCard>
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
            </ContentCard>

            <ContentCard>
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
            </ContentCard>
        </div>

        <!-- Charts (only if there are completed games) -->
        <div v-if="hasCompletedGames" class="mb-8 grid gap-6 md:grid-cols-2">
            <ContentCard>
                <h3 class="mb-4 text-center text-sm font-semibold">Résultats des confrontations</h3>
                <div class="h-48">
                    <Pie :data="resultsChartData" :options="pieChartOptions" />
                </div>
            </ContentCard>
            <ContentCard>
                <h3 class="mb-4 text-center text-sm font-semibold">Buts marqués</h3>
                <div class="h-48">
                    <Pie :data="goalsChartData" :options="pieChartOptions" />
                </div>
            </ContentCard>
        </div>

        <!-- Games History -->
        <h2 class="mb-4 text-lg font-semibold">Historique des confrontations</h2>

        <GamesHistoryTable
            :games="games"
            :highlight-team-id="team1.id"
            empty-message="Ces équipes ne se sont jamais affrontées."
        />
    </PublicLayout>
</template>
