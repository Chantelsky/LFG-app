<script setup lang="ts">
withDefaults(
    defineProps<{
        showText?: boolean;
        showTagline?: boolean;
        size?: 'sm' | 'lg';
    }>(),
    {
        showText: true,
        showTagline: false,
        size: 'sm',
    }
);
</script>

<template>
    <div class="flex items-center gap-3">
        <svg
            :width="size === 'lg' ? 48 : 28"
            :height="size === 'lg' ? 52 : 28"
            viewBox="0 0 130 140"
            role="img"
            aria-label="LFG logo"
        >
            <path d="M0 70 L45 20 L60 20 L15 70 L60 120 L45 120 Z" fill="#FF2E88" />
            <path d="M35 70 L80 20 L95 20 L50 70 L95 120 L80 120 Z" fill="#00E5FF" opacity="0.85" />
            <path
                d="M70 70 L115 20 L130 20 L85 70 L130 120 L115 120 Z"
                fill="#9B6BFF"
                opacity="0.85"
            />
        </svg>
        <div v-if="showText">
            <span
                :class="size === 'lg' ? 'text-3xl' : 'text-lg'"
                class="text-lfg-text block leading-none font-medium tracking-wide uppercase"
            >
                LFG
            </span>
            <span
                v-if="showTagline"
                :class="size === 'lg' ? 'mt-1.5 text-xs' : 'mt-1 text-[10px]'"
                class="text-lfg-muted block tracking-widest uppercase"
            >
                Find your squad
            </span>
        </div>
    </div>
</template>
