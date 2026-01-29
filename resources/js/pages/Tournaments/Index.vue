<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { create as createTournament, generateRoster, show as showTournament } from '@/actions/App/Http/Controllers/TournamentController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import type { Tournament } from '@/types/models';
import { Head, Link } from '@inertiajs/vue3';
import { Scale } from 'lucide-vue-next';

defineProps<{
    tournaments: Tournament[];
}>();
</script>

<template>
    <Head title="Tournois" />
    <PublicLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Tournois</h1>
            <div v-if="$page.props.auth.user" class="flex gap-4">
                <Link
                    :href="createTournament().url"
                    class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Créer un tournoi
                </Link>
                <Link
                    :href="generateRoster().url"
                    class="mt-2 inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Générer un roster de tournoi
                </Link>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
            <table class="w-full min-w-[500px]">
                <thead>
                    <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                        <th class="px-6 py-3 text-left text-sm font-semibold">Nom</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Vainqueur</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Deuxième</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Troisième</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="tournaments.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            Aucun tournoi pour le moment.
                        </td>
                    </tr>
                    <tr
                        v-for="tournament in tournaments"
                        :key="tournament.id"
                        class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        :class="tournament.is_major ? 'bg-amber-50 dark:bg-amber-950/30' : ''"
                    >
                        <td class="px-6 py-4 text-sm font-medium">
                            <Link
                                :href="showTournament(tournament.id).url"
                                class="hover:underline"
                            >
                                {{ tournament.name }}
                            </Link>
                            <Scale
                                v-if="tournament.is_balancing"
                                class="ml-1.5 inline-block h-4 w-4 text-[#706f6c] dark:text-[#A1A09A]"
                                title="Tournoi d'équilibrage"
                            />
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
                                v-if="tournament.second_place"
                                :href="showTeam(tournament.second_place.id).url"
                                class="hover:underline"
                            >
                                {{ tournament.second_place.name }}
                            </Link>
                            <span v-else class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <Link
                                v-if="tournament.third_place"
                                :href="showTeam(tournament.third_place.id).url"
                                class="hover:underline"
                            >
                                {{ tournament.third_place.name }}
                            </Link>
                            <span v-else class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </PublicLayout>
</template>
