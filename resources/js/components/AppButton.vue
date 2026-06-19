<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'ghost';
        size?: 'sm' | 'md' | 'lg';
        loading?: boolean;
        disabled?: boolean;
        type?: 'button' | 'submit' | 'reset';
        class?: HTMLAttributes['class'];
    }>(),
    {
        variant: 'primary',
        size: 'md',
        type: 'button',
    },
);

const variants = {
    primary: 'bg-primary text-on-primary hover:bg-primary/90',
    secondary: 'bg-secondary text-on-primary hover:bg-secondary/90',
    success: 'bg-success text-on-primary hover:bg-success/90',
    danger: 'bg-danger text-on-primary hover:bg-danger/90',
    ghost: 'bg-transparent text-text-primary hover:bg-border-muted',
};

const sizes = {
    sm: 'h-9 gap-2 rounded-md px-3 text-sm',
    md: 'h-10 gap-2 rounded-md px-4 text-sm',
    lg: 'h-12 gap-2 rounded-lg px-5 text-base',
};
</script>

<template>
    <button
        :type="props.type"
        :disabled="props.disabled || props.loading"
        :class="
            cn(
                'inline-flex items-center justify-center font-medium transition duration-200 disabled:pointer-events-none disabled:opacity-60',
                'focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background',
                variants[props.variant],
                sizes[props.size],
                props.class,
            )
        "
    >
        <LoaderCircle v-if="props.loading" class="size-4 animate-spin" />
        <slot />
    </button>
</template>
