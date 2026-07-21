<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { useEcho } from '@laravel/echo-vue';
import { IconLock, IconUsers, IconStar, IconWorld, IconHeadphones } from '@tabler/icons-vue';

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

interface ChatMessage {
    id: number;
    body: string;
    user: { id: number; name: string };
    created_at: string;
}

interface Post {
    uuid: string;
    title: string;
    skill_rank: string | null;
    region: string | null;
    timezone: string | null;
    availability: string | null;
    comm_preference: string;
    status: string;
    current_members: number;
    party_size: number;
    game: { name: string; cover_art: string | null; artwork_url: string | null };
    user: User;
    party_members: PartyMember[];
    join_requests: JoinRequestItem[];
    messages: ChatMessage[];
}

const props = defineProps<{
    post: Post;
}>();

const page = usePage<{
    auth: { user: { id: number } };
}>();

const currentUserId = page.props.auth.user.id;
const isHost = computed(() => props.post.user.id === currentUserId);
const isMember = computed(() => props.post.party_members.some((m) => m.user.id === currentUserId));
const pendingRequests = computed(() =>
    props.post.join_requests.filter((r) => r.status === 'pending')
);

function requestToJoin() {
    router.post(`/posts/${props.post.uuid}/join-requests`);
}

function leaveParty() {
    router.delete(`/posts/${props.post.uuid}/leave`);
}

function removeMember(partyMemberId: number) {
    router.delete(`/posts/${props.post.uuid}/members/${partyMemberId}`);
}

function acceptRequest(requestId: number) {
    router.patch(`/join-requests/${requestId}/accept`);
}

function declineRequest(requestId: number) {
    router.patch(`/join-requests/${requestId}/decline`);
}

const messages = ref<ChatMessage[]>(props.post.messages ?? []);
const newMessageBody = ref('');

useEcho(`post.${props.post.id}`, 'MessageSent', (event: ChatMessage) => {
    messages.value.push(event);
});

function sendMessage() {
    if (!newMessageBody.value.trim()) return;

    const body = newMessageBody.value;

    router.post(
        `/posts/${props.post.uuid}/messages`,
        { body },
        {
            preserveScroll: true,
            onSuccess: () => {
                messages.value.push({
                    id: Date.now(),
                    body,
                    user: { id: currentUserId, name: 'You' },
                    created_at: new Date().toISOString(),
                });
                newMessageBody.value = '';
            },
        }
    );
}
</script>

<template>
    <AppLayout>
        <div class="flex h-[calc(100vh-2rem)] gap-4 p-4">
            <Toast type="error" />
            <Toast type="success" />

            <!-- left sidebar: post info, party roster, requests, actions -->
            <div class="flex w-72 shrink-0 flex-col gap-4 overflow-y-auto">
                <!-- post info card -->
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

                <!-- member/host view: roster, requests, leave -->
                <template v-if="isMember">
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

                    <!-- pending requests: host only -->
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

                    <!-- leave party: members only, not host -->
                    <button
                        v-if="!isHost"
                        @click="leaveParty"
                        class="border-lfg-orange text-lfg-orange hover:bg-lfg-orange/10 w-full cursor-pointer rounded-md border py-2.5 text-xs font-medium tracking-wide uppercase"
                    >
                        Leave Party
                    </button>
                </template>

                <!-- non-member view: request to join only -->
                <template v-else>
                    <div class="border-lfg-border bg-lfg-surface rounded-md border p-4">
                        <p class="text-lfg-muted mb-2 text-sm">
                            Join this lobby to see who's in the party and chat with the group.
                        </p>
                        <button
                            v-if="post.status === 'open'"
                            @click="requestToJoin"
                            class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full cursor-pointer rounded-md border py-2.5 text-xs font-medium tracking-wide uppercase"
                        >
                            Request to Join
                        </button>
                        <p v-else class="text-lfg-muted text-xs">This lobby is currently full.</p>
                    </div>
                </template>
            </div>

            <!-- chat panel: members only -->
            <div
                v-if="isMember"
                class="border-lfg-border bg-lfg-surface flex flex-1 flex-col rounded-md border"
            >
                <div class="border-lfg-border border-b p-4">
                    <p class="text-lfg-muted text-xs font-medium tracking-wide uppercase">
                        Party Chat
                    </p>
                </div>

                <div class="flex-1 space-y-3 overflow-y-auto p-4">
                    <div
                        v-if="!messages.length"
                        class="text-lfg-muted flex h-full items-center justify-center text-sm"
                    >
                        No messages yet. Say hello.
                    </div>
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="max-w-[75%]"
                        :class="msg.user.id === currentUserId ? 'ml-auto' : ''"
                    >
                        <p class="text-lfg-muted mb-1 text-xs">{{ msg.user.name }}</p>
                        <div
                            class="rounded-md px-3 py-2 text-sm"
                            :class="
                                msg.user.id === currentUserId
                                    ? 'bg-lfg-pink/20 text-lfg-text'
                                    : 'bg-lfg-bg text-lfg-text'
                            "
                        >
                            {{ msg.body }}
                        </div>
                    </div>
                </div>

                <form
                    @submit.prevent="sendMessage"
                    class="border-lfg-border flex gap-2 border-t p-4"
                >
                    <input
                        v-model="newMessageBody"
                        type="text"
                        placeholder="Message the party..."
                        class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan flex-1 rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 cursor-pointer rounded-md border px-4 text-sm font-medium"
                    >
                        Send
                    </button>
                </form>
            </div>

            <!-- chat panel: non-member placeholder -->
            <div
                v-else
                class="border-lfg-border bg-lfg-surface flex flex-1 flex-col items-center justify-center gap-3 rounded-md border p-8 text-center"
            >
                <div
                    class="border-lfg-border flex h-12 w-12 items-center justify-center rounded-full border"
                >
                    <IconLock :size="20" class="text-lfg-muted" />
                </div>
                <div>
                    <p class="text-lfg-text mb-1 text-sm font-medium">Squad chat is members-only</p>
                    <p class="text-lfg-muted text-xs">
                        Request to join to see what the squad's saying.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
