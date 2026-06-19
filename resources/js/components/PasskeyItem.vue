<script setup lang="ts">
import { KeyRound, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import AppBadge from '@/components/AppBadge.vue';
import AppButton from '@/components/AppButton.vue';
import AppModal from '@/components/AppModal.vue';
import type { Passkey } from '@/types/auth';

const props = defineProps<{
    passkey: Passkey;
}>();

const emit = defineEmits<{
    remove: [id: number, onError: () => void];
}>();

const isDeleting = ref(false);
const showModal = ref(false);

const handleDelete = () => {
    isDeleting.value = true;
    emit('remove', props.passkey.id, () => {
        isDeleting.value = false;
    });
};
</script>

<template>
    <div
        class="flex items-center justify-between gap-4 border-b border-border-muted p-4 last:border-b-0"
    >
        <div class="flex items-center gap-4">
            <div
                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-border-muted text-primary"
            >
                <KeyRound class="size-5" />
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <p class="font-medium tracking-tight text-on-surface">
                        {{ passkey.name }}
                    </p>
                    <AppBadge
                        v-if="passkey.authenticator"
                        variant="neutral"
                        class="uppercase"
                    >
                        {{ passkey.authenticator }}
                    </AppBadge>
                </div>
                <p class="text-sm text-text-secondary">
                    Added {{ passkey.created_at_diff }}
                    <template v-if="passkey.last_used_at_diff">
                        <span class="mx-1 text-text-secondary/50">/</span>
                        Last used {{ passkey.last_used_at_diff }}
                    </template>
                </p>
            </div>
        </div>

        <AppButton
            variant="ghost"
            size="sm"
            class="text-danger hover:bg-danger/10"
            @click="showModal = true"
        >
            <Trash2 class="size-4" />
            <span class="sr-only">Remove</span>
        </AppButton>

        <AppModal
            v-model:open="showModal"
            title="Remove passkey"
            :description="`Are you sure you want to remove the &quot;${passkey.name}&quot; passkey? You will no longer be able to use it to sign in.`"
        >
            <div class="flex justify-end gap-3">
                <AppButton variant="ghost" @click="showModal = false"
                    >Cancel</AppButton
                >
                <AppButton
                    variant="danger"
                    :loading="isDeleting"
                    @click="handleDelete"
                >
                    Remove passkey
                </AppButton>
            </div>
        </AppModal>
    </div>
</template>
