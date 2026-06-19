<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppCard from '@/components/AppCard.vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Appearance settings" />

    <h1 class="sr-only">Appearance settings</h1>

    <AppCard class="p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-on-surface">
                Appearance settings
            </h2>
            <p class="mt-1 text-sm text-text-secondary">
                Update the appearance settings for your account.
            </p>
        </div>
        <AppearanceTabs />
    </AppCard>
</template>
