<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    IconWorld,
    IconClock,
    IconHeadphones,
    IconShield,
    IconPlus,
    IconSword,
    IconStar,
} from '@tabler/icons-vue';

interface PartyMember {
    id: number;
    user: { id: number; name: string };
}

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
    game: { name: string; cover_art: string | null; artwork_url: string | null };
    user: { name: string };
    user_id: number;
    party_members: PartyMember[];
    created_at: string;
}

const props = defineProps<{
    post: Post;
    currentUserId: number;
}>();

const emit = defineEmits<{
    requestToJoin: [postId: number];
}>();

const avatarColors = ['#FF2E88', '#00E5FF', '#FFC700', '#9B6BFF', '#3ADC8C'];

const postedAgo = computed(() => {
    const diff = Date.now() - new Date(props.post.created_at).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours}h ago`;
    return `${Math.floor(hours / 24)}d ago`;
});

const roleIconMap: Record<string, { icon: any; color: string }> = {
    tank: { icon: IconShield, color: '#00E5FF' },
    healer: { icon: IconPlus, color: '#3ADC8C' },
    dps: { icon: IconSword, color: '#9B6BFF' },
    support: { icon: IconStar, color: '#FFC700' },
};

function getRoleIcon(role: string) {
    return roleIconMap[role.toLowerCase()] ?? { icon: IconStar, color: '#8A8A96' };
}
</script>

<template>
    <div>
        <div
            class="border-lfg-border bg-lfg-surface hover:border-lfg-cyan/40 grid items-center gap-6 rounded-lg border p-6 transition"
            style="grid-template-columns: 90px 1.8fr 130px 140px 220px"
        >
            <!-- game thumbnail -->
            <div class="bg-lfg-bg h-[90px] w-[90px] overflow-hidden rounded-xl">
                <img
                    v-if="post.game.artwork_url || post.game.cover_art"
                    :src="post.game.artwork_url ?? post.game.cover_art ?? undefined"
                    :alt="post.game.name"
                    class="h-full w-full object-cover"
                />
            </div>

            <!-- game info: title, tags, hosted by -->
            <div class="min-w-0">
                <p class="text-lfg-pink mb-1 text-xs font-medium tracking-wide uppercase">
                    {{ post.game.name }}
                </p>
                <h3 class="text-lfg-text mb-2 text-xl font-medium">{{ post.title }}</h3>
                <div v-if="post.skill_rank" class="mb-3 flex gap-2">
                    <span class="bg-lfg-bg text-lfg-muted rounded-full px-2.5 py-1 text-xs">
                        {{ post.skill_rank }}
                    </span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <div class="bg-lfg-pink h-6 w-6 flex-shrink-0 rounded-full"></div>
                    <span class="text-lfg-muted text-xs">
                        Hosted by {{ post.user.name }} <span class="ml-1">· {{ postedAgo }}</span>
                    </span>
                </div>
            </div>

            <!-- roles needed (optional per post) -->
            <div v-if="post.roles_needed?.length" class="border-lfg-border border-l pl-6">
                <p class="text-lfg-muted mb-3 text-xs tracking-wide uppercase">Need</p>
                <div class="flex gap-3">
                    <div
                        v-for="(role, i) in post.roles_needed"
                        :key="i"
                        class="flex flex-col items-center gap-1"
                    >
                        <component
                            :is="getRoleIcon(role).icon"
                            :size="18"
                            :style="{ color: getRoleIcon(role).color }"
                        />
                        <span class="text-[10px]" :style="{ color: getRoleIcon(role).color }">{{
                            role
                        }}</span>
                    </div>
                </div>
            </div>
            <div v-else class="border-lfg-border border-l pl-6"></div>

            <!-- players count and avatar stack -->
            <div class="border-lfg-border border-l pl-6">
                <p class="text-lfg-muted mb-3 text-xs tracking-wide uppercase">Players</p>
                <h4 class="text-lfg-text mb-3 text-2xl font-medium">
                    {{ post.current_members
                    }}<span class="text-lfg-muted">/{{ post.party_size }}</span>
                </h4>
                <div class="flex">
                    <div
                        v-for="(member, index) in post.party_members.slice(0, 4)"
                        :key="member.id"
                        :style="{ backgroundColor: avatarColors[index % avatarColors.length] }"
                        class="border-lfg-surface text-lfg-bg -mr-2.5 flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-medium"
                    >
                        {{ member.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div
                        v-for="i in Math.max(post.party_size - post.party_members.length, 0)"
                        :key="`empty-${i}`"
                        class="border-lfg-surface bg-lfg-bg -mr-2.5 h-10 w-10 rounded-full border-2"
                    ></div>
                </div>
            </div>

            <!-- metadata (region, posted time, comms) and action button -->
            <div class="border-lfg-border flex flex-col justify-center gap-2 border-l pl-6">
                <div class="text-lfg-muted space-y-1.5 text-sm">
                    <div v-if="post.region || post.timezone" class="flex items-center gap-2">
                        <IconWorld :size="16" />
                        {{ [post.region, post.timezone].filter(Boolean).join(' · ') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <IconClock :size="16" />
                        {{ postedAgo }}
                    </div>
                    <div class="flex items-center gap-2">
                        <IconHeadphones :size="16" />
                        {{ post.comm_preference.replace('_', ' ') }}
                    </div>
                </div>

                <Link
                    v-if="post.user_id === currentUserId"
                    :href="`/posts/${post.id}`"
                    class="border-lfg-pink text-lfg-pink hover:bg-lfg-pink/10 mt-2 flex h-12 cursor-pointer items-center justify-center rounded-xl border text-sm font-semibold"
                >
                    Manage
                </Link>
                <button
                    v-else
                    @click="emit('requestToJoin', post.id)"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 mt-2 flex h-12 cursor-pointer items-center justify-center rounded-xl border text-sm font-semibold"
                >
                    Join Lobby
                </button>
            </div>
        </div>
    </div>
</template>
