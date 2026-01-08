<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Region {
    id: number;
    name: string;
}

type GameResult = 'win' | 'draw' | 'loss';

interface Team {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
    lastGames: GameResult[];
}

type SortColumn = 'name' | 'region' | 'elo_rating';
type SortDirection = 'asc' | 'desc';

const props = withDefaults(
    defineProps<{
        teams: Team[];
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
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
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

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
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
                            <td colspan="4" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                Aucune équipe trouvée.
                            </td>
                        </tr>
                        <tr
                            v-for="team in filteredAndSortedTeams"
                            :key="team.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm font-medium">
                                <Link
                                    :href="showTeam(team.id).url"
                                    class="hover:underline"
                                >
                                    {{ team.name }}
                                </Link>
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
        </main>
    </div>
</template>
