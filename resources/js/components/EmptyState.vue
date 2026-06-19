<script setup lang="ts">
import { Inbox } from '@lucide/vue';
import type { Component, HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    title: string;
    description?: string;
    icon?: Component;
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div
        :class="
            cn(
                'flex flex-col items-center justify-center rounded-lg border border-dashed border-border-default bg-background/70 p-8 text-center',
                props.class,
            )
        "
    >
        <component
            :is="props.icon || Inbox"
            class="mb-4 size-10 text-text-secondary"
        />
        <p class="font-semibold text-text-primary">{{ props.title }}</p>
        <p
            v-if="props.description"
            class="mt-1 max-w-sm text-sm text-text-secondary"
        >
            {{ props.description }}
        </p>
        <div v-if="$slots.default" class="mt-5">
            <slot />
        </div>
    </div>
</template>
