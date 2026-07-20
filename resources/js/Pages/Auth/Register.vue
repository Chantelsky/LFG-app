<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6">
            <h1 class="text-lfg-text text-xl font-medium tracking-wide uppercase">New here?</h1>
            <p class="text-lfg-muted mt-1 text-sm">
                Create an account to start finding your squad.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" class="text-lfg-muted" />
                <TextInput
                    id="name"
                    type="text"
                    class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan mt-1 block w-full rounded-md"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" class="text-lfg-muted" />
                <TextInput
                    id="email"
                    type="email"
                    class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan mt-1 block w-full rounded-md"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-lfg-muted"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan mt-1 block w-full rounded-md"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-6 space-y-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full cursor-pointer rounded-md border py-2.5 text-sm font-medium tracking-wide uppercase transition disabled:opacity-50"
                >
                    Create Account
                </button>

                <Link
                    :href="route('login')"
                    class="text-lfg-muted hover:text-lfg-text block text-center text-sm underline"
                >
                    Already registered?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
