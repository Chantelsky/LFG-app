<script setup lang="ts">
import { router, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Toast from '@/Components/Toast.vue';
import CreatePostModal from '@/Components/CreatePostModal.vue';

interface Post {
    id: number;
    title: string;
    skill_rank: string | null;
    region: string | null;
    game: { name: string; cover_art: string | null; artwork_url: string | null };
    user: { name: string };
    user_id: number;
}

const page = usePage<{
    auth: { user: { id: number } };
}>();

defineProps<{
    posts: Post[];
}>();

const currentUserId = page.props.auth.user.id;

const showCreateModal = ref(false);

function requestToJoin(postId: number) {
    router.post(`/posts/${postId}/join-requests`);
}
</script>

<template>
    <div class="bg-lfg-bg min-h-screen p-8">
        <Toast type="error" />
        <Toast type="success" />

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-lfg-text mb-1 text-2xl font-medium tracking-wide uppercase">
                    Open Lobbies
                </h1>
                <p class="text-lfg-muted text-sm">Find your next squad.</p>
            </div>
            <button
                @click="showCreateModal = true"
                class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 cursor-pointer rounded-md border px-4 py-2 text-sm font-medium tracking-wide uppercase"
            >
                + Post a Lobby
            </button>
        </div>

        <div class="grid max-w-4xl grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="post in posts"
                :key="post.id"
                class="border-lfg-border bg-lfg-surface hover:border-lfg-cyan rounded-md border p-4 transition"
            >
                <div
                    class="border-lfg-border bg-lfg-bg mb-3 h-32 w-full overflow-hidden rounded-md border"
                >
                    <img
                        v-if="post.game.artwork_url || post.game.cover_art"
                        :src="post.game.artwork_url ?? post.game.cover_art ?? undefined"
                        :alt="post.game.name"
                        class="h-full w-full object-cover"
                    />
                </div>
                <p class="text-lfg-pink mb-2 text-xs font-medium tracking-wide uppercase">
                    {{ post.game.name }}
                </p>
                <Link
                    :href="`/posts/${post.id}`"
                    class="text-lfg-text hover:text-lfg-cyan mb-1 block text-base font-medium"
                >
                    {{ post.title }}
                </Link>
                <p class="text-lfg-muted mb-4 text-xs">
                    Hosted by {{ post.user.name }}
                    <span v-if="post.skill_rank"> · {{ post.skill_rank }}</span>
                    <span v-if="post.region"> · {{ post.region }}</span>
                </p>

                <Link
                    v-if="post.user_id === currentUserId"
                    :href="`/posts/${post.id}`"
                    class="border-lfg-pink text-lfg-pink hover:bg-lfg-pink/10 block w-full cursor-pointer rounded-md border py-2 text-center text-sm font-medium"
                >
                    Manage
                </Link>
                <button
                    v-else
                    @click="requestToJoin(post.id)"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full cursor-pointer rounded-md border py-2 text-sm font-medium"
                >
                    Request to Join
                </button>
            </div>
        </div>

        <p v-if="posts.length === 0" class="text-lfg-muted py-12 text-center text-sm">
            No open lobbies right now. Be the first to post one.
        </p>

        <CreatePostModal :open="showCreateModal" @close="showCreateModal = false" />
    </div>
</template>
