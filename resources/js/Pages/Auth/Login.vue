<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6">
            <h1 class="text-lfg-text text-xl font-medium tracking-wide uppercase">Welcome back</h1>
            <p class="text-lfg-muted mt-1 text-sm">Log in to find your next squad.</p>
        </div>

        <div v-if="status" class="text-lfg-cyan mb-4 text-sm font-medium">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="text-lfg-muted" />
                <TextInput
                    id="email"
                    type="email"
                    class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan mt-1 block w-full rounded-md"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="text-lfg-muted" />
                <TextInput
                    id="password"
                    type="password"
                    class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan mt-1 block w-full rounded-md"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-lfg-muted ms-2 text-sm">Remember me</span>
                </label>
            </div>

            <div class="mt-6 space-y-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full cursor-pointer rounded-md border py-2.5 text-sm font-medium tracking-wide uppercase transition disabled:opacity-50"
                >
                    Log in
                </button>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-lfg-muted hover:text-lfg-text block text-center text-sm underline"
                >
                    Forgot your password?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
