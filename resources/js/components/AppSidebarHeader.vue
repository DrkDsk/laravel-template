<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Search } from '@lucide/vue';
import { computed } from 'vue';
import AppButton from '@/components/AppButton.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <header
        class="sticky top-0 z-20 border-b border-border-muted bg-surface/95 px-4 py-4 backdrop-blur md:px-8"
    >
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <template v-if="breadcrumbs && breadcrumbs.length > 0">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </template>
                <p class="mt-1 text-sm text-text-secondary">
                    Secure pension intelligence for {{ user.name }}
                </p>
            </div>

            <div
                class="hidden min-w-72 items-center gap-2 rounded-md border border-border-default bg-background px-3 py-2 text-sm text-text-secondary md:flex"
            >
                <Search class="size-4" />
                <span>Search calculations, reports...</span>
            </div>

            <AppButton
                variant="ghost"
                size="sm"
                class="lg:hidden"
                aria-label="Open navigation"
            >
                <Menu class="size-5" />
            </AppButton>
        </div>

        <nav class="mt-4 flex gap-2 lg:hidden" aria-label="Mobile navigation">
            <Link
                :href="dashboard()"
                class="rounded-md bg-primary px-3 py-2 text-sm font-medium text-on-primary"
            >
                Dashboard
            </Link>
        </nav>
    </header>
</template>
