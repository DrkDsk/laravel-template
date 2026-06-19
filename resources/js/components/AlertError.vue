<script setup lang="ts">
import AppAlert from '@/components/AppAlert.vue';

defineProps<{
    title?: string;
    message?: string;
}>();
</script>

<template>
    <AppAlert v-if="message" variant="danger" :title="title || 'Error'">
        {{ message }}
    </AppAlert>
</template>
