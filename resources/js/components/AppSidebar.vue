<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, LineChart, ShieldCheck, WalletCards } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import SidebarNavItem from '@/components/SidebarNavItem.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <aside
        class="sticky top-0 hidden h-screen w-72 shrink-0 flex-col border-r border-on-primary/10 bg-sidebar-background px-4 py-5 text-on-primary lg:flex"
    >
        <Link
            :href="dashboard()"
            class="mb-8 flex items-center rounded-lg px-2 py-1"
        >
            <AppLogo />
        </Link>

        <div
            class="mb-4 px-3 text-xs font-semibold tracking-[0.18em] text-on-primary/70 uppercase"
        >
            Platform
        </div>
        <nav class="space-y-1" aria-label="Primary navigation">
            <SidebarNavItem
                v-for="item in mainNavItems"
                :key="item.title"
                :title="item.title"
                :href="item.href"
                :icon="item.icon"
                :active="isCurrentOrParentUrl(item.href)"
            />
        </nav>

        <div class="mt-auto border-t border-on-primary/10 pt-4">
            <NavUser />
        </div>
    </aside>
    <slot />
</template>
