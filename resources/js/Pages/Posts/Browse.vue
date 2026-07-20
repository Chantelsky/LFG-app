<script setup lang="ts">
import { router, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Toast from '@/Components/Toast.vue';
import CreatePostModal from '@/Components/CreatePostModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LobbyCard from '@/Components/LobbyCard.vue';

interface Post {
    id: number;
    title: string;
    skill_rank: string | null;
    region: string | null;
    timezone: string | null;
    comm_preference: string;
    current_members: number;
    party_size: number;
    roles_needed: string[] | null;
    created_at: string;
    game: { name: string; cover_art: string | null; artwork_url: string | null };
    user: { name: string };
    user_id: number;
    party_members: { id: number; user: { id: number; name: string } }[];
}

const page = usePage<{
    auth: { user: { id: number } };
}>();

const props = defineProps<{
    posts: Post[];
}>();

const currentUserId = page.props.auth.user.id;

const showCreateModal = ref(false);

function requestToJoin(postId: number) {
    router.post(`/posts/${postId}/join-requests`);
}

const selectedGame = ref<string>('all');
const selectedComm = ref<string>('all');

const availableGames = computed(() => {
    const names = new Set(props.posts.map((p) => p.game.name));
    return Array.from(names);
});

const filteredPosts = computed(() => {
    return props.posts.filter((post) => {
        const gameMatch = selectedGame.value === 'all' || post.game.name === selectedGame.value;
        const commMatch =
            selectedComm.value === 'all' || post.comm_preference === selectedComm.value;
        return gameMatch && commMatch;
    });
});
</script>

<template>
    <AppLayout>
        <div class="bg-lfg-bg min-h-screen p-8">
            <div class="mx-auto max-w-6xl">
                <Toast type="error" />
                <Toast type="success" />

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-lfg-text mb-1 text-2xl font-medium tracking-wide uppercase">
                            Find a squad
                        </h1>
                        <p class="text-lfg-muted text-sm">
                            Browse open lobbies and find your next adventure.
                        </p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="from-lfg-pink to-lfg-purple flex cursor-pointer items-center gap-2 rounded-lg bg-gradient-to-r px-5 py-2.5 text-sm font-medium text-white shadow-[0_0_20px_-6px_rgba(255,46,136,0.5)] transition hover:shadow-[0_0_24px_-4px_rgba(255,46,136,0.7)]"
                    >
                        <span class="text-base">+</span> Create Lobby
                    </button>
                </div>

                <div class="flex max-w-6xl flex-col gap-4">
                    <LobbyCard
                        v-for="post in filteredPosts"
                        :key="post.id"
                        :post="post"
                        :current-user-id="currentUserId"
                        @request-to-join="requestToJoin"
                    />
                </div>

                <p v-if="posts.length === 0" class="text-lfg-muted py-12 text-center text-sm">
                    No open lobbies right now. Be the first to post one.
                </p>

                <CreatePostModal :open="showCreateModal" @close="showCreateModal = false" />
            </div>
        </div>
    </AppLayout>
</template>
