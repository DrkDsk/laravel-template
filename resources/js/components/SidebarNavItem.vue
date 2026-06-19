<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { Component } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: Component;
    active?: boolean;
}>();

const emit = defineEmits<{
    navigate: [];
}>();
</script>

<template>
    <Link
        :href="props.href"
        :class="
            cn(
                'flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition',
                props.active
                    ? 'bg-sidebar-active text-on-primary'
                    : 'text-on-primary/75 hover:bg-on-primary/10 hover:text-on-primary',
            )
        "
        @click="emit('navigate')"
    >
        <component v-if="props.icon" :is="props.icon" class="size-4" />
        <span>{{ props.title }}</span>
    </Link>
</template>
