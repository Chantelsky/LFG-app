<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface JoinRequestUser {
    id: number;
    name: string;
}

interface JoinRequest {
    id: number;
    status: string;
    user: JoinRequestUser;
}

interface Post {
    id: number;
    title: string;
    status: string;
    current_members: number;
    party_size: number;
    game: { name: string };
    join_requests: JoinRequest[];
}

defineProps<{
    posts: Post[];
}>();

function accept(requestId: number) {
    router.patch(`/join-requests/${requestId}/accept`);
}

function decline(requestId: number) {
    router.patch(`/join-requests/${requestId}/decline`);
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <h1 class="mb-4 text-2xl font-bold">My Posts</h1>

            <div v-for="post in posts" :key="post.id" class="mb-4 rounded border p-4">
                <p class="text-sm text-gray-500">{{ post.game.name }}</p>
                <h2 class="font-semibold">{{ post.title }}</h2>
                <p class="text-sm">
                    {{ post.current_members }}/{{ post.party_size }} ·
                    {{ post.status }}
                </p>

                <div
                    v-if="post.join_requests.filter((r) => r.status === 'pending').length"
                    class="mt-3"
                >
                    <p class="mb-1 text-sm font-semibold">Pending requests:</p>
                    <div
                        v-for="request in post.join_requests.filter((r) => r.status === 'pending')"
                        :key="request.id"
                        class="flex items-center justify-between border-t pt-2"
                    >
                        <span>{{ request.user.name }}</span>
                        <div class="space-x-2">
                            <button @click="accept(request.id)" class="text-sm text-green-600">
                                Accept
                            </button>
                            <button @click="decline(request.id)" class="text-sm text-red-600">
                                Decline
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
