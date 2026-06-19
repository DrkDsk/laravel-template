<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Landmark, LockKeyhole, ShieldCheck, TrendingUp } from '@lucide/vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="grid min-h-screen bg-background text-text-primary lg:grid-cols-[1.05fr_0.95fr]"
    >
        <aside
            class="financial-gradient relative hidden overflow-hidden p-10 text-on-primary lg:flex lg:flex-col"
        >
            <Link :href="home()" class="flex items-center gap-3">
                <div
                    class="flex size-11 items-center justify-center rounded-lg bg-on-primary/12"
                >
                    <Landmark class="size-6" />
                </div>
                <div>
                    <p class="text-base font-semibold">PensionFlow</p>
                    <p class="text-sm text-on-primary/75">
                        Institutional retirement planning
                    </p>
                </div>
            </Link>

            <div class="mt-auto max-w-xl">
                <div
                    class="mb-8 inline-flex items-center gap-2 rounded-full border border-on-primary/15 bg-on-primary/10 px-4 py-2 text-sm text-on-primary/85"
                >
                    <ShieldCheck class="size-4" />
                    Bank-grade controls enabled
                </div>
                <h1
                    class="text-5xl leading-tight font-semibold text-on-primary"
                >
                    Pension decisions with portfolio-grade clarity.
                </h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-on-primary/80">
                    Model contributions, visualize projected outcomes, and keep
                    sensitive retirement data protected in one professional
                    workspace.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-3">
                    <div
                        class="rounded-lg border border-on-primary/10 bg-on-primary/10 p-4"
                    >
                        <TrendingUp class="mb-4 size-5 text-on-primary/80" />
                        <p class="text-2xl font-semibold">24/7</p>
                        <p class="mt-1 text-xs text-on-primary/75">
                            Scenario access
                        </p>
                    </div>
                    <div
                        class="rounded-lg border border-on-primary/10 bg-on-primary/10 p-4"
                    >
                        <LockKeyhole class="mb-4 size-5 text-on-primary/80" />
                        <p class="text-2xl font-semibold">2FA</p>
                        <p class="mt-1 text-xs text-on-primary/75">
                            Protected sign-in
                        </p>
                    </div>
                    <div
                        class="rounded-lg border border-on-primary/10 bg-on-primary/10 p-4"
                    >
                        <ShieldCheck class="mb-4 size-5 text-on-primary/80" />
                        <p class="text-2xl font-semibold">AA</p>
                        <p class="mt-1 text-xs text-on-primary/75">
                            Contrast system
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <main
            class="flex min-h-screen items-center justify-center px-5 py-10 md:px-10"
        >
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <Link :href="home()" class="flex items-center gap-3">
                        <div
                            class="flex size-11 items-center justify-center rounded-lg bg-primary text-on-primary"
                        >
                            <Landmark class="size-6" />
                        </div>
                        <div>
                            <p
                                class="text-base font-semibold text-text-primary"
                            >
                                PensionFlow
                            </p>
                            <p class="text-sm text-text-secondary">
                                Retirement planning
                            </p>
                        </div>
                    </Link>
                </div>

                <section
                    class="app-shadow-elevated rounded-lg border border-border-muted bg-surface p-6 text-on-surface md:p-8"
                >
                    <div class="mb-7">
                        <p class="text-sm font-medium text-on-surface/80">
                            Secure workspace
                        </p>
                        <h1 class="mt-2 text-2xl font-semibold text-on-surface">
                            {{ title }}
                        </h1>
                        <p class="mt-2 text-sm leading-6 text-text-secondary">
                            {{ description }}
                        </p>
                    </div>
                    <slot />
                </section>
            </div>
        </main>
    </div>
</template>
