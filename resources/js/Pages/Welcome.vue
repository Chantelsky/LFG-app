<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="LFG — Find your next squad" />

    <div class="bg-lfg-bg text-lfg-text min-h-screen">
        <header class="flex items-center justify-between px-8 py-6">
            <span class="text-xl font-medium tracking-wide uppercase">
                <span class="text-lfg-pink">LFG</span>
            </span>

            <nav v-if="canLogin" class="flex items-center gap-3">
                <Link
                    v-if="$page.props.auth.user"
                    href="/browse"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 rounded-md border px-4 py-2 text-sm font-medium tracking-wide uppercase"
                >
                    Browse Lobbies
                </Link>
                <template v-else>
                    <Link
                        href="/login"
                        class="text-lfg-muted hover:text-lfg-text rounded-md px-4 py-2 text-sm"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 rounded-md border px-4 py-2 text-sm font-medium tracking-wide uppercase"
                    >
                        Sign up
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex flex-col items-center px-6 pt-20 pb-32 text-center">
            <h1 class="max-w-2xl text-4xl font-medium tracking-wide uppercase sm:text-5xl">
                Find your next <span class="text-lfg-pink">squad</span>
            </h1>
            <p class="text-lfg-muted mt-4 max-w-lg">
                Post your game, rank, region, and comms preference. Match with people looking for
                the exact same thing, instead of hoping the right person sees your message in a dead
                Discord channel.
            </p>

            <div class="mt-8 flex gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    href="/browse"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 rounded-md border px-6 py-3 text-sm font-medium tracking-wide uppercase"
                >
                    Browse Open Lobbies
                </Link>
                <template v-else>
                    <Link
                        href="/register"
                        class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 rounded-md border px-6 py-3 text-sm font-medium tracking-wide uppercase"
                    >
                        Get Started
                    </Link>
                    <Link
                        href="/login"
                        class="border-lfg-border text-lfg-muted hover:text-lfg-text rounded-md border px-6 py-3 text-sm font-medium tracking-wide uppercase"
                    >
                        Log in
                    </Link>
                </template>
            </div>
        </main>

        <section class="grid grid-cols-1 gap-6 px-6 pb-24 sm:grid-cols-3 sm:px-16">
            <div class="border-lfg-border bg-lfg-surface rounded-md border p-6">
                <p class="text-lfg-pink mb-2 text-xs font-medium tracking-wide uppercase">Post</p>
                <p class="text-lfg-muted text-sm">
                    Say what game, rank, region, and comm style you're after. Any game — search
                    thousands via IGDB, or add your own.
                </p>
            </div>
            <div class="border-lfg-border bg-lfg-surface rounded-md border p-6">
                <p class="text-lfg-cyan mb-2 text-xs font-medium tracking-wide uppercase">Match</p>
                <p class="text-lfg-muted text-sm">
                    Browse open lobbies or get requests on your own. Accept who fits, decline who
                    doesn't.
                </p>
            </div>
            <div class="border-lfg-border bg-lfg-surface rounded-md border p-6">
                <p class="text-lfg-text mb-2 text-xs font-medium tracking-wide uppercase">Play</p>
                <p class="text-lfg-muted text-sm">
                    Squad fills automatically. Add your regulars for next time, so you're never
                    starting from scratch.
                </p>
            </div>
        </section>

        <footer class="border-lfg-border border-t px-8 py-12">
            <div class="mx-auto grid max-w-5xl grid-cols-2 gap-8 sm:grid-cols-4">
                <div>
                    <span class="text-lg font-medium tracking-wide uppercase">
                        <span class="text-lfg-pink">LFG</span>
                    </span>
                    <p class="text-lfg-muted mt-2 text-xs">
                        Find your next squad, any game, any time.
                    </p>
                </div>

                <div>
                    <p class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Product
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link href="/browse" class="text-lfg-muted hover:text-lfg-text"
                                >Browse Lobbies</Link
                            >
                        </li>
                        <li>
                            <Link href="/register" class="text-lfg-muted hover:text-lfg-text"
                                >Get Started</Link
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Account
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link href="/login" class="text-lfg-muted hover:text-lfg-text"
                                >Log in</Link
                            >
                        </li>
                        <li>
                            <Link href="/register" class="text-lfg-muted hover:text-lfg-text"
                                >Sign up</Link
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Legal
                    </p>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <span class="text-lfg-muted cursor-not-allowed opacity-50"
                                >Terms of Service</span
                            >
                        </li>
                        <li>
                            <span class="text-lfg-muted cursor-not-allowed opacity-50"
                                >Privacy Policy</span
                            >
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-lfg-border text-lfg-muted mx-auto mt-10 max-w-5xl border-t pt-6 text-center text-xs"
            >
                © {{ new Date().getFullYear() }} LFG. All rights reserved.
            </div>
        </footer>
    </div>
</template>
