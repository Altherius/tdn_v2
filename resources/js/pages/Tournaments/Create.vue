<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from '@/components/Breadcrumb.vue';
import InputError from '@/components/InputError.vue';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index as indexTournaments, store } from '@/actions/App/Http/Controllers/TournamentController';
import { home } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Tournois', href: indexTournaments().url },
    { label: 'Créer' },
];
</script>

<template>
    <Head title="Créer un tournoi" />
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <Breadcrumb :items="breadcrumbs" />
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Créer un tournoi</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Ajouter un nouveau tournoi.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
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
            </div>
        </main>
    </div>
</template>
