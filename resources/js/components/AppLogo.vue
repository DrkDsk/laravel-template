<script setup lang="ts">
import { Landmark } from '@lucide/vue';
</script>

<template>
    <div
        class="flex size-10 items-center justify-center rounded-lg bg-on-primary/10 text-on-primary"
    >
        <Landmark class="size-5" />
    </div>
    <div class="ml-2 grid flex-1 text-left">
        <span
            class="truncate text-sm leading-tight font-semibold text-on-primary"
        >
            PensionFlow
        </span>
        <span class="truncate text-xs text-on-primary/75">
            Financial planning
        </span>
    </div>
</template>
