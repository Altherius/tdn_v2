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
import { Spinner } from '@/components/ui/spinner';
import { useAppearance } from '@/composables/useAppearance';
import { show as showTeam, update } from '@/actions/App/Http/Controllers/TeamController';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Navbar from '@/components/Navbar.vue';

interface Region {
    id: number;
    name: string;
}

interface Team {
    id: number;
    name: string;
    region_id: number;
    elo_rating: number;
}

const props = defineProps<{
    team: Team;
    regions: Region[];
}>();

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

const selectedRegion = ref<Region | undefined>(props.regions.find((r) => r.id === props.team.region_id));
const searchTerm = ref('');

const filteredRegions = computed(() => {
    if (!searchTerm.value) return props.regions;
    return props.regions.filter((region) => region.name.toLowerCase().includes(searchTerm.value.toLowerCase()));
});
</script>

<template>
    <Head :title="`Éditer ${team.name}`" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Éditer une équipe</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Modifier les données d'une équipe.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <Form v-bind="update.form(team.id)" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            name="name"
                            placeholder="Nom de l'équipe"
                            :default-value="team.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Région</Label>
                        <input type="hidden" name="region_id" :value="selectedRegion?.id ?? ''" />
                        <Combobox v-model="selectedRegion" v-model:search-term="searchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedRegion?.name ?? 'Rechercher une région...'" :display-value="(val: Region) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucune région trouvée.</ComboboxEmpty>
                                <ComboboxItem v-for="region in filteredRegions" :key="region.id" :value="region">
                                    {{ region.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="errors.region_id" />
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
