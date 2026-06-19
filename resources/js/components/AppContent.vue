<script setup lang="ts">
import { computed } from 'vue';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});
const className = computed(() => props.class);
</script>

<template>
    <main
        :class="[
            props.variant === 'sidebar'
                ? 'min-h-screen flex-1 bg-background'
                : 'mx-auto flex min-h-screen w-full max-w-7xl flex-1 flex-col bg-background',
            className,
        ]"
    >
        <slot />
    </main>
</template>
