<script setup lang="ts">
import { create as createGame } from '@/actions/App/Http/Controllers/GameController';
import { index as indexMatchup } from '@/actions/App/Http/Controllers/MatchupController';
import { create as createTeam } from '@/actions/App/Http/Controllers/TeamController';
import { index as indexTournaments } from '@/actions/App/Http/Controllers/TournamentController';
import { useAppearance } from '@/composables/useAppearance';
import { home, login, logout } from '@/routes';
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut, Menu, Moon, Sun, User, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const currentPath = computed(() => page.url.split('?')[0]);

const { resolvedAppearance, updateAppearance } = useAppearance();

const mobileMenuOpen = ref(false);

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

function handleLogout() {
    router.post(logout().url);
}

function closeMobileMenu() {
    mobileMenuOpen.value = false;
}
</script>

<template>
    <header class="w-full border-b border-[#e3e3e0] bg-white px-4 py-4 dark:border-[#3E3E3A] dark:bg-[#161615] sm:px-6">
        <nav class="mx-auto flex max-w-4xl items-center justify-between">
            <!-- Mobile menu button -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="flex h-9 w-9 items-center justify-center rounded-md border border-[#e3e3e0] bg-[#FDFDFC] transition-colors hover:bg-[#f5f5f4] dark:border-[#3E3E3A] dark:bg-[#1a1a19] dark:hover:bg-[#252524] md:hidden"
            >
                <X v-if="mobileMenuOpen" class="h-5 w-5" />
                <Menu v-else class="h-5 w-5" />
            </button>

            <!-- Desktop navigation -->
            <div class="hidden items-center gap-4 md:flex">
                <Link
                    :href="home().url"
                    class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                    :class="currentPath === home().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                >
                    Classement
                </Link>
                <Link
                    :href="indexTournaments().url"
                    class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                    :class="currentPath === indexTournaments().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                >
                    Tournois
                </Link>
                <Link
                    :href="indexMatchup().url"
                    class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                    :class="currentPath.startsWith('/matchup') ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                >
                    Confrontation
                </Link>
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="createTeam().url"
                        class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                        :class="currentPath === createTeam().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                    >
                        Créer une équipe
                    </Link>
                    <Link
                        :href="createGame().url"
                        class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                        :class="currentPath === createGame().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                    >
                        Créer un match
                    </Link>
                </template>
            </div>

            <!-- Desktop right side -->
            <div class="flex items-center gap-4">
                <template v-if="$page.props.auth.user">
                    <div class="hidden items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] sm:flex">
                        <User class="h-4 w-4" />
                        <span>{{ $page.props.auth.user.name }}</span>
                    </div>
                    <button
                        @click="handleLogout"
                        class="hidden items-center gap-1 text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC] md:flex"
                    >
                        <LogOut class="h-4 w-4" />
                        Déconnexion
                    </button>
                </template>
                <template v-else>
                    <Link
                        :href="login().url"
                        class="hidden text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC] md:block"
                    >
                        Se connecter
                    </Link>
                </template>
                <button
                    @click="toggleTheme"
                    class="flex h-9 w-9 items-center justify-center rounded-md border border-[#e3e3e0] bg-[#FDFDFC] transition-colors hover:bg-[#f5f5f4] dark:border-[#3E3E3A] dark:bg-[#1a1a19] dark:hover:bg-[#252524]"
                    :title="resolvedAppearance === 'dark' ? 'Mode clair' : 'Mode sombre'"
                >
                    <Sun v-if="resolvedAppearance === 'dark'" class="h-5 w-5" />
                    <Moon v-else class="h-5 w-5" />
                </button>
            </div>
        </nav>

        <!-- Mobile menu -->
        <div
            v-if="mobileMenuOpen"
            class="mt-4 flex flex-col gap-3 border-t border-[#e3e3e0] pt-4 dark:border-[#3E3E3A] md:hidden"
        >
            <Link
                :href="home().url"
                @click="closeMobileMenu"
                class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                :class="currentPath === home().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
            >
                Classement
            </Link>
            <Link
                :href="indexTournaments().url"
                @click="closeMobileMenu"
                class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                :class="currentPath === indexTournaments().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
            >
                Tournois
            </Link>
            <Link
                :href="indexMatchup().url"
                @click="closeMobileMenu"
                class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                :class="currentPath.startsWith('/matchup') ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
            >
                Confrontation
            </Link>
            <template v-if="$page.props.auth.user">
                <Link
                    :href="createTeam().url"
                    @click="closeMobileMenu"
                    class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                    :class="currentPath === createTeam().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                >
                    Créer une équipe
                </Link>
                <Link
                    :href="createGame().url"
                    @click="closeMobileMenu"
                    class="text-sm hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                    :class="currentPath === createGame().url ? 'font-medium text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'"
                >
                    Créer un match
                </Link>
                <div class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    <User class="h-4 w-4" />
                    <span>{{ $page.props.auth.user.name }}</span>
                </div>
                <button
                    @click="handleLogout"
                    class="flex items-center gap-1 text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    <LogOut class="h-4 w-4" />
                    Déconnexion
                </button>
            </template>
            <template v-else>
                <Link
                    :href="login().url"
                    @click="closeMobileMenu"
                    class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Se connecter
                </Link>
            </template>
        </div>
    </header>
</template>
