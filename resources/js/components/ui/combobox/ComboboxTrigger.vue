<script setup lang="ts">
import type { ComboboxTriggerProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { ChevronDown } from 'lucide-vue-next';
import { ComboboxTrigger } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<ComboboxTriggerProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <ComboboxTrigger v-bind="delegatedProps" :class="cn('shrink-0', props.class)">
        <slot>
            <ChevronDown class="h-4 w-4 opacity-50" />
        </slot>
    </ComboboxTrigger>
</template>
