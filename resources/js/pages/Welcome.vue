<script setup lang="ts">
import { create as createGame } from '@/actions/App/Http/Controllers/GameController';
import { create as createTeam, show as showTeam } from '@/actions/App/Http/Controllers/TeamController';
import { index as indexTournaments } from '@/actions/App/Http/Controllers/TournamentController';
import { useAppearance } from '@/composables/useAppearance';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from 'lucide-vue-next';

interface Region {
    id: number;
    name: string;
}

type GameResult = 'win' | 'draw' | 'loss';

interface Team {
    id: number;
    name: string;
    elo_rating: number;
    region: Region;
    lastGames: GameResult[];
}

withDefaults(
    defineProps<{
        canRegister: boolean;
        teams: Team[];
    }>(),
    {
        canRegister: true,
        teams: () => [],
    },
);

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

</script>

<template>
    <Head title="Teams Ranking" />
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
                        Tournaments
                    </Link>
                    <Link
                        :href="createTeam().url"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        + Create Team
                    </Link>
                    <Link
                        :href="createGame().url"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        + Create Game
                    </Link>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <h1 class="mb-6 text-2xl font-bold">Teams Ranking</h1>

            <div class="overflow-hidden rounded-lg border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#f8f8f7] dark:border-[#3E3E3A] dark:bg-[#1a1a19]">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Team</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Region</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold">Rating</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Last games</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="team in teams"
                            :key="team.id"
                            class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                        >
                            <td class="px-6 py-4 text-sm font-medium">
                                <Link
                                    :href="showTeam(team.id).url"
                                    class="hover:underline"
                                >
                                    {{ team.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ team.region.name }}</td>
                            <td class="px-6 py-4 text-right text-sm font-mono">{{ team.elo_rating }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1">
                                    <span
                                        v-for="(result, index) in [...team.lastGames].reverse()"
                                        :key="index"
                                        class="h-3 w-3 rounded-full"
                                        :class="{
                                            'bg-green-500': result === 'win',
                                            'bg-yellow-500': result === 'draw',
                                            'bg-red-500': result === 'loss',
                                        }"
                                        :title="result"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
