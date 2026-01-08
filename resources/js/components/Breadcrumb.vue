<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

defineProps<{
    items: BreadcrumbItem[];
}>();
</script>

<template>
    <nav class="mb-2 flex items-center gap-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">
        <template v-for="(item, index) in items" :key="index">
            <ChevronRight v-if="index > 0" class="h-4 w-4" />
            <Link
                v-if="item.href"
                :href="item.href"
                class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
            >
                {{ item.label }}
            </Link>
            <span v-else>{{ item.label }}</span>
        </template>
    </nav>
</template>
