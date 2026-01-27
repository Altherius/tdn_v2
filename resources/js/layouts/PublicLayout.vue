<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <Navbar />
        <main class="mx-auto w-full max-w-4xl p-6 lg:p-8">
            <slot />
        </main>
    </div>
</template>
