<script setup lang="ts">
import type { ComboboxItemEmits, ComboboxItemProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { Check } from 'lucide-vue-next';
import { ComboboxItem, ComboboxItemIndicator, useForwardPropsEmits } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<ComboboxItemProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<ComboboxItemEmits>();

const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <ComboboxItem
        v-bind="forwarded"
        :class="
            cn(
                'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none data-[disabled]:pointer-events-none data-[highlighted]:bg-[#f5f5f4] data-[highlighted]:text-[#1b1b18] data-[disabled]:opacity-50 dark:data-[highlighted]:bg-[#252524] dark:data-[highlighted]:text-[#EDEDEC]',
                props.class,
            )
        "
    >
        <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
            <ComboboxItemIndicator>
                <Check class="h-4 w-4" />
            </ComboboxItemIndicator>
        </span>
        <slot />
    </ComboboxItem>
</template>
