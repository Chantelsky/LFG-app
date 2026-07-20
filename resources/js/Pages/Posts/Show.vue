<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

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
const pendingRequests = props.post.join_requests.filter((r) => r.status === 'pending');

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
        <div class="bg-lfg-bg min-h-screen p-8">
            <Toast type="error" />
            <Toast type="success" />

            <div class="mx-auto max-w-2xl">
                <p class="text-lfg-pink mb-1 text-xs font-medium tracking-wide uppercase">
                    {{ post.game.name }}
                </p>
                <h1 class="text-lfg-text mb-1 text-2xl font-medium">{{ post.title }}</h1>
                <p class="text-lfg-muted mb-6 text-sm">
                    Hosted by {{ post.user.name }}
                    <span v-if="post.skill_rank"> · {{ post.skill_rank }}</span>
                    <span v-if="post.region"> · {{ post.region }}</span>
                    <span v-if="post.timezone"> · {{ post.timezone }}</span>
                </p>

                <div class="border-lfg-border bg-lfg-surface mb-6 rounded-md border p-4">
                    <h2 class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Party ({{ post.current_members }}/{{ post.party_size }})
                    </h2>
                    <div
                        v-for="member in post.party_members"
                        :key="member.id"
                        class="border-lfg-border flex items-center justify-between border-t py-2 first:border-t-0"
                    >
                        <span class="text-lfg-text text-sm">
                            {{ member.user.name }}
                            <span v-if="member.is_host" class="text-lfg-pink ml-1 text-xs"
                                >HOST</span
                            >
                        </span>
                        <button
                            v-if="isHost && !member.is_host"
                            @click="removeMember(member.id)"
                            class="text-lfg-orange text-xs hover:underline"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <div
                    v-if="isHost && pendingRequests.length"
                    class="border-lfg-border bg-lfg-surface mb-6 rounded-md border p-4"
                >
                    <h2 class="text-lfg-muted mb-3 text-xs font-medium tracking-wide uppercase">
                        Pending Requests
                    </h2>
                    <div
                        v-for="request in pendingRequests"
                        :key="request.id"
                        class="border-lfg-border flex items-center justify-between border-t py-2 first:border-t-0"
                    >
                        <span class="text-lfg-text text-sm">{{ request.user.name }}</span>
                        <div class="space-x-3">
                            <button
                                @click="acceptRequest(request.id)"
                                class="text-lfg-cyan text-xs hover:underline"
                            >
                                Accept
                            </button>
                            <button
                                @click="declineRequest(request.id)"
                                class="text-lfg-orange text-xs hover:underline"
                            >
                                Decline
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    v-if="isMember && !isHost"
                    @click="leaveParty"
                    class="border-lfg-orange text-lfg-orange hover:bg-lfg-orange/10 w-full rounded-md border py-2.5 text-sm font-medium tracking-wide uppercase"
                >
                    Leave Party
                </button>

                <button
                    v-else-if="!isMember && post.status === 'open'"
                    @click="requestToJoin"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full rounded-md border py-2.5 text-sm font-medium tracking-wide uppercase"
                >
                    Request to Join
                </button>
            </div>
        </div>
    </AppLayout>
</template>
