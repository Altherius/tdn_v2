<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from '@/components/Breadcrumb.vue';
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
import { Label } from '@/components/ui/label';
import { home } from '@/routes';
import { show as showMatchup } from '@/actions/App/Http/Controllers/MatchupController';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Team {
    id: number;
    name: string;
}

const props = defineProps<{
    teams: Team[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Classement', href: home().url },
    { label: 'Analyse de confrontation' },
];

const selectedTeam1 = ref<Team | undefined>(undefined);
const team1SearchTerm = ref('');
const selectedTeam2 = ref<Team | undefined>(undefined);
const team2SearchTerm = ref('');

const filteredTeams1 = computed(() => {
    if (!team1SearchTerm.value) return props.teams;
    return props.teams.filter((t) => t.name.toLowerCase().startsWith(team1SearchTerm.value.toLowerCase()));
});

const filteredTeams2 = computed(() => {
    if (!team2SearchTerm.value) return props.teams;
    return props.teams.filter((t) => t.name.toLowerCase().startsWith(team2SearchTerm.value.toLowerCase()));
});

const canSubmit = computed(() => {
    return selectedTeam1.value && selectedTeam2.value && selectedTeam1.value.id !== selectedTeam2.value.id;
});

function submitForm() {
    if (!selectedTeam1.value || !selectedTeam2.value) return;
    router.get(showMatchup([selectedTeam1.value.id, selectedTeam2.value.id]).url);
}
</script>

<template>
    <Head title="Analyse de confrontation" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Analyse de confrontation</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Comparez deux équipes et analysez leur historique.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <form @submit.prevent="submitForm" class="flex flex-col gap-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Première équipe</Label>
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
                        </div>

                        <div class="grid gap-2">
                            <Label>Deuxième équipe</Label>
                            <Combobox v-model="selectedTeam2" v-model:search-term="team2SearchTerm" :filter-function="() => true">
                                <ComboboxAnchor>
                                    <ComboboxInput
                                        :placeholder="selectedTeam2?.name ?? 'Rechercher une équipe...'"
                                        :display-value="(val: Team) => val?.name"
                                    />
                                    <ComboboxTrigger />
                                </ComboboxAnchor>
                                <ComboboxContent>
                                    <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                    <ComboboxItem v-for="team in filteredTeams2" :key="team.id" :value="team">
                                        {{ team.name }}
                                    </ComboboxItem>
                                </ComboboxContent>
                            </Combobox>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="!canSubmit">Analyser</Button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
