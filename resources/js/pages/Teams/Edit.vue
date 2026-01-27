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
import ContentCard from '@/components/ContentCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { update } from '@/actions/App/Http/Controllers/TeamController';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { Country, Region, Team } from '@/types/models';

const props = defineProps<{
    team: Team;
    regions: Region[];
    countries: Country[];
}>();

const selectedRegion = ref<Region | undefined>(props.regions.find((r) => r.id === props.team.region_id));
const searchTerm = ref('');

const filteredRegions = computed(() => {
    if (!searchTerm.value) return props.regions;
    return props.regions.filter((region) => region.name.toLowerCase().includes(searchTerm.value.toLowerCase()));
});

const selectedCountry = ref<Country | undefined>(props.team.country ?? undefined);
const countrySearchTerm = ref('');

const filteredCountries = computed(() => {
    if (!countrySearchTerm.value) return props.countries;
    return props.countries.filter((country) => country.name.toLowerCase().includes(countrySearchTerm.value.toLowerCase()));
});
</script>

<template>
    <Head :title="`Éditer ${team.name}`" />
    <PublicLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Éditer une équipe</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Modifier les données d'une équipe.</p>
        </div>

        <ContentCard>
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
                        Valider les changements
                    </Button>
                </div>
            </Form>
        </ContentCard>
    </PublicLayout>
</template>
