<script setup lang="ts">
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
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Open Lobbies</h1>
        <div
            v-for="post in posts"
            :key="post.id"
            class="border p-4 mb-2 rounded"
        >
            <p class="text-sm text-gray-500">{{ post.game.name }}</p>
            <h2 class="font-semibold">{{ post.title }}</h2>
            <p class="text-sm">Hosted by {{ post.user.name }}</p>
        </div>
    </div>
</template>
