<script setup lang="ts">
import type { ComboboxContentProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { ComboboxContent, ComboboxPortal, ComboboxViewport } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<
        ComboboxContentProps & {
            class?: HTMLAttributes['class'];
        }
    >(),
    {
        position: 'popper',
        sideOffset: 4,
    },
);

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <ComboboxPortal>
        <ComboboxContent
            v-bind="delegatedProps"
            :class="
                cn(
                    'relative z-50 max-h-[300px] min-w-[8rem] overflow-hidden rounded-md border border-[#e3e3e0] bg-white text-[#1b1b18] shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-[#EDEDEC]',
                    position === 'popper' &&
                        'data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1',
                    props.class,
                )
            "
        >
            <ComboboxViewport
                :class="
                    cn('p-1', position === 'popper' && 'h-[var(--reka-combobox-content-available-height)] w-full min-w-[var(--reka-combobox-trigger-width)]')
                "
            >
                <slot />
            </ComboboxViewport>
        </ComboboxContent>
    </ComboboxPortal>
</template>
