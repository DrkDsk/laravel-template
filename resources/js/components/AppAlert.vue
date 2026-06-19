<script setup lang="ts">
import { AlertCircle, CheckCircle2, Info, TriangleAlert } from '@lucide/vue';
import type { Component, HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        variant?: 'success' | 'warning' | 'danger' | 'info';
        title?: string;
        class?: HTMLAttributes['class'];
    }>(),
    {
        variant: 'info',
    },
);

const styles = {
    success: 'border-success/25 bg-success/10 text-on-surface',
    warning: 'border-warning/30 bg-warning/10 text-on-surface',
    danger: 'border-danger/25 bg-danger/10 text-on-surface',
    info: 'border-secondary/25 bg-secondary/10 text-on-surface',
};

const icons: Record<string, Component> = {
    success: CheckCircle2,
    warning: TriangleAlert,
    danger: AlertCircle,
    info: Info,
};
</script>

<template>
    <div
        :class="
            cn(
                'flex gap-3 rounded-lg border p-4 text-sm',
                styles[props.variant],
                props.class,
            )
        "
    >
        <component :is="icons[props.variant]" class="mt-0.5 size-5 shrink-0" />
        <div class="space-y-1">
            <p v-if="props.title" class="font-semibold text-on-surface">
                {{ props.title }}
            </p>
            <div class="text-text-secondary">
                <slot />
            </div>
        </div>
    </div>
</template>
