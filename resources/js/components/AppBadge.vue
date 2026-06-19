<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        variant?: 'success' | 'warning' | 'danger' | 'info' | 'neutral';
        class?: HTMLAttributes['class'];
    }>(),
    {
        variant: 'neutral',
    },
);

const variants = {
    success: 'border-success/25 bg-success/10 text-success',
    warning: 'border-warning/30 bg-warning/10 text-warning',
    danger: 'border-danger/25 bg-danger/10 text-danger',
    info: 'border-secondary/25 bg-secondary/10 text-on-surface',
    neutral: 'border-border-default bg-border-muted text-text-primary',
};
</script>

<template>
    <span
        :class="
            cn(
                'inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium',
                variants[props.variant],
                props.class,
            )
        "
    >
        <slot />
    </span>
</template>
