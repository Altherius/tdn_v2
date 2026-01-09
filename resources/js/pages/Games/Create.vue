<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';
import { store } from '@/actions/App/Http/Controllers/GameController';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import Navbar from '@/components/Navbar.vue';

interface Tournament {
    id: number;
    name: string;
}

interface Team {
    id: number;
    name: string;
}

const props = defineProps<{
    tournaments: Tournament[];
    teams: Team[];
}>();

const page = usePage();

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

const selectedTournament = ref<Tournament | undefined>(undefined);
const tournamentSearchTerm = ref('');
const selectedTeam1 = ref<Team | undefined>(undefined);
const team1SearchTerm = ref('');
const selectedTeam2 = ref<Team | undefined>(undefined);
const team2SearchTerm = ref('');

const filteredTournaments = computed(() => {
    if (!tournamentSearchTerm.value) return props.tournaments;
    return props.tournaments.filter((t) => t.name.toLowerCase().includes(tournamentSearchTerm.value.toLowerCase()));
});

const filteredTeams1 = computed(() => {
    if (!team1SearchTerm.value) return props.teams;
    return props.teams.filter((t) => t.name.toLowerCase().includes(team1SearchTerm.value.toLowerCase()));
});

const filteredTeams2 = computed(() => {
    if (!team2SearchTerm.value) return props.teams;
    return props.teams.filter((t) => t.name.toLowerCase().includes(team2SearchTerm.value.toLowerCase()));
});

const team1Name = computed(() => selectedTeam1.value?.name ?? 'Domicile');
const team2Name = computed(() => selectedTeam2.value?.name ?? 'Visiteur');

const leg1Team1Score = ref<number | undefined>(undefined);
const leg1Team2Score = ref<number | undefined>(undefined);
const leg2Team1Score = ref<number | undefined>(undefined);
const leg2Team2Score = ref<number | undefined>(undefined);

function resetForm() {
    selectedTeam1.value = undefined;
    selectedTeam2.value = undefined;
    leg1Team1Score.value = undefined;
    leg1Team2Score.value = undefined;
    leg2Team1Score.value = undefined;
    leg2Team2Score.value = undefined;
}

function submitForm() {
    router.post(store().url, {
        tournament_id: selectedTournament.value?.id ?? null,
        team1_id: selectedTeam1.value?.id,
        team2_id: selectedTeam2.value?.id,
        leg1_team1_score: leg1Team1Score.value,
        leg1_team2_score: leg1Team2Score.value,
        leg2_team1_score: leg2Team1Score.value,
        leg2_team2_score: leg2Team2Score.value,
    });
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash && typeof flash === 'object' && 'success' in flash && flash.success) {
            toast.success(flash.success as string);
            resetForm();
        }
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Créer un match" />
    <Toaster
        position="top-right"
        :theme="resolvedAppearance === 'dark' ? 'dark' : 'light'"
        rich-colors
        :toast-options="{
            style: {
                marginTop: '70px',
            },
        }"
    />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Créer un match</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Enregistrer le résultat d'un match.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <form @submit.prevent="submitForm" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label>Tournoi (optionnel)</Label>
                        <Combobox
                            v-model="selectedTournament"
                            v-model:search-term="tournamentSearchTerm"
                            :filter-function="() => true"
                        >
                            <ComboboxAnchor>
                                <ComboboxInput
                                    :placeholder="selectedTournament?.name ?? 'Rechercher un tournoi...'"
                                    :display-value="(val: Tournament) => val?.name"
                                />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucun tournoi trouvé.</ComboboxEmpty>
                                <ComboboxItem v-for="tournament in filteredTournaments" :key="tournament.id" :value="tournament">
                                    {{ tournament.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="(page.props.errors as Record<string, string>)?.tournament_id" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Équipe domicile</Label>
                            <Combobox v-model="selectedTeam1" v-model:search-term="team1SearchTerm" :filter-function="() => true">
                                <ComboboxAnchor>
                                    <ComboboxInput
                                        :placeholder="selectedTeam1?.name ?? 'Rechercher une équipe...'"
                                        :display-value="(val: Team) => val?.name"
                                    />
                                    <ComboboxTrigger />
                                </ComboboxAnchor>
                                <ComboboxContent>
                                    <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                    <ComboboxItem v-for="team in filteredTeams1" :key="team.id" :value="team">
                                        {{ team.name }}
                                    </ComboboxItem>
                                </ComboboxContent>
                            </Combobox>
                            <InputError :message="(page.props.errors as Record<string, string>)?.team1_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Équipe visiteur</Label>
                            <Combobox v-model="selectedTeam2" v-model:search-term="team2SearchTerm" :filter-function="() => true">
                                <ComboboxAnchor>
                                    <ComboboxInput
                                        :placeholder="selectedTeam2?.name ?? 'Rechercher une équipe...'"
                                        :display-value="(val: Team) => val?.name"
                                    />
                                    <ComboboxTrigger />
                                </ComboboxAnchor>
                                <ComboboxContent>
                                    <ComboboxEmpty>No teams found.</ComboboxEmpty>
                                    <ComboboxItem v-for="team in filteredTeams2" :key="team.id" :value="team">
                                        {{ team.name }}
                                    </ComboboxItem>
                                </ComboboxContent>
                            </Combobox>
                            <InputError :message="(page.props.errors as Record<string, string>)?.team2_id" />
                        </div>
                    </div>

                    <div class="rounded-lg border border-[#e3e3e0] p-4 dark:border-[#3E3E3A]">
                        <h3 class="mb-4 font-semibold">Match aller</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="leg1_team1_score">Score {{ team1Name }}</Label>
                                <Input
                                    id="leg1_team1_score"
                                    v-model="leg1Team1Score"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                />
                                <InputError :message="(page.props.errors as Record<string, string>)?.leg1_team1_score" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="leg1_team2_score">Score {{ team2Name }}</Label>
                                <Input
                                    id="leg1_team2_score"
                                    v-model="leg1Team2Score"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                />
                                <InputError :message="(page.props.errors as Record<string, string>)?.leg1_team2_score" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-[#e3e3e0] p-4 dark:border-[#3E3E3A]">
                        <h3 class="mb-4 font-semibold">Match retour</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="leg2_team1_score">Score {{ team1Name }}</Label>
                                <Input
                                    id="leg2_team1_score"
                                    v-model="leg2Team1Score"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                />
                                <InputError :message="(page.props.errors as Record<string, string>)?.leg2_team1_score" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="leg2_team2_score">Score {{ team2Name }}</Label>
                                <Input
                                    id="leg2_team2_score"
                                    v-model="leg2Team2Score"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                />
                                <InputError :message="(page.props.errors as Record<string, string>)?.leg2_team2_score" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit">Créer le match</Button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
