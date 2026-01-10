<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    code: string | null;
    size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'sm',
});

const sizeClasses = {
    sm: 'text-base',
    md: 'text-lg',
    lg: 'text-xl',
};

const flagCode = computed(() => {
    if (!props.code) return null;
    return props.code.toLowerCase();
});
</script>

<template>
    <span
        v-if="flagCode"
        :class="['fi fis inline-block rounded-sm', `fi-${flagCode}`, sizeClasses[size]]"
        :title="flagCode.toUpperCase()"
    />
</template>
