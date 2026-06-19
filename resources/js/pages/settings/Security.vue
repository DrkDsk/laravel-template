<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import type { Props as ManagePasskeysProps } from '@/components/ManagePasskeys.vue';
import ManagePasskeys from '@/components/ManagePasskeys.vue';
import type { Props as ManageTwoFactorProps } from '@/components/ManageTwoFactor.vue';
import ManageTwoFactor from '@/components/ManageTwoFactor.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { edit } from '@/routes/security';

type Props = {
    passwordRules: string;
} & ManagePasskeysProps &
    ManageTwoFactorProps;

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Security settings" />

    <h1 class="sr-only">Security settings</h1>

    <AppCard class="p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-on-surface">
                Update password
            </h2>
            <p class="mt-1 text-sm text-text-secondary">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </div>
        <Form
            v-bind="SecurityController.update.form()"
            :options="{
                preserveScroll: true,
            }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <label
                    for="current_password"
                    class="text-sm font-medium text-text-primary"
                    >Current password</label
                >
                <PasswordInput
                    id="current_password"
                    name="current_password"
                    autocomplete="current-password"
                    placeholder="Current password"
                    :error="errors.current_password"
                />
            </div>

            <div class="grid gap-2">
                <label
                    for="password"
                    class="text-sm font-medium text-text-primary"
                    >New password</label
                >
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="New password"
                    :passwordrules="props.passwordRules"
                    :error="errors.password"
                />
            </div>

            <div class="grid gap-2">
                <label
                    for="password_confirmation"
                    class="text-sm font-medium text-text-primary"
                    >Confirm password</label
                >
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                    :passwordrules="props.passwordRules"
                    :error="errors.password_confirmation"
                />
            </div>

            <div class="flex items-center gap-4">
                <AppButton
                    :loading="processing"
                    data-test="update-password-button"
                >
                    Save password
                </AppButton>
            </div>
        </Form>
    </AppCard>

    <ManageTwoFactor
        :canManageTwoFactor="canManageTwoFactor"
        :requiresConfirmation="requiresConfirmation"
        :twoFactorEnabled="twoFactorEnabled"
    />

    <ManagePasskeys
        :canManagePasskeys="canManagePasskeys"
        :passkeys="passkeys"
    />
</template>
