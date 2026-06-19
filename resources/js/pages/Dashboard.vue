<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Calculator,
    CircleDollarSign,
    Landmark,
    TrendingUp,
} from '@lucide/vue';
import AppBadge from '@/components/AppBadge.vue';
import AppCard from '@/components/AppCard.vue';
import AppTable from '@/components/AppTable.vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const kpis = [
    {
        label: 'Projected monthly pension',
        value: '$42,850',
        delta: '+8.4%',
        icon: CircleDollarSign,
    },
    {
        label: 'Replacement rate',
        value: '71%',
        delta: '+3.1%',
        icon: TrendingUp,
    },
    {
        label: 'Contribution runway',
        value: '18 yrs',
        delta: 'On track',
        icon: Landmark,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-8 p-4 md:p-8">
        <section class="grid gap-4 lg:grid-cols-[1.3fr_0.7fr]">
            <AppCard variant="elevated" class="overflow-hidden">
                <div class="financial-gradient p-7 text-on-primary md:p-8">
                    <div
                        class="flex flex-wrap items-start justify-between gap-4"
                    >
                        <div>
                            <AppBadge
                                variant="info"
                                class="border-on-primary/20 bg-on-primary/12 text-on-primary"
                            >
                                Premium projection
                            </AppBadge>
                            <h1
                                class="mt-5 max-w-2xl text-3xl leading-tight font-semibold text-on-primary"
                            >
                                Retirement planning dashboard
                            </h1>
                            <p
                                class="mt-3 max-w-xl text-sm leading-6 text-on-primary/80"
                            >
                                Monitor pension readiness, contribution
                                velocity, and risk posture from one secure
                                workspace.
                            </p>
                        </div>
                        <div
                            class="rounded-lg border border-on-primary/15 bg-on-primary/10 p-4 text-right"
                        >
                            <p class="text-xs text-on-primary/75">
                                Confidence score
                            </p>
                            <p
                                class="mt-1 text-3xl font-semibold text-on-primary"
                            >
                                86
                            </p>
                        </div>
                    </div>
                </div>
            </AppCard>

            <AppCard class="p-6">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-secondary/10 text-secondary"
                    >
                        <Calculator class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-on-surface">
                            Next calculation
                        </p>
                        <p class="text-sm text-text-secondary">
                            Ready to simulate updated inputs
                        </p>
                    </div>
                </div>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary"
                            >Data completeness</span
                        >
                        <span class="font-semibold text-text-primary">92%</span>
                    </div>
                    <div class="h-2 rounded-full bg-border-muted">
                        <div class="h-2 w-[92%] rounded-full bg-success" />
                    </div>
                </div>
            </AppCard>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <AppCard v-for="item in kpis" :key="item.label" class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-text-secondary">
                            {{ item.label }}
                        </p>
                        <p class="mt-3 text-2xl font-semibold text-on-surface">
                            {{ item.value }}
                        </p>
                    </div>
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-border-muted text-primary"
                    >
                        <component :is="item.icon" class="size-5" />
                    </div>
                </div>
                <div class="mt-5 flex items-center gap-2 text-sm">
                    <ArrowUpRight class="size-4 text-success" />
                    <span class="font-medium text-success">{{
                        item.delta
                    }}</span>
                    <span class="text-text-secondary">vs. baseline</span>
                </div>
            </AppCard>
        </section>

        <section class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
            <AppCard class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-on-surface">
                            Funding curve
                        </h2>
                        <p class="text-sm text-text-secondary">
                            Illustrative readiness trend
                        </p>
                    </div>
                    <AppBadge variant="success">Healthy</AppBadge>
                </div>
                <div
                    class="flex h-64 items-end gap-3 rounded-lg bg-background p-4"
                >
                    <div
                        v-for="height in [34, 42, 48, 58, 63, 72, 80, 88]"
                        :key="height"
                        class="flex-1 rounded-t-md bg-secondary/80"
                        :style="{ height: `${height}%` }"
                    />
                </div>
            </AppCard>

            <div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">
                        Recent pension scenarios
                    </h2>
                    <p class="text-sm text-text-secondary">
                        Saved records will appear in this workspace.
                    </p>
                </div>
                <AppTable
                    empty
                    empty-title="No scenarios yet"
                    empty-description="Create a pension calculation to compare projections here."
                />
            </div>
        </section>
    </div>
</template>
