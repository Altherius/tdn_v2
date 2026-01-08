<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from '@/components/Breadcrumb.vue';
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index as indexTournaments, show as showTournament, update } from '@/actions/App/Http/Controllers/TournamentController';
import { home } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Team {
    id: number;
    name: string;
}

interface Tournament {
    id: number;
    name: string;
    is_major: boolean;
    is_balancing: boolean;
    winner_team_id: number | null;
    second_place_team_id: number | null;
    third_place_team_id: number | null;
}

const props = defineProps<{
    tournament: Tournament;
    teams: Team[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: props.tournament.name, href: showTournament(props.tournament.id).url },
    { label: 'Éditer' },
];

const selectedWinner = ref<Team | undefined>(props.teams.find((t) => t.id === props.tournament.winner_team_id));
const selectedSecondPlace = ref<Team | undefined>(props.teams.find((t) => t.id === props.tournament.second_place_team_id));
const selectedThirdPlace = ref<Team | undefined>(props.teams.find((t) => t.id === props.tournament.third_place_team_id));

const winnerSearchTerm = ref('');
const secondPlaceSearchTerm = ref('');
const thirdPlaceSearchTerm = ref('');

const filteredWinnerTeams = computed(() => {
    if (!winnerSearchTerm.value) return props.teams;
    return props.teams.filter((team) => team.name.toLowerCase().includes(winnerSearchTerm.value.toLowerCase()));
});

const filteredSecondPlaceTeams = computed(() => {
    if (!secondPlaceSearchTerm.value) return props.teams;
    return props.teams.filter((team) => team.name.toLowerCase().includes(secondPlaceSearchTerm.value.toLowerCase()));
});

const filteredThirdPlaceTeams = computed(() => {
    if (!thirdPlaceSearchTerm.value) return props.teams;
    return props.teams.filter((team) => team.name.toLowerCase().includes(thirdPlaceSearchTerm.value.toLowerCase()));
});
</script>

<template>
    <Head :title="`Éditer ${tournament.name}`" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Éditer un tournoi</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Mettre à jour les informations d'un tournoi.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <Form v-bind="update.form(tournament.id)" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            name="name"
                            placeholder="Nom du tournoi"
                            :default-value="tournament.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="is_major"
                            type="checkbox"
                            name="is_major"
                            value="1"
                            :checked="tournament.is_major"
                            class="h-4 w-4 rounded border-[#e3e3e0] text-blue-600 focus:ring-blue-500 dark:border-[#3E3E3A] dark:bg-[#1a1a19]"
                        />
                        <Label for="is_major" class="cursor-pointer">Tournoi majeur</Label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="is_balancing"
                            type="checkbox"
                            name="is_balancing"
                            value="1"
                            :checked="tournament.is_balancing"
                            class="h-4 w-4 rounded border-[#e3e3e0] text-blue-600 focus:ring-blue-500 dark:border-[#3E3E3A] dark:bg-[#1a1a19]"
                        />
                        <Label for="is_balancing" class="cursor-pointer">Tournoi de rééquilibrage</Label>
                    </div>

                    <div class="grid gap-2">
                        <Label>Vainqueur</Label>
                        <input type="hidden" name="winner_team_id" :value="selectedWinner?.id ?? ''" />
                        <Combobox v-model="selectedWinner" v-model:search-term="winnerSearchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedWinner?.name ?? 'Rechercher une équipe...'" :display-value="(val: Team) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                <ComboboxItem v-for="team in filteredWinnerTeams" :key="team.id" :value="team">
                                    {{ team.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="errors.winner_team_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Deuxième</Label>
                        <input type="hidden" name="second_place_team_id" :value="selectedSecondPlace?.id ?? ''" />
                        <Combobox v-model="selectedSecondPlace" v-model:search-term="secondPlaceSearchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedSecondPlace?.name ?? 'Rechercher une équipe...'" :display-value="(val: Team) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                <ComboboxItem v-for="team in filteredSecondPlaceTeams" :key="team.id" :value="team">
                                    {{ team.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="errors.second_place_team_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Troisième</Label>
                        <input type="hidden" name="third_place_team_id" :value="selectedThirdPlace?.id ?? ''" />
                        <Combobox v-model="selectedThirdPlace" v-model:search-term="thirdPlaceSearchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedThirdPlace?.name ?? 'Rechercher une équipe...'" :display-value="(val: Team) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                <ComboboxItem v-for="team in filteredThirdPlaceTeams" :key="team.id" :value="team">
                                    {{ team.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="errors.third_place_team_id" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Valider les changements
                        </Button>
                    </div>
                </Form>
            </div>
        </main>
    </div>
</template>
