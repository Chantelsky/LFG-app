<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

declare const setTimeout: typeof globalThis.setTimeout;

interface IgdbGame {
    id: number;
    name: string;
    cover?: { url: string };
}

const props = defineProps<{
    modelValue: { igdb_id: number | null; name: string };
}>();

const emit = defineEmits<{
    'update:modelValue': [value: { igdb_id: number | null; name: string }];
}>();

const query = ref(props.modelValue.name || '');
const results = ref<IgdbGame[]>([]);
const showDropdown = ref(false);
const loading = ref(false);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(query, (newQuery) => {
    emit('update:modelValue', { igdb_id: null, name: newQuery });

    if (debounceTimer) clearTimeout(debounceTimer);

    if (newQuery.length < 2) {
        results.value = [];
        showDropdown.value = false;
        return;
    }

    debounceTimer = setTimeout(async () => {
        loading.value = true;
        try {
            const response = await axios.get('/games/search', {
                params: { q: newQuery },
            });
            results.value = response.data;
            showDropdown.value = true;
        } finally {
            loading.value = false;
        }
    }, 400);
});

function selectGame(game: IgdbGame) {
    query.value = game.name;
    showDropdown.value = false;
    emit('update:modelValue', { igdb_id: game.id, name: game.name });
}

function coverUrl(game: IgdbGame): string | null {
    if (!game.cover?.url) return null;
    return `https:${game.cover.url.replace('t_thumb', 't_cover_small')}`;
}
</script>

<template>
    <div class="relative">
        <input
            v-model="query"
            type="text"
            placeholder="Search for a game..."
            @focus="results.length && (showDropdown = true)"
            @blur="setTimeout(() => (showDropdown = false), 150)"
            class="border-lfg-border bg-lfg-bg text-lfg-text placeholder-lfg-muted focus:border-lfg-cyan focus:ring-lfg-cyan w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none"
        />

        <div
            v-if="showDropdown && results.length"
            class="border-lfg-border bg-lfg-surface absolute z-10 mt-1 max-h-64 w-full overflow-y-auto rounded-md border shadow-lg"
        >
            <button
                v-for="game in results"
                :key="game.id"
                type="button"
                @mousedown.prevent="selectGame(game)"
                class="text-lfg-text hover:bg-lfg-bg flex w-full items-center gap-3 p-2.5 text-left text-sm"
            >
                <img
                    v-if="coverUrl(game)"
                    :src="coverUrl(game)!"
                    class="h-10 w-8 rounded object-cover"
                    alt=""
                />
                <div v-else class="bg-lfg-bg h-10 w-8 rounded"></div>
                {{ game.name }}
            </button>
        </div>

        <p
            v-if="showDropdown && !results.length && !loading && query.length >= 2"
            class="text-lfg-muted mt-1 text-xs"
        >
            No matches — this will be added as a new game.
        </p>
    </div>
</template>
