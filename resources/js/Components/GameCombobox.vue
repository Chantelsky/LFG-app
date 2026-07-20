<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

interface IgdbGame {
    id: number;
    name: string;
    cover?: { url: string };
    artworks?: { url: string }[];
}

const props = defineProps<{
    modelValue: {
        igdb_id: number | null;
        name: string;
        coverUrl: string | null;
        artworkUrl: string | null;
    };
}>();

const emit = defineEmits<{
    'update:modelValue': [
        value: {
            igdb_id: number | null;
            name: string;
            coverUrl: string | null;
            artworkUrl: string | null;
        },
    ];
}>();

const query = ref(props.modelValue.name || '');
const results = ref<IgdbGame[]>([]);
const showDropdown = ref(false);
const loading = ref(false);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;
let suppressNextWatch = false;

watch(query, (newQuery) => {
    if (suppressNextWatch) {
        suppressNextWatch = false;
        return;
    }

    emit('update:modelValue', { igdb_id: null, name: newQuery, coverUrl: null, artworkUrl: null });

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

function artworkUrl(game: IgdbGame): string | null {
    if (!game.artworks?.length) return null;
    return `https:${game.artworks[0].url.replace('t_thumb', 't_1080p')}`;
}

function selectGame(game: IgdbGame) {
    query.value = game.name;
    showDropdown.value = false;
    suppressNextWatch = true;
    emit('update:modelValue', {
        igdb_id: game.id,
        name: game.name,
        coverUrl: coverUrl(game),
        artworkUrl: artworkUrl(game),
    });
}

function coverUrl(game: IgdbGame): string | null {
    if (!game.cover?.url) return null;
    return `https:${game.cover.url.replace('t_thumb', 't_cover_big')}`;
}

function handleFocus() {
    if (results.value.length) showDropdown.value = true;
}

function handleBlur() {
    setTimeout(() => {
        showDropdown.value = false;
    }, 150);
}
</script>

<template>
    <div class="relative">
        <input
            v-model="query"
            type="text"
            placeholder="Search for a game..."
            @focus="handleFocus"
            @blur="handleBlur"
            :class="[
                'bg-lfg-bg text-lfg-text placeholder-lfg-muted w-full rounded-md border p-2.5 text-sm focus:ring-1 focus:outline-none',
                hasError
                    ? 'border-lfg-orange focus:border-lfg-orange focus:ring-lfg-orange'
                    : 'border-lfg-border focus:border-lfg-cyan focus:ring-lfg-cyan',
            ]"
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
                    v-if="game.cover?.url"
                    :src="`https:${game.cover.url.replace('t_thumb', 't_thumb')}`"
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
