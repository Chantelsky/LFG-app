<script setup></script>

<template>
    <div class="bg-lfg-bg flex min-h-screen flex-col items-center justify-center px-4">
        <div
            class="border-lfg-cyan/30 bg-lfg-surface w-full overflow-hidden rounded-md border px-6 py-6 shadow-[0_0_40px_-12px_rgba(0,229,255,0.25)] sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
