<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Check, Copy, ScanLine } from '@lucide/vue';
import { useClipboard } from '@vueuse/core';
import { computed, nextTick, ref, useTemplateRef, watch } from 'vue';
import AlertError from '@/components/AlertError.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import AppModal from '@/components/AppModal.vue';
import InputError from '@/components/InputError.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm } from '@/routes/two-factor';
import type { TwoFactorConfigContent } from '@/types';

type Props = {
    requiresConfirmation: boolean;
    twoFactorEnabled: boolean;
};

const { resolvedAppearance } = useAppearance();

const props = defineProps<Props>();
const isOpen = defineModel<boolean>('isOpen');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
    useTwoFactorAuth();

const showVerificationStep = ref(false);
const code = ref<string>('');

const pinInputContainerRef = useTemplateRef('pinInputContainerRef');

const modalConfig = computed<TwoFactorConfigContent>(() => {
    if (props.twoFactorEnabled) {
        return {
            title: 'Two-factor authentication enabled',
            description:
                'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            buttonText: 'Close',
        };
    }

    if (showVerificationStep.value) {
        return {
            title: 'Verify authentication code',
            description: 'Enter the 6-digit code from your authenticator app',
            buttonText: 'Continue',
        };
    }

    return {
        title: 'Enable two-factor authentication',
        description:
            'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
        buttonText: 'Continue',
    };
});

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        nextTick(() => {
            pinInputContainerRef.value?.querySelector('input')?.focus();
        });

        return;
    }

    clearSetupData();
    isOpen.value = false;
};

const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = '';
};

watch(
    () => isOpen.value,
    async (isOpen) => {
        if (!isOpen) {
            resetModalState();

            return;
        }

        if (!qrCodeSvg.value) {
            await fetchSetupData();
        }
    },
);
</script>

<template>
    <AppModal
        v-model:open="isOpen"
        :title="modalConfig.title"
        :description="modalConfig.description"
    >
        <div class="mb-5 flex items-center justify-center">
            <div
                class="app-shadow-card w-auto rounded-full border border-border-default bg-surface p-0.5"
            >
                <div
                    class="relative overflow-hidden rounded-full border border-border-muted bg-background p-2.5"
                >
                    <div class="absolute inset-0 grid grid-cols-5 opacity-50">
                        <div
                            v-for="i in 5"
                            :key="`col-${i}`"
                            class="border-r border-border-muted last:border-r-0"
                        />
                    </div>
                    <div class="absolute inset-0 grid grid-rows-5 opacity-50">
                        <div
                            v-for="i in 5"
                            :key="`row-${i}`"
                            class="border-b border-border-muted last:border-b-0"
                        />
                    </div>
                    <ScanLine class="relative z-20 size-6 text-primary" />
                </div>
            </div>
        </div>

        <div
            class="relative flex w-auto flex-col items-center justify-center space-y-5"
        >
            <template v-if="!showVerificationStep">
                <AlertError
                    v-if="errors?.length"
                    :message="errors.join(', ')"
                />
                <template v-else>
                    <div
                        class="relative mx-auto flex max-w-md items-center overflow-hidden"
                    >
                        <div
                            class="relative mx-auto aspect-square w-64 overflow-hidden rounded-lg border border-border-default"
                        >
                            <div
                                v-if="!qrCodeSvg"
                                class="absolute inset-0 z-10 flex aspect-square h-auto w-full animate-pulse items-center justify-center bg-background text-text-secondary"
                            >
                                Loading...
                            </div>
                            <div
                                v-else
                                class="relative z-10 overflow-hidden border border-border-muted p-5"
                            >
                                <div
                                    v-html="qrCodeSvg"
                                    class="flex aspect-square size-full items-center justify-center"
                                    :style="{
                                        filter:
                                            resolvedAppearance === 'dark'
                                                ? 'invert(1) brightness(1.5)'
                                                : undefined,
                                    }"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex w-full items-center space-x-5">
                        <AppButton class="w-full" @click="handleModalNextStep">
                            {{ modalConfig.buttonText }}
                        </AppButton>
                    </div>

                    <div
                        class="relative flex w-full items-center justify-center"
                    >
                        <div
                            class="absolute inset-0 top-1/2 h-px w-full bg-border-muted"
                        />
                        <span
                            class="relative bg-surface px-2 py-1 text-sm text-text-secondary"
                            >or, enter the code manually</span
                        >
                    </div>

                    <div
                        class="flex w-full items-center justify-center space-x-2"
                    >
                        <div
                            class="flex w-full items-stretch overflow-hidden rounded-lg border border-border-default"
                        >
                            <div
                                v-if="!manualSetupKey"
                                class="flex h-full w-full items-center justify-center bg-background p-3 text-sm text-text-secondary"
                            >
                                Loading...
                            </div>
                            <template v-else>
                                <input
                                    type="text"
                                    readonly
                                    :value="manualSetupKey"
                                    class="h-full w-full bg-background p-3 text-text-primary"
                                />
                                <button
                                    @click="copy(manualSetupKey || '')"
                                    class="relative block h-auto border-l border-border-default px-3 text-text-primary hover:bg-border-muted"
                                >
                                    <Check
                                        v-if="copied"
                                        class="w-4 text-success"
                                    />
                                    <Copy v-else class="w-4" />
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </template>

            <template v-else>
                <Form
                    v-bind="confirm.form()"
                    error-bag="confirmTwoFactorAuthentication"
                    reset-on-error
                    @finish="code = ''"
                    @success="isOpen = false"
                    v-slot="{ errors, processing }"
                >
                    <input type="hidden" name="code" :value="code" />
                    <div
                        ref="pinInputContainerRef"
                        class="relative w-full space-y-3"
                    >
                        <div
                            class="flex w-full flex-col items-center justify-center space-y-3 py-2"
                        >
                            <AppInput
                                id="otp"
                                v-model="code"
                                maxlength="6"
                                inputmode="numeric"
                                :disabled="processing"
                                class="text-center text-xl font-semibold tracking-[0.45em]"
                                placeholder="000000"
                                autofocus
                            />
                            <InputError :message="errors?.code" />
                        </div>

                        <div class="flex w-full items-center space-x-5">
                            <AppButton
                                type="button"
                                variant="ghost"
                                class="w-auto flex-1"
                                @click="showVerificationStep = false"
                                :disabled="processing"
                            >
                                Back
                            </AppButton>
                            <AppButton
                                type="submit"
                                class="w-auto flex-1"
                                :loading="processing"
                                :disabled="code.length < 6"
                            >
                                Confirm
                            </AppButton>
                        </div>
                    </div>
                </Form>
            </template>
        </div>
    </AppModal>
</template>
