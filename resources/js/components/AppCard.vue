<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        variant?: 'default' | 'elevated' | 'bordered';
        class?: HTMLAttributes['class'];
    }>(),
    {
        variant: 'default',
    },
);

const variants = {
    default: 'border border-border-muted app-shadow-card',
    elevated: 'border border-border-muted app-shadow-elevated',
    bordered: 'border border-border-default',
};
</script>

<template>
    <section
        :class="
            cn(
                'rounded-lg bg-card-background text-on-surface',
                variants[props.variant],
                props.class,
            )
        "
    >
        <slot />
    </section>
</template>
