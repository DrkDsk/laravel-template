<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AppAlert from '@/components/AppAlert.vue';
import AppButton from '@/components/AppButton.vue';
import TextLink from '@/components/TextLink.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Email verification',
        description:
            'Please verify your email address by clicking on the link we just emailed to you.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Email verification" />

    <AppAlert
        v-if="status === 'verification-link-sent'"
        variant="success"
        class="mb-5"
    >
        A new verification link has been sent to the email address you provided
        during registration.
    </AppAlert>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <AppButton :loading="processing" variant="secondary">
            Resend verification email
        </AppButton>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            Log out
        </TextLink>
    </Form>
</template>
