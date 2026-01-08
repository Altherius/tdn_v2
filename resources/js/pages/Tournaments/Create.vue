<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useAppearance } from '@/composables/useAppearance';
import { index as indexTournaments, store } from '@/actions/App/Http/Controllers/TournamentController';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <Head title="Create Tournament" />
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
                        :href="indexTournaments().url"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        &larr; Back to tournaments
                    </Link>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Create Tournament</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Add a new tournament.</p>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
            >
                <Form v-bind="store.form()" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" type="text" required autofocus name="name" placeholder="Tournament name" />
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
                        <Label for="is_major" class="cursor-pointer">Major tournament</Label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="is_balancing"
                            type="checkbox"
                            name="is_balancing"
                            value="1"
                            class="h-4 w-4 rounded border-[#e3e3e0] text-blue-600 focus:ring-blue-500 dark:border-[#3E3E3A] dark:bg-[#1a1a19]"
                        />
                        <Label for="is_balancing" class="cursor-pointer">Balancing tournament</Label>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Create Tournament
                        </Button>
                    </div>
                </Form>
            </div>
        </main>
    </div>
</template>
