<script setup lang="ts">
import Breadcrumb from '@/components/Breadcrumb.vue';
import GamesHistoryTable from '@/components/GamesHistoryTable.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { edit as editTournament, index as indexTournaments } from '@/actions/App/Http/Controllers/TournamentController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import type { BreadcrumbItem } from '@/types';
import type { Game, Tournament } from '@/types/models';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    tournament: Tournament;
    games: Game[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: props.tournament.name },
];
</script>

<template>
    <Head :title="tournament.name" />
    <PublicLayout>
        <Breadcrumb :items="breadcrumbs" />
        <div class="mb-6">
            <h1 class="text-2xl font-bold">{{ tournament.name }}</h1>
            <Link
                v-if="$page.props.auth.user"
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
                    v-if="tournament.second_place"
                    :href="showTeam(tournament.second_place.id).url"
                    class="text-lg font-semibold hover:underline"
                >
                    {{ tournament.second_place.name }}
                </Link>
                <p v-else class="text-lg text-[#706f6c] dark:text-[#A1A09A]">-</p>
            </div>

            <div class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <p class="mb-2 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Troisième</p>
                <Link
                    v-if="tournament.third_place"
                    :href="showTeam(tournament.third_place.id).url"
                    class="text-lg font-semibold hover:underline"
                >
                    {{ tournament.third_place.name }}
                </Link>
                <p v-else class="text-lg text-[#706f6c] dark:text-[#A1A09A]">-</p>
            </div>
        </div>

        <h2 class="mb-4 text-lg font-semibold">Liste des matchs</h2>

        <GamesHistoryTable
            :games="games"
            :show-tournament-column="false"
            :colorize-results="false"
            empty-message="Aucun match joué dans ce tournoi."
        />
    </PublicLayout>
</template>
