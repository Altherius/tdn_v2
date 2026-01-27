<script setup lang="ts">
import { edit as editTeam } from '@/actions/App/Http/Controllers/TeamController';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import ContentCard from '@/components/ContentCard.vue';
import GamesHistoryTable from '@/components/GamesHistoryTable.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { useAppearance } from '@/composables/useAppearance';
import { usePieChartOptions } from '@/composables/useChartConfig';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArcElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { computed, ref } from 'vue';
import { Line, Pie } from 'vue-chartjs';
import type { BreadcrumbItem } from '@/types';
import type { EloHistory, Game, Team, Tournament } from '@/types/models';

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Classement', href: home().url },
    { label: 'Détail' },
];

ChartJS.register(ArcElement, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const props = defineProps<{
    team: Team;
    games: Game[];
    eloHistory: EloHistory[];
}>();

const { resolvedAppearance } = useAppearance();
const { pieChartOptions } = usePieChartOptions();

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

const gameStats = computed(() => {
    let wins = 0;
    let draws = 0;
    let losses = 0;

    for (const game of props.games) {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            continue;
        }

        const team1Total = game.leg1_team1_score + game.leg2_team1_score;
        const team2Total = game.leg1_team2_score + game.leg2_team2_score;
        const isTeam1 = props.team.id === game.team1_id;
        const teamTotal = isTeam1 ? team1Total : team2Total;
        const opponentTotal = isTeam1 ? team2Total : team1Total;

        if (teamTotal > opponentTotal) {
            wins++;
        } else if (teamTotal < opponentTotal) {
            losses++;
        } else {
            draws++;
        }
    }

    return { wins, draws, losses };
});

const goalStats = computed(() => {
    let scored = 0;
    let received = 0;

    for (const game of props.games) {
        if (
            game.leg1_team1_score === null ||
            game.leg1_team2_score === null ||
            game.leg2_team1_score === null ||
            game.leg2_team2_score === null
        ) {
            continue;
        }

        const team1Total = game.leg1_team1_score + game.leg2_team1_score;
        const team2Total = game.leg1_team2_score + game.leg2_team2_score;
        const isTeam1 = props.team.id === game.team1_id;

        if (isTeam1) {
            scored += team1Total;
            received += team2Total;
        } else {
            scored += team2Total;
            received += team1Total;
        }
    }

    return { scored, received };
});

const resultsChartData = computed(() => {
    return {
        labels: ['Victoires', 'Nuls', 'Défaites'],
        datasets: [
            {
                data: [gameStats.value.wins, gameStats.value.draws, gameStats.value.losses],
                backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                borderColor: resolvedAppearance.value === 'dark' ? '#161615' : '#ffffff',
                borderWidth: 2,
            },
        ],
    };
});

const goalsChartData = computed(() => {
    return {
        labels: ['Buts marqués', 'Buts encaissés'],
        datasets: [
            {
                data: [goalStats.value.scored, goalStats.value.received],
                backgroundColor: ['#3b82f6', '#f97316'],
                borderColor: resolvedAppearance.value === 'dark' ? '#161615' : '#ffffff',
                borderWidth: 2,
            },
        ],
    };
});

const selectedTournament = ref<Tournament | undefined>(undefined);
const tournamentSearchTerm = ref('');

const tournaments = computed(() => {
    const tournamentMap = new Map<number, Tournament>();
    for (const game of props.games) {
        if (game.tournament && !tournamentMap.has(game.tournament.id)) {
            tournamentMap.set(game.tournament.id, game.tournament);
        }
    }
    return Array.from(tournamentMap.values());
});

const filteredTournaments = computed(() => {
    if (!tournamentSearchTerm.value) return tournaments.value;
    return tournaments.value.filter((t) => t.name.toLowerCase().includes(tournamentSearchTerm.value.toLowerCase()));
});

const filteredGames = computed(() => {
    if (selectedTournament.value === undefined) {
        return props.games;
    }
    return props.games.filter((game) => game.tournament?.id === selectedTournament.value?.id);
});
</script>

<template>
    <Head :title="team.name" />
    <PublicLayout>
        <Breadcrumb :items="breadcrumbs" />
        <div class="mb-6">
            <h1 class="text-2xl font-bold">{{ team.name }}</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">
                {{ team.region.name }} &middot; Classement : {{ team.elo_rating }}
            </p>
            <Link
                v-if="$page.props.auth.user"
                :href="editTeam(team.id).url"
                class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
            >
                Éditer l'équipe
            </Link>
        </div>

        <ContentCard class="mb-8">
            <div class="h-64">
                <Line :data="chartData" :options="chartOptions" />
            </div>
        </ContentCard>

        <div class="mb-8 grid gap-6 md:grid-cols-2">
            <ContentCard>
                <h3 class="mb-4 text-center text-sm font-semibold">Résultats</h3>
                <div class="h-48">
                    <Pie :data="resultsChartData" :options="pieChartOptions" />
                </div>
            </ContentCard>
            <ContentCard>
                <h3 class="mb-4 text-center text-sm font-semibold">Buts</h3>
                <div class="h-48">
                    <Pie :data="goalsChartData" :options="pieChartOptions" />
                </div>
            </ContentCard>
        </div>

        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Historique des matchs</h2>

            <div v-if="tournaments.length > 0" class="w-64">
                <Combobox
                    v-model="selectedTournament"
                    v-model:search-term="tournamentSearchTerm"
                    :filter-function="() => true"
                >
                    <ComboboxAnchor>
                        <ComboboxInput
                            placeholder="Tous les tournois"
                            :display-value="(val: Tournament) => val?.name"
                        />
                        <ComboboxTrigger />
                    </ComboboxAnchor>
                    <ComboboxContent>
                        <ComboboxEmpty>Aucun tournoi trouvé.</ComboboxEmpty>
                        <ComboboxItem :value="undefined">
                            Tous les tournois
                        </ComboboxItem>
                        <ComboboxItem v-for="tournament in filteredTournaments" :key="tournament.id" :value="tournament">
                            {{ tournament.name }}
                        </ComboboxItem>
                    </ComboboxContent>
                </Combobox>
            </div>
        </div>

        <GamesHistoryTable
            :games="filteredGames"
            :highlight-team-id="team.id"
            :show-tournament-column="selectedTournament === undefined"
            empty-message="Aucun match joué."
        />
    </PublicLayout>
</template>
