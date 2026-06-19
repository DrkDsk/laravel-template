<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AppAlert from '@/components/AppAlert.vue';
import AppButton from '@/components/AppButton.vue';
import AppCard from '@/components/AppCard.vue';
import AppInput from '@/components/AppInput.vue';
import DeleteUser from '@/components/DeleteUser.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <AppCard class="p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-on-surface">Profile</h2>
            <p class="mt-1 text-sm text-text-secondary">
                Update your name and email address.
            </p>
        </div>
        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div>
                <AppInput
                    id="name"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                    label="Name"
                    :error="errors.name"
                />
            </div>

            <div>
                <AppInput
                    id="email"
                    type="email"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                    label="Email address"
                    :error="errors.email"
                />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-text-secondary">
                    Your email address is unverified.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-on-surface underline underline-offset-4 transition hover:text-on-surface/80"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <AppAlert
                    v-if="page.props.status === 'verification-link-sent'"
                    variant="success"
                    class="mt-3"
                >
                    A new verification link has been sent to your email address.
                </AppAlert>
            </div>

            <div class="flex items-center gap-4">
                <AppButton
                    :loading="processing"
                    data-test="update-profile-button"
                >
                    Save changes
                </AppButton>
            </div>
        </Form>
    </AppCard>

    <DeleteUser />
</template>
