<script setup lang="ts">
import Toast from "@/Components/Toast.vue";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

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
                alert(
                    errors.message ?? "Unable to request to join this lobby.",
                );
            },
        },
    );
}
</script>

<template>
    <div class="min-h-screen bg-lfg-bg p-8">
        <Toast type="error" />
        <Toast type="success" />
        <h1
            class="text-2xl font-medium tracking-wide uppercase text-lfg-text mb-1"
        >
            Open Lobbies
        </h1>
        <p class="text-sm text-lfg-muted mb-6">Find your next squad.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="post in posts"
                :key="post.id"
                class="bg-lfg-surface border border-lfg-border rounded-md p-4"
            >
                <p
                    class="text-xs font-medium uppercase tracking-wide text-lfg-pink mb-2"
                >
                    {{ post.game.name }}
                </p>
                <h2 class="text-base font-medium text-lfg-text mb-1">
                    {{ post.title }}
                </h2>
                <p class="text-xs text-lfg-muted mb-4">
                    Hosted by {{ post.user.name }}
                    <span v-if="post.skill_rank"> · {{ post.skill_rank }}</span>
                    <span v-if="post.region"> · {{ post.region }}</span>
                </p>
                <button
                    @click="requestToJoin(post.id)"
                    class="w-full border border-lfg-cyan text-lfg-cyan text-sm font-medium py-2 rounded hover:bg-lfg-cyan/10"
                >
                    Request to Join
                </button>
            </div>
        </div>

        <p
            v-if="posts.length === 0"
            class="text-sm text-lfg-muted text-center py-12"
        >
            No open lobbies right now. Be the first to post one.
        </p>
    </div>
</template>
