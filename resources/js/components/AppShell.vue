<script setup lang="ts">
import type { AppVariant } from '@/types';

withDefaults(
    defineProps<{
        variant?: AppVariant;
    }>(),
    {
        variant: 'sidebar',
    },
);
</script>

<template>
    <div
        :class="
            variant === 'sidebar'
                ? 'flex min-h-screen bg-background text-text-primary'
                : 'flex min-h-screen w-full flex-col bg-background text-text-primary'
        "
    >
        <slot />
    </div>
</template>
