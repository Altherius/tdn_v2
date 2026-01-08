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
    <Head :title="`Edit ${team.name}`" />
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
                        :href="showTeam(team.id).url"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        &larr; Back to team
                    </Link>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Edit Team</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Update team information.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <Form v-bind="update.form(team.id)" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            name="name"
                            placeholder="Team name"
                            :default-value="team.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Region</Label>
                        <input type="hidden" name="region_id" :value="selectedRegion?.id ?? ''" />
                        <Combobox v-model="selectedRegion" v-model:search-term="searchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedRegion?.name ?? 'Search region...'" :display-value="(val: Region) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>No regions found.</ComboboxEmpty>
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
                            Save Changes
                        </Button>
                    </div>
                </Form>
            </div>
        </main>
    </div>
</template>
