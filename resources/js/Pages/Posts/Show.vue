<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import { IconUsers, IconStar, IconWorld, IconHeadphones } from '@tabler/icons-vue';

interface User {
    id: number;
    name: string;
}

interface PartyMember {
    id: number;
    is_host: boolean;
    user: User;
}

interface JoinRequestItem {
    id: number;
    status: string;
    user: User;
}

interface Post {
    id: number;
    title: string;
    skill_rank: string | null;
    region: string | null;
    timezone: string | null;
    availability: string | null;
    comm_preference: string;
    status: string;
    current_members: number;
    party_size: number;
    game: { name: string };
    user: User;
    party_members: PartyMember[];
    join_requests: JoinRequestItem[];
}

const props = defineProps<{
    post: Post;
}>();

const page = usePage<{
    auth: { user: { id: number } };
}>();

const currentUserId = page.props.auth.user.id;
const isHost = props.post.user.id === currentUserId;
const isMember = props.post.party_members.some((m) => m.user.id === currentUserId);
const pendingRequests = computed(() =>
    props.post.join_requests.filter((r) => r.status === 'pending')
);

function requestToJoin() {
    router.post(`/posts/${props.post.id}/join-requests`);
}

function leaveParty() {
    router.delete(`/posts/${props.post.id}/leave`);
}

function removeMember(partyMemberId: number) {
    router.delete(`/posts/${props.post.id}/members/${partyMemberId}`);
}

function acceptRequest(requestId: number) {
    router.patch(`/join-requests/${requestId}/accept`);
}

function declineRequest(requestId: number) {
    router.patch(`/join-requests/${requestId}/decline`);
}
</script>

<template>
    <AppLayout>
        <div class="flex h-[calc(100vh-2rem)] gap-4 p-4">
            <Toast type="error" />
            <Toast type="success" />

            <div class="flex w-72 shrink-0 flex-col gap-4 overflow-y-auto">
                <div class="border-lfg-border bg-lfg-surface rounded-md border p-4">
                    <p class="text-lfg-pink mb-1 text-xs font-medium tracking-wide uppercase">
                        {{ post.game.name }}
                    </p>
                    <h1 class="text-lfg-text mb-2 text-lg font-medium">{{ post.title }}</h1>
                    <div class="text-lfg-muted space-y-1.5 text-xs">
                        <div class="flex items-center gap-2">
                            <IconUsers :size="14" />
                            {{ post.current_members }}/{{ post.party_size }} players
                        </div>
                        <div v-if="post.skill_rank" class="flex items-center gap-2">
                            <IconStar :size="14" />
                            {{ post.skill_rank }}
                        </div>
                        <div v-if="post.region || post.timezone" class="flex items-center gap-2">
                            <IconWorld :size="14" />
                            {{ [post.region, post.timezone].filter(Boolean).join(' · ') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <IconHeadphones :size="14" />
                            {{ post.comm_preference.replace('_', ' ') }}
                        </div>
                    </div>
                </div>

                <div class="border-lfg-border bg-lfg-surface rounded-md border p-4">
                    <h2 class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Party
                    </h2>
                    <div
                        v-for="member in post.party_members"
                        :key="member.id"
                        class="border-lfg-border flex items-center justify-between border-t py-2.5 first:border-t-0 first:pt-0"
                    >
                        <div class="flex items-center gap-2">
                            <div class="bg-lfg-pink h-2 w-2 rounded-full"></div>
                            <span class="text-lfg-text text-sm">{{ member.user.name }}</span>
                        </div>
                        <span
                            v-if="member.is_host"
                            class="border-lfg-pink text-lfg-pink rounded border px-1.5 py-0.5 text-[10px] font-medium"
                        >
                            HOST
                        </span>
                        <button
                            v-else-if="isHost"
                            @click="removeMember(member.id)"
                            class="text-lfg-orange cursor-pointer text-xs hover:underline"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <div
                    v-if="isHost && pendingRequests.length"
                    class="border-lfg-border bg-lfg-surface rounded-md border p-4"
                >
                    <h2 class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Pending Requests
                    </h2>
                    <div
                        v-for="request in pendingRequests"
                        :key="request.id"
                        class="border-lfg-border border-t py-2.5 first:border-t-0 first:pt-0"
                    >
                        <p class="text-lfg-text mb-2 text-sm">{{ request.user.name }}</p>
                        <div class="flex gap-2">
                            <button
                                @click="acceptRequest(request.id)"
                                class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 flex-1 cursor-pointer rounded border py-1 text-xs font-medium"
                            >
                                Accept
                            </button>
                            <button
                                @click="declineRequest(request.id)"
                                class="border-lfg-orange text-lfg-orange hover:bg-lfg-orange/10 flex-1 cursor-pointer rounded border py-1 text-xs font-medium"
                            >
                                Decline
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    v-if="isMember && !isHost"
                    @click="leaveParty"
                    class="border-lfg-orange text-lfg-orange hover:bg-lfg-orange/10 w-full cursor-pointer rounded-md border py-2.5 text-xs font-medium tracking-wide uppercase"
                >
                    Leave Party
                </button>

                <button
                    v-else-if="!isMember && post.status === 'open'"
                    @click="requestToJoin"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full cursor-pointer rounded-md border py-2.5 text-xs font-medium tracking-wide uppercase"
                >
                    Request to Join
                </button>
            </div>

            <div class="border-lfg-border bg-lfg-surface flex flex-1 flex-col rounded-md border">
                <div class="border-lfg-border border-b p-4">
                    <p class="text-lfg-muted text-xs font-medium tracking-wide uppercase">
                        Party Chat
                    </p>
                </div>

                <div class="flex flex-1 items-center justify-center">
                    <div class="text-center">
                        <p class="text-lfg-muted mb-1 text-sm">Chat coming soon</p>
                        <p class="text-lfg-muted/60 text-xs">
                            Real-time messaging between party members
                        </p>
                    </div>
                </div>

                <div class="border-lfg-border border-t p-4">
                    <input
                        type="text"
                        disabled
                        placeholder="Message the party..."
                        class="border-lfg-border bg-lfg-bg text-lfg-muted placeholder-lfg-muted/60 w-full cursor-not-allowed rounded-md border p-2.5 text-sm"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
