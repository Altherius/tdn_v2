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
import { index as indexTournaments } from '@/actions/App/Http/Controllers/TournamentController';
import { show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { X, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Region {
    id: number;
    name: string;
}

interface Team {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
}

interface TicketEntry {
    team: Team | undefined;
    tickets: number;
}

const props = defineProps<{
    teams: Team[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: 'Générer un roster' },
];

// Form state
const slots = ref(8);
const qualifiedTeams = ref<Team[]>([]);
const ticketEntries = ref<TicketEntry[]>([{ team: undefined, tickets: 1 }]);
const generatedRoster = ref<Team[] | null>(null);

// Qualified teams combobox state
const qualifiedSearchTerm = ref('');
const selectedQualifiedTeam = ref<Team | undefined>(undefined);

// Computed values
const remainingSlots = computed(() => slots.value - qualifiedTeams.value.length);

const totalTickets = computed(() =>
    ticketEntries.value.reduce((sum, entry) => sum + (entry.team ? entry.tickets : 0), 0)
);

const usedTeamIds = computed(() => {
    const qualified = qualifiedTeams.value.map(t => t.id);
    const ticketed = ticketEntries.value.filter(e => e.team).map(e => e.team!.id);
    return new Set([...qualified, ...ticketed]);
});

const availableTeamsForQualified = computed(() => {
    if (!qualifiedSearchTerm.value) {
        return props.teams.filter(t => !usedTeamIds.value.has(t.id));
    }
    return props.teams.filter(
        t => !usedTeamIds.value.has(t.id) && t.name.toLowerCase().includes(qualifiedSearchTerm.value.toLowerCase())
    );
});

const probabilities = computed(() =>
    ticketEntries.value
        .filter(entry => entry.team)
        .map(entry => ({
            team: entry.team!,
            tickets: entry.tickets,
            probability: totalTickets.value > 0 ? (entry.tickets / totalTickets.value) * 100 : 0,
        }))
);

const validationError = computed(() => {
    if (qualifiedTeams.value.length > slots.value) {
        return "Il y plus d'équipes qualifiées que de places";
    }
    const ticketedTeamsCount = ticketEntries.value.filter(e => e.team).length;
    if (remainingSlots.value > 0 && totalTickets.value === 0) {
        return 'Ajoutez des équipes pour compléter le nombre de places';
    }
    if (remainingSlots.value > ticketedTeamsCount) {
        return `Il faut encore au moins ${remainingSlots.value} équipes pour compléter le tournoi`;
    }
    return null;
});

// Functions
function getAvailableTeamsForEntry(currentEntryIndex: number): Team[] {
    const usedInOtherEntries = new Set(
        ticketEntries.value
            .filter((e, i) => i !== currentEntryIndex && e.team)
            .map(e => e.team!.id)
    );
    const qualifiedIds = new Set(qualifiedTeams.value.map(t => t.id));

    return props.teams.filter(t => !usedInOtherEntries.has(t.id) && !qualifiedIds.has(t.id));
}

function addQualifiedTeam() {
    if (selectedQualifiedTeam.value && !qualifiedTeams.value.find(t => t.id === selectedQualifiedTeam.value!.id)) {
        qualifiedTeams.value.push(selectedQualifiedTeam.value);
        selectedQualifiedTeam.value = undefined;
        qualifiedSearchTerm.value = '';
    }
}

function removeQualifiedTeam(teamId: number) {
    qualifiedTeams.value = qualifiedTeams.value.filter(t => t.id !== teamId);
}

function addTicketEntry() {
    ticketEntries.value.push({ team: undefined, tickets: 1 });
}

function removeTicketEntry(index: number) {
    if (ticketEntries.value.length > 1) {
        ticketEntries.value.splice(index, 1);
    } else {
        ticketEntries.value[0] = { team: undefined, tickets: 1 };
    }
}

// Auto-add new entry when last entry is filled
watch(
    () => ticketEntries.value[ticketEntries.value.length - 1]?.team,
    (newTeam) => {
        if (newTeam) {
            addTicketEntry();
        }
    }
);

// Watch for qualified team selection
watch(selectedQualifiedTeam, (newTeam) => {
    if (newTeam) {
        addQualifiedTeam();
    }
});

function generate() {
    const roster = [...qualifiedTeams.value];

    // Build ticket pool
    let pool = ticketEntries.value
        .filter(entry => entry.team)
        .flatMap(entry => Array(entry.tickets).fill(entry.team));

    // Weighted random selection
    for (let i = 0; i < remainingSlots.value && pool.length > 0; i++) {
        const randomIndex = Math.floor(Math.random() * pool.length);
        const selected = pool[randomIndex];
        roster.push(selected);
        // Remove all tickets for selected team
        pool = pool.filter(t => t.id !== selected.id);
    }

    generatedRoster.value = roster;
}

function regenerate() {
    generate();
}
</script>

<template>
    <Head title="Générer un roster" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Générer une composition de tournoi</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Créer un tournoi avec un système de lotterie avec tickets.
                </p>
            </div>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <div class="flex flex-col gap-6">
                    <!-- Slots Input -->
                    <div class="grid gap-2">
                        <Label for="slots">Nombre de places</Label>
                        <Input
                            id="slots"
                            type="number"
                            v-model.number="slots"
                            min="1"
                            class="w-32"
                        />
                    </div>

                    <!-- Qualified Teams Section -->
                    <div class="grid gap-2">
                        <Label>Équipes qualifiées (ajoutées automatiquement)</Label>

                        <!-- Selected tags -->
                        <div v-if="qualifiedTeams.length > 0" class="flex flex-wrap gap-2 mb-2">
                            <span
                                v-for="team in qualifiedTeams"
                                :key="team.id"
                                class="inline-flex items-center gap-1 rounded-full bg-[#f5f5f4] px-3 py-1 text-sm dark:bg-[#252524]"
                            >
                                {{ team.name }}
                                <button
                                    @click="removeQualifiedTeam(team.id)"
                                    class="ml-1 rounded-full p-0.5 hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A]"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </span>
                        </div>

                        <!-- Combobox for adding qualified teams -->
                        <Combobox
                            v-model="selectedQualifiedTeam"
                            v-model:search-term="qualifiedSearchTerm"
                            :filter-function="() => true"
                        >
                            <ComboboxAnchor>
                                <ComboboxInput placeholder="Rechercher une équipe..." />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                <ComboboxItem
                                    v-for="team in availableTeamsForQualified"
                                    :key="team.id"
                                    :value="team"
                                >
                                    {{ team.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                    </div>

                    <!-- Ticket Entries Section -->
                    <div class="grid gap-2">
                        <Label>Équipes avec tickets (lotterie)</Label>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Places restantes : {{ remainingSlots }}
                        </p>

                        <div class="flex flex-col gap-3 mt-2">
                            <div
                                v-for="(entry, index) in ticketEntries"
                                :key="index"
                                class="flex items-center gap-3"
                            >
                                <!-- Team combobox -->
                                <div class="flex-1">
                                    <Combobox
                                        v-model="entry.team"
                                        :filter-function="() => true"
                                    >
                                        <ComboboxAnchor>
                                            <ComboboxInput
                                                :placeholder="entry.team?.name ?? 'Rechercher une équipe...'"
                                                :display-value="(val: Team) => val?.name"
                                            />
                                            <ComboboxTrigger />
                                        </ComboboxAnchor>
                                        <ComboboxContent>
                                            <ComboboxEmpty>Aucune équipe trouvée.</ComboboxEmpty>
                                            <ComboboxItem
                                                v-for="team in getAvailableTeamsForEntry(index)"
                                                :key="team.id"
                                                :value="team"
                                            >
                                                {{ team.name }}
                                            </ComboboxItem>
                                        </ComboboxContent>
                                    </Combobox>
                                </div>

                                <!-- Tickets input -->
                                <div class="w-24">
                                    <Input
                                        type="number"
                                        v-model.number="entry.tickets"
                                        min="1"
                                        placeholder="Tickets"
                                        :disabled="!entry.team"
                                    />
                                </div>

                                <!-- Remove button -->
                                <button
                                    @click="removeTicketEntry(index)"
                                    class="p-2 text-[#706f6c] hover:text-red-500 dark:text-[#A1A09A] dark:hover:text-red-400"
                                    :class="{ 'opacity-50 cursor-not-allowed': ticketEntries.length === 1 && !entry.team }"
                                    :disabled="ticketEntries.length === 1 && !entry.team"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Probability Display -->
                    <div v-if="probabilities.length > 0" class="grid gap-2">
                        <Label>Probabilités de sélection</Label>
                        <div class="rounded-lg border border-[#e3e3e0] bg-[#f8f8f7] p-4 dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <div class="flex flex-col gap-2">
                                <div
                                    v-for="prob in probabilities"
                                    :key="prob.team.id"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span>{{ prob.team.name }}</span>
                                    <span class="font-mono">
                                        {{ prob.tickets }} ticket{{ prob.tickets > 1 ? 's' : '' }} &middot;
                                        {{ prob.probability.toFixed(1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Error -->
                    <InputError :message="validationError ?? undefined" />

                    <!-- Generate Button -->
                    <div class="flex justify-end">
                        <Button
                            @click="generate"
                            :disabled="!!validationError"
                        >
                            Générer
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Result Table -->
            <div v-if="generatedRoster" class="mt-8">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Composition générée</h2>
                    <Button variant="outline" @click="regenerate">
                        Régénérer
                    </Button>
                </div>

                <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                                <th class="px-6 py-3 text-left text-sm font-semibold">#</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Équipe</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Région</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold">Classement</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(team, index) in generatedRoster"
                                :key="team.id"
                                class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                            >
                                <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <Link
                                        :href="showTeam(team.id).url"
                                        class="hover:underline"
                                    >
                                        {{ team.name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ team.region.name }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-mono">
                                    {{ team.elo_rating }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        v-if="qualifiedTeams.find(t => t.id === team.id)"
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400"
                                    >
                                        Qualifié
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                                    >
                                        Lotterie
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
