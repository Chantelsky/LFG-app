<script setup lang="ts">
import Toast from '@/Components/Toast.vue';
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage<{
    flash: {
        error: string | null;
        success: string | null;
    };
}>();

interface Post {
    id: number;
    title: string;
    skill_rank: string | null;
    region: string | null;
    game: { name: string };
    user: { name: string };
}

defineProps<{
    posts: Post[];
}>();

function requestToJoin(postId: number) {
    router.post(
        `/posts/${postId}/join-requests`,
        {},
        {
            onError: (errors) => {
                alert(errors.message ?? 'Unable to request to join this lobby.');
            },
        }
    );
}
</script>

<template>
    <div class="bg-lfg-bg min-h-screen p-8">
        <Toast type="error" />
        <Toast type="success" />
        <h1 class="text-lfg-text mb-1 text-2xl font-medium tracking-wide uppercase">
            Open Lobbies
        </h1>
        <p class="text-lfg-muted mb-6 text-sm">Find your next squad.</p>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="post in posts"
                :key="post.id"
                class="bg-lfg-surface border-lfg-border rounded-md border p-4"
            >
                <p class="text-lfg-pink mb-2 text-xs font-medium tracking-wide uppercase">
                    {{ post.game.name }}
                </p>
                <h2 class="text-lfg-text mb-1 text-base font-medium">
                    {{ post.title }}
                </h2>
                <p class="text-lfg-muted mb-4 text-xs">
                    Hosted by {{ post.user.name }}
                    <span v-if="post.skill_rank"> · {{ post.skill_rank }}</span>
                    <span v-if="post.region"> · {{ post.region }}</span>
                </p>
                <button
                    @click="requestToJoin(post.id)"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full rounded border py-2 text-sm font-medium"
                >
                    Request to Join
                </button>
            </div>
        </div>

        <p v-if="posts.length === 0" class="text-lfg-muted py-12 text-center text-sm">
            No open lobbies right now. Be the first to post one.
        </p>
    </div>
</template>
