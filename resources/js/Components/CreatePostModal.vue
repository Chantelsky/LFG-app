<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import GameCombobox from '@/Components/GameCombobox.vue';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const selectedGame = ref<{ igdb_id: number | null; name: string; coverUrl: string | null }>({
    igdb_id: null,
    name: '',
    coverUrl: null,
});

const form = useForm({
    game_name: '',
    igdb_id: null as number | null,
    title: '',
    skill_rank: '',
    region: '',
    timezone: '',
    availability: '',
    comm_preference: 'mic_optional',
    join_mode: 'manual_review',
    party_size: 5,
});

function submit() {
    form.transform((data) => ({
        ...data,
        igdb_id: selectedGame.value.igdb_id,
        game_name: selectedGame.value.name,
    })).post('/posts', {
        onSuccess: () => {
            form.reset();
            selectedGame.value = { igdb_id: null, name: '', coverUrl: null };
            emit('close');
        },
    });
}

function close() {
    form.reset();
    form.clearErrors();
    selectedGame.value = { igdb_id: null, name: '', coverUrl: null };
    emit('close');
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="close"></div>

        <div
            class="custom-scrollbar border-lfg-cyan/30 bg-lfg-surface relative z-50 max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-md border p-6 shadow-[0_0_40px_-12px_rgba(0,229,255,0.25)]"
        >
            <div class="mb-1 flex items-center justify-between">
                <h2 class="text-lfg-text text-xl font-medium tracking-wide uppercase">
                    <span class="text-lfg-pink">Create a Lobby</span>
                </h2>
                <button @click="close" class="text-lfg-muted hover:text-lfg-text text-sm">✕</button>
            </div>

            <p class="text-lfg-muted mb-6 text-sm">Tell the lobby what you're looking for.</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="flex items-stretch gap-4">
                    <div
                        class="border-lfg-border bg-lfg-bg w-24 shrink-0 overflow-hidden rounded-md border"
                    >
                        <img
                            v-if="selectedGame.coverUrl"
                            :src="selectedGame.coverUrl"
                            :alt="selectedGame.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="text-lfg-muted flex h-full w-full items-center justify-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 18v3m-4 0h8M5 3h14a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1z"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3">
                        <div>
                            <label
                                class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                                >Game</label
                            >
                            <GameCombobox
                                v-model="selectedGame"
                                :has-error="!!form.errors.game_name"
                            />
                            <p
                                v-if="form.errors.game_name"
                                class="text-lfg-orange mt-1 text-xs font-medium"
                            >
                                {{ form.errors.game_name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                                >Title</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Need duo for ranked climb"
                                :class="[
                                    'bg-lfg-bg text-lfg-text placeholder-lfg-muted w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none',
                                    form.errors.title
                                        ? 'border-lfg-orange focus:border-lfg-orange focus:ring-lfg-orange'
                                        : 'border-lfg-border focus:border-lfg-cyan focus:ring-lfg-cyan',
                                ]"
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-lfg-orange mt-1 text-xs font-medium"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                        >Skill / Rank</label
                    >
                    <input
                        v-model="form.skill_rank"
                        type="text"
                        placeholder="e.g. Immortal 1, Diamond II"
                        class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                            >Region</label
                        >
                        <input
                            v-model="form.region"
                            type="text"
                            placeholder="NA-East"
                            class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                            >Timezone</label
                        >
                        <input
                            v-model="form.timezone"
                            type="text"
                            placeholder="ET, CET, PT"
                            class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                        >Availability</label
                    >
                    <input
                        v-model="form.availability"
                        type="text"
                        placeholder="Weeknights 7-11pm"
                        class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                            >Party Size</label
                        >
                        <input
                            v-model.number="form.party_size"
                            type="number"
                            min="2"
                            max="10"
                            :class="[
                                'bg-lfg-bg text-lfg-text w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none',
                                form.errors.party_size
                                    ? 'border-lfg-orange focus:border-lfg-orange focus:ring-lfg-orange'
                                    : 'border-lfg-border focus:border-lfg-cyan focus:ring-lfg-cyan',
                            ]"
                        />
                        <p v-if="form.errors.party_size" class="text-lfg-orange mt-1 text-xs">
                            {{ form.errors.party_size }}
                        </p>
                    </div>
                    <div>
                        <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                            >Voice Comms</label
                        >
                        <select
                            v-model="form.comm_preference"
                            class="border-lfg-border bg-lfg-bg text-lfg-text focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                        >
                            <option value="mic_required">Mic required</option>
                            <option value="mic_optional">Mic optional</option>
                            <option value="text_only">Text only</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-lfg-muted mb-1.5 block text-xs tracking-wide uppercase"
                        >Join Mode</label
                    >
                    <select
                        v-model="form.join_mode"
                        class="border-lfg-border bg-lfg-bg text-lfg-text focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
                    >
                        <option value="auto_accept">Auto-accept</option>
                        <option value="manual_review">Manual review</option>
                    </select>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="border-lfg-cyan text-lfg-cyan hover:bg-lfg-cyan/10 w-full rounded-md border py-2.5 text-sm font-medium tracking-wide uppercase transition hover:shadow-[0_0_20px_-4px_rgba(0,229,255,0.5)] disabled:opacity-50"
                >
                    Post it up
                </button>
            </form>
        </div>
    </div>
</template>
