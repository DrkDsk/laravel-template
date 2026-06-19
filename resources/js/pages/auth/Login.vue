<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AppAlert from '@/components/AppAlert.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <AppAlert v-if="status" variant="success" class="mb-5">
        {{ status }}
    </AppAlert>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div>
                <AppInput
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                    label="Email address"
                    :error="errors.email"
                />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <label
                        for="password"
                        class="text-sm font-medium text-text-primary"
                        >Password</label
                    >
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    :error="errors.password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                />
            </div>

            <div class="flex items-center justify-between">
                <label
                    for="remember"
                    class="flex items-center gap-3 text-sm font-medium text-text-secondary"
                >
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        :tabindex="3"
                        class="size-4 rounded border-border-default text-primary"
                    />
                    <span>Remember this device</span>
                </label>
            </div>

            <AppButton
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :loading="processing"
                data-test="login-button"
            >
                Log in
            </AppButton>
        </div>

        <div class="text-center text-sm text-text-secondary">
            Don't have an account?
            <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
        </div>
    </Form>
</template>
