<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage<{
    auth: { user: { id: number; name: string } };
}>();

const user = page.props.auth.user;

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="bg-lfg-bg flex min-h-screen">
        <aside class="border-lfg-border flex w-56 shrink-0 flex-col border-r">
            <div class="px-5 py-6">
                <Link
                    href="/browse"
                    class="text-lfg-text text-lg font-medium tracking-wide uppercase"
                >
                    <span class="text-lfg-pink">LFG</span>
                </Link>
            </div>

            <nav class="flex-1 space-y-1 px-3">
                <Link
                    href="/browse"
                    class="text-lfg-text hover:bg-lfg-surface block rounded-md px-3 py-2 text-sm font-medium"
                    :class="{ 'bg-lfg-surface text-lfg-cyan': $page.url === '/browse' }"
                >
                    Find a Squad
                </Link>
                <Link
                    href="/my-posts"
                    class="text-lfg-muted hover:bg-lfg-surface hover:text-lfg-text block rounded-md px-3 py-2 text-sm font-medium"
                    :class="{ 'bg-lfg-surface text-lfg-cyan': $page.url === '/my-posts' }"
                >
                    My Lobbies
                </Link>
                <Link
                    href="/profile"
                    class="text-lfg-muted hover:bg-lfg-surface hover:text-lfg-text block rounded-md px-3 py-2 text-sm font-medium"
                    :class="{ 'bg-lfg-surface text-lfg-cyan': $page.url === '/profile' }"
                >
                    Profile
                </Link>
            </nav>

            <div class="border-lfg-border border-t px-3 py-4">
                <div class="flex items-center gap-2 rounded-md px-2 py-2">
                    <div
                        class="bg-lfg-pink text-lfg-bg flex h-8 w-8 items-center justify-center rounded-full text-xs font-medium"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-lfg-text truncate text-sm font-medium">{{ user.name }}</p>
                    </div>
                    <button
                        @click="logout"
                        class="text-lfg-muted hover:text-lfg-orange cursor-pointer text-xs"
                    >
                        Log out
                    </button>
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>
