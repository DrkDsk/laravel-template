<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AppAlert from '@/components/AppAlert.vue';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppModal from '@/components/AppModal.vue';
import PasswordInput from '@/components/PasswordInput.vue';

const passwordInput = useTemplateRef('passwordInput');
const showModal = ref(false);
</script>

<template>
    <AppCard class="p-6">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-on-surface">
                Delete account
            </h2>
            <p class="mt-1 text-sm text-text-secondary">
                Delete your account and all of its resources.
            </p>
        </div>

        <AppAlert variant="danger" title="Warning" class="mb-5">
            Please proceed with caution, this cannot be undone.
        </AppAlert>

        <AppButton
            variant="danger"
            data-test="delete-user-button"
            @click="showModal = true"
        >
            Delete account
        </AppButton>

        <AppModal
            v-model:open="showModal"
            title="Are you sure you want to delete your account?"
            description="Once your account is deleted, all resources and data will be permanently deleted."
        >
            <Form
                v-bind="ProfileController.destroy.form()"
                reset-on-success
                @error="() => passwordInput?.focus()"
                :options="{
                    preserveScroll: true,
                }"
                class="space-y-6"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <div class="grid gap-2">
                    <label
                        for="password"
                        class="text-sm font-medium text-text-primary"
                        >Password</label
                    >
                    <PasswordInput
                        id="password"
                        name="password"
                        ref="passwordInput"
                        placeholder="Password"
                        :error="errors.password"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <AppButton
                        variant="ghost"
                        @click="
                            () => {
                                clearErrors();
                                reset();
                                showModal = false;
                            }
                        "
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        variant="danger"
                        :loading="processing"
                        data-test="confirm-delete-user-button"
                    >
                        Delete account
                    </AppButton>
                </div>
            </Form>
        </AppModal>
    </AppCard>
</template>
