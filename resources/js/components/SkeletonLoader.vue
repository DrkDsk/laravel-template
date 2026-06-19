<script setup lang="ts">
withDefaults(
    defineProps<{
        variant?: 'cards' | 'tables' | 'forms';
        rows?: number;
    }>(),
    {
        variant: 'cards',
        rows: 3,
    },
);
</script>

<template>
    <div v-if="variant === 'cards'" class="grid gap-4 md:grid-cols-3">
        <div
            v-for="item in rows"
            :key="item"
            class="h-32 animate-pulse rounded-lg bg-border-muted"
        />
    </div>
    <div v-else-if="variant === 'tables'" class="space-y-3">
        <div class="h-10 animate-pulse rounded-md bg-border-muted" />
        <div
            v-for="item in rows"
            :key="item"
            class="h-12 animate-pulse rounded-md bg-border-muted"
        />
    </div>
    <div v-else class="space-y-4">
        <div v-for="item in rows" :key="item" class="space-y-2">
            <div class="h-4 w-32 animate-pulse rounded bg-border-muted" />
            <div class="h-11 animate-pulse rounded-md bg-border-muted" />
        </div>
    </div>
</template>
