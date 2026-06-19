<script setup lang="ts">
import { Toaster } from 'vue-sonner';
</script>

<template>
    <Toaster
        rich-colors
        close-button
        position="top-right"
        :toast-options="{
            classes: {
                toast: 'border-border-default bg-surface text-on-surface app-shadow-elevated',
                description: 'text-text-secondary',
                actionButton: 'bg-primary text-on-primary',
                cancelButton: 'bg-border-muted text-text-primary',
            },
        }"
    />
</template>
