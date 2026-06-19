<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Reset password',
        description: 'Please enter your new password below',
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Reset password" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-6">
            <div>
                <AppInput
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    readonly
                    label="Email"
                    :error="errors.email"
                />
            </div>

            <div class="grid gap-2">
                <label
                    for="password"
                    class="text-sm font-medium text-text-primary"
                    >Password</label
                >
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    autofocus
                    placeholder="Password"
                    :passwordrules="passwordRules"
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
                    :passwordrules="passwordRules"
                    :error="errors.password_confirmation"
                />
            </div>

            <AppButton
                type="submit"
                class="mt-4 w-full"
                :loading="processing"
                data-test="reset-password-button"
            >
                Reset password
            </AppButton>
        </div>
    </Form>
</template>
