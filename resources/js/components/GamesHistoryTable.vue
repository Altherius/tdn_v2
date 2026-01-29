<script setup lang="ts">
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { show as showTournament } from '@/actions/App/Http/Controllers/TournamentController';
import { useGameFormatting } from '@/composables/useGameFormatting';
import type { Game } from '@/types/models';
import { Link } from '@inertiajs/vue3';

const props = withDefaults(
    defineProps<{
        games: Game[];
        emptyMessage?: string;
        showTournamentColumn?: boolean;
        highlightTeamId?: number;
        colorizeResults?: boolean;
    }>(),
    {
        emptyMessage: 'Aucun match joué.',
        showTournamentColumn: true,
        colorizeResults: true,
    },
);

const { formatLegResult, formatTieResult, getTieResultClass } = useGameFormatting(props.highlightTeamId);

const columnCount = props.showTournamentColumn ? 5 : 4;
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
        <table class="w-full min-w-[500px]">
            <thead>
                <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                    <th class="px-6 py-3 text-left text-sm font-semibold">Équipes</th>
                    <th v-if="showTournamentColumn" class="px-6 py-3 text-left text-sm font-semibold">Tournoi</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Résultat</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Aller</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Retour</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="games.length === 0">
                    <td :colspan="columnCount" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                        {{ emptyMessage }}
                    </td>
                </tr>
                <tr
                    v-for="game in games"
                    :key="game.id"
                    class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                >
                    <td class="px-6 py-4 text-sm">
                        <span v-if="highlightTeamId === game.team1_id" class="font-semibold">
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
                        <span v-if="highlightTeamId === game.team2_id" class="font-semibold">
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
                    <td v-if="showTournamentColumn" class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        <Link
                            v-if="game.tournament"
                            :href="showTournament(game.tournament.id).url"
                            class="hover:underline"
                        >
                            {{ game.tournament.name }}
                        </Link>
                        <span v-else class="text-[#AAA] dark:text-[#444444]"><i>Amical</i></span>
                    </td>
                    <td
                        class="px-6 py-4 text-center text-sm font-mono font-semibold"
                        :class="colorizeResults ? getTieResultClass(game) : ''"
                    >
                        {{ formatTieResult(game) }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-mono text-[#AAA] dark:text-[#444444]">
                        {{ formatLegResult(game, 1) }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-mono text-[#AAA] dark:text-[#444444]">
                        {{ formatLegResult(game, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
