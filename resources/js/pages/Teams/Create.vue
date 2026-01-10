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
import { home } from '@/routes';
import { store } from '@/actions/App/Http/Controllers/TeamController';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Navbar from '@/components/Navbar.vue';

interface Region {
    id: number;
    name: string;
}

interface Country {
    id: number;
    name: string;
    code: string;
}

const props = defineProps<{
    regions: Region[];
    countries: Country[];
}>();

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

const selectedRegion = ref<Region | undefined>(undefined);
const searchTerm = ref('');

const filteredRegions = computed(() => {
    if (!searchTerm.value) return props.regions;
    return props.regions.filter((region) => region.name.toLowerCase().includes(searchTerm.value.toLowerCase()));
});

const selectedCountry = ref<Country | undefined>(undefined);
const countrySearchTerm = ref('');

const filteredCountries = computed(() => {
    if (!countrySearchTerm.value) return props.countries;
    return props.countries.filter((country) => country.name.toLowerCase().includes(countrySearchTerm.value.toLowerCase()));
});
</script>

<template>
    <Head title="Créer une équipe" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Créer une équipe</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Ajouter une nouvelle équipe au classement.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <Form v-bind="store.form()" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input id="name" type="text" required autofocus name="name" placeholder="Nom de l'équipe" />
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

                    <div class="grid gap-2">
                        <Label>Pays</Label>
                        <input type="hidden" name="country_id" :value="selectedCountry?.id ?? ''" />
                        <Combobox v-model="selectedCountry" v-model:search-term="countrySearchTerm" :filter-function="() => true">
                            <ComboboxAnchor>
                                <ComboboxInput :placeholder="selectedCountry?.name ?? 'Rechercher un pays...'" :display-value="(val: Country) => val?.name" />
                                <ComboboxTrigger />
                            </ComboboxAnchor>
                            <ComboboxContent>
                                <ComboboxEmpty>Aucun pays trouvé.</ComboboxEmpty>
                                <ComboboxItem v-for="country in filteredCountries" :key="country.id" :value="country">
                                    {{ country.name }}
                                </ComboboxItem>
                            </ComboboxContent>
                        </Combobox>
                        <InputError :message="errors.country_id" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Créer l'équipe
                        </Button>
                    </div>
                </Form>
            </div>
        </main>
    </div>
</template>
