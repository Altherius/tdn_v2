<script setup lang="ts">
import type { ComboboxInputProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { ComboboxInput } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<ComboboxInputProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <ComboboxInput
        v-bind="delegatedProps"
        :class="
            cn(
                'flex-1 bg-transparent text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50',
                props.class,
            )
        "
    />
</template>
