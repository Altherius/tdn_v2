<script setup lang="ts">
import CountryFlag from '@/components/CountryFlag.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { GameResult, Region } from '@/types/models';

interface WelcomeTeam {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
    country: { id: number; name: string; code: string } | null;
    lastGames: GameResult[];
    goldCount: number;
    silverCount: number;
    bronzeCount: number;
}

type SortColumn = 'name' | 'region' | 'elo_rating';
type SortDirection = 'asc' | 'desc';

const props = withDefaults(
    defineProps<{
        teams: WelcomeTeam[];
    }>(),
    {
        teams: () => [],
    },
);

// Filter state
const searchQuery = ref('');

// Sort state
const sortColumn = ref<SortColumn | null>(null);
const sortDirection = ref<SortDirection>('asc');

function toggleSort(column: SortColumn) {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
}

// Compute original ranking based on ELO (teams come sorted by ELO from backend)
const teamRanks = computed(() => {
    const ranks = new Map<number, number>();
    props.teams.forEach((team, index) => {
        ranks.set(team.id, index + 1);
    });
    return ranks;
});

const filteredAndSortedTeams = computed(() => {
    let result = [...props.teams];

    // Filter by search query
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        result = result.filter(
            (team) =>
                team.name.toLowerCase().includes(query) ||
                team.region.name.toLowerCase().includes(query)
        );
    }

    // Sort if a column is selected
    if (sortColumn.value) {
        result.sort((a, b) => {
            let comparison = 0;
            switch (sortColumn.value) {
                case 'name':
                    comparison = a.name.localeCompare(b.name);
                    break;
                case 'region':
                    comparison = a.region.name.localeCompare(b.region.name);
                    break;
                case 'elo_rating':
                    comparison = a.elo_rating - b.elo_rating;
                    break;
            }
            return sortDirection.value === 'asc' ? comparison : -comparison;
        });
    }

    return result;
});
</script>

<template>
    <Head title="Classement" />
    <PublicLayout>
        <h1 class="mb-6 text-2xl font-bold">Classement des équipes</h1>

        <!-- Search input -->
        <div class="mb-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Filtrer par équipe ou par région..."
                class="w-full rounded-lg border border-[#e3e3e0] bg-white px-4 py-2 text-sm outline-none placeholder:text-[#706f6c] focus:border-[#1b1b18] focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#161615] dark:placeholder:text-[#A1A09A] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
            />
        </div>

        <div class="overflow-x-auto rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                        <th class="px-4 py-3 text-center text-sm font-semibold w-12">#</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">
                            <button
                                @click="toggleSort('name')"
                                class="inline-flex items-center gap-1 hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                            >
                                Équipe
                                <ArrowUp v-if="sortColumn === 'name' && sortDirection === 'asc'" class="h-4 w-4" />
                                <ArrowDown v-else-if="sortColumn === 'name' && sortDirection === 'desc'" class="h-4 w-4" />
                                <ArrowUpDown v-else class="h-4 w-4 text-[#706f6c] dark:text-[#A1A09A]" />
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">
                            <button
                                @click="toggleSort('region')"
                                class="inline-flex items-center gap-1 hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                            >
                                Région
                                <ArrowUp v-if="sortColumn === 'region' && sortDirection === 'asc'" class="h-4 w-4" />
                                <ArrowDown v-else-if="sortColumn === 'region' && sortDirection === 'desc'" class="h-4 w-4" />
                                <ArrowUpDown v-else class="h-4 w-4 text-[#706f6c] dark:text-[#A1A09A]" />
                            </button>
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-semibold">
                            <button
                                @click="toggleSort('elo_rating')"
                                class="inline-flex items-center gap-1 hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                            >
                                Classement
                                <ArrowUp v-if="sortColumn === 'elo_rating' && sortDirection === 'asc'" class="h-4 w-4" />
                                <ArrowDown v-else-if="sortColumn === 'elo_rating' && sortDirection === 'desc'" class="h-4 w-4" />
                                <ArrowUpDown v-else class="h-4 w-4 text-[#706f6c] dark:text-[#A1A09A]" />
                            </button>
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-semibold">Derniers matchs</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredAndSortedTeams.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            Aucune équipe trouvée.
                        </td>
                    </tr>
                    <tr
                        v-for="team in filteredAndSortedTeams"
                        :key="team.id"
                        class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                    >
                        <td class="px-4 py-4 text-center text-sm font-mono text-[#706f6c] dark:text-[#A1A09A]">
                            {{ teamRanks.get(team.id) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <CountryFlag v-if="team.country" :code="team.country.code" />
                                <Link
                                    :href="showTeam(team.id).url"
                                    class="hover:underline"
                                >
                                    {{ team.name }}
                                </Link>
                                <span v-if="team.goldCount > 0 || team.silverCount > 0 || team.bronzeCount > 0" class="flex items-center gap-1.5 text-xs">
                                    <span v-if="team.goldCount > 0" class="text-amber-500" title="Victoires">🥇{{ team.goldCount }}</span>
                                    <span v-if="team.silverCount > 0" class="text-slate-400" title="Deuxièmes places">🥈{{ team.silverCount }}</span>
                                    <span v-if="team.bronzeCount > 0" class="text-amber-700" title="Troisièmes places">🥉{{ team.bronzeCount }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ team.region.name }}</td>
                        <td class="px-6 py-4 text-right text-sm font-mono">{{ team.elo_rating }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1 justify-end">
                                <span
                                    v-for="(result, index) in [...team.lastGames].reverse()"
                                    :key="index"
                                    class="h-3 w-3 rounded-full"
                                    :class="{
                                        'bg-green-500': result === 'win',
                                        'bg-yellow-500': result === 'draw',
                                        'bg-red-500': result === 'loss',
                                    }"
                                    :title="result"
                                />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </PublicLayout>
</template>
