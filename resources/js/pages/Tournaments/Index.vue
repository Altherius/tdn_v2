<script setup lang="ts">
import { create as createTournament, generateRoster, show as showTournament } from '@/actions/App/Http/Controllers/TournamentController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';

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

defineProps<{
    tournaments: Tournament[];
}>();

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <Head title="Tournaments" />
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
                <h1 class="text-2xl font-bold">Tournaments</h1>
                <div class="flex gap-4">
                    <Link
                        :href="createTournament().url"
                        class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        + Create tournament
                    </Link>
                    <Link
                        :href="generateRoster().url"
                        class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        Generate roster
                    </Link>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Winner</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">2nd Place</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">3rd Place</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tournaments.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                                No tournaments yet.
                            </td>
                        </tr>
                        <tr
                            v-for="tournament in tournaments"
                            :key="tournament.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm font-medium">
                                <Link
                                    :href="showTournament(tournament.id).url"
                                    class="hover:underline"
                                >
                                    {{ tournament.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <Link
                                    v-if="tournament.winner"
                                    :href="showTeam(tournament.winner.id).url"
                                    class="hover:underline"
                                >
                                    {{ tournament.winner.name }}
                                </Link>
                                <span v-else class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <Link
                                    v-if="tournament.secondPlace"
                                    :href="showTeam(tournament.secondPlace.id).url"
                                    class="hover:underline"
                                >
                                    {{ tournament.secondPlace.name }}
                                </Link>
                                <span v-else class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <Link
                                    v-if="tournament.thirdPlace"
                                    :href="showTeam(tournament.thirdPlace.id).url"
                                    class="hover:underline"
                                >
                                    {{ tournament.thirdPlace.name }}
                                </Link>
                                <span v-else class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
