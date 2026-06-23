<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { ref } from 'vue';
import { cn } from '@/lib/utils';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label?: string;
    helper?: string;
    error?: string;
    disabled?: boolean;
    required?: boolean;
    class?: HTMLAttributes['class'];
}>();

const model = defineModel<string | number>();
const inputRef = ref<HTMLInputElement | null>(null);

defineExpose({
    focus: () => inputRef.value?.focus(),
});

const inputClass = () =>
    cn(
        'h-11 w-full rounded-md border bg-surface px-3 text-sm text-on-surface transition placeholder:text-text-secondary disabled:cursor-not-allowed disabled:bg-border-muted disabled:text-text-secondary',
        props.error
            ? 'border-danger focus-visible:ring-danger'
            : 'border-border-default hover:border-secondary/50 focus-visible:ring-ring',
        props.class,
    );
</script>

<template>
    <label class="grid gap-2">
        <span v-if="props.label" class="ui-label text-sm font-medium">
            {{ props.label
            }}<span v-if="props.required" class="text-red-500"> *</span>
        </span>
        <input
            v-if="model !== undefined"
            ref="inputRef"
            v-bind="$attrs"
            v-model="model"
            :disabled="props.disabled"
            :required="props.required"
            :aria-invalid="Boolean(props.error)"
            :class="inputClass()"
        />
        <input
            v-else
            ref="inputRef"
            v-bind="$attrs"
            :disabled="props.disabled"
            :required="props.required"
            :aria-invalid="Boolean(props.error)"
            :class="inputClass()"
        />
        <span
            v-if="props.error"
            class="text-red-600 dark:text-red-300 text-xs"
        >
            {{ props.error }}
        </span>
        <span v-else-if="props.helper" class="text-sm text-text-secondary">
            {{ props.helper }}
        </span>
    </label>
</template>
