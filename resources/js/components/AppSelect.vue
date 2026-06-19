<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label?: string;
    helper?: string;
    error?: string;
    disabled?: boolean;
    class?: HTMLAttributes['class'];
}>();

const model = defineModel<string | number>();

const selectClass = () =>
    cn(
        'h-11 w-full rounded-md border bg-surface px-3 text-sm text-on-surface transition disabled:cursor-not-allowed disabled:bg-border-muted disabled:text-text-secondary',
        props.error
            ? 'border-danger focus-visible:ring-danger'
            : 'border-border-default hover:border-secondary/50 focus-visible:ring-ring',
        props.class,
    );
</script>

<template>
    <label class="grid gap-2">
        <span v-if="props.label" class="text-sm font-medium text-text-primary">
            {{ props.label }}
        </span>
        <select
            v-if="model !== undefined"
            v-bind="$attrs"
            v-model="model"
            :disabled="props.disabled"
            :aria-invalid="Boolean(props.error)"
            :class="selectClass()"
        >
            <slot />
        </select>
        <select
            v-else
            v-bind="$attrs"
            :disabled="props.disabled"
            :aria-invalid="Boolean(props.error)"
            :class="selectClass()"
        >
            <slot />
        </select>
        <span v-if="props.error" class="text-sm font-medium text-danger">
            {{ props.error }}
        </span>
        <span v-else-if="props.helper" class="text-sm text-text-secondary">
            {{ props.helper }}
        </span>
    </label>
</template>
