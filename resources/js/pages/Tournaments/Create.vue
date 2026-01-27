<script setup lang="ts">
import Breadcrumb from '@/components/Breadcrumb.vue';
import ContentCard from '@/components/ContentCard.vue';
import InputError from '@/components/InputError.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index as indexTournaments, store } from '@/actions/App/Http/Controllers/TournamentController';
import { Form, Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: 'Créer' },
];
</script>

<template>
    <Head title="Créer un tournoi" />
    <PublicLayout>
        <Breadcrumb :items="breadcrumbs" />
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Créer un tournoi</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Ajouter un nouveau tournoi.</p>
        </div>

        <ContentCard>
            <Form v-bind="store.form()" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nom</Label>
                    <Input id="name" type="text" required autofocus name="name" placeholder="Nom du tournoi" />
                    <InputError :message="errors.name" />
                </div>

                <div class="flex items-center gap-3">
                    <input
                        id="is_major"
                        type="checkbox"
                        name="is_major"
                        value="1"
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
                        class="h-4 w-4 rounded border-[#e3e3e0] text-blue-600 focus:ring-blue-500 dark:border-[#3E3E3A] dark:bg-[#1a1a19]"
                    />
                    <Label for="is_balancing" class="cursor-pointer">Tournoi de rééquilibrage</Label>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        Créer le tournoi
                    </Button>
                </div>
            </Form>
        </ContentCard>
    </PublicLayout>
</template>
