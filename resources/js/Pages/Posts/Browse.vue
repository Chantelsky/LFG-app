<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Toast from '@/Components/Toast.vue';
import CreatePostModal from '@/Components/CreatePostModal.vue';

interface Game {
    id: number;
    name: string;
}

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
    games: Game[];
}>();

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
                class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 rounded-md border px-4 py-2 text-sm font-medium tracking-wide uppercase"
            >
                + Post a Lobby
            </button>
        </div>

        <!-- existing posts grid stays as-is -->

        <CreatePostModal :open="showCreateModal" :games="games" @close="showCreateModal = false" />
    </div>
</template>
