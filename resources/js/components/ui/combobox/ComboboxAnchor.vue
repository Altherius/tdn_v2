<script setup lang="ts">
import type { ComboboxAnchorProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { ComboboxAnchor } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<ComboboxAnchorProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <ComboboxAnchor
        v-bind="delegatedProps"
        :class="
            cn(
                'flex h-9 w-full items-center justify-between rounded-md border border-[#e3e3e0] bg-transparent px-3 py-2 text-sm shadow-xs ring-offset-background focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 dark:border-[#3E3E3A] dark:bg-[#1a1a19]',
                props.class,
            )
        "
    >
        <slot />
    </ComboboxAnchor>
</template>
