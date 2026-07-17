<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const props = defineProps<{
    type: "error" | "success";
}>();

const page = usePage<{
    flash: {
        error: string | null;
        success: string | null;
    };
}>();

const visible = ref(false);
const currentMessage = ref<string | null>(null);
let timeoutId: ReturnType<typeof setTimeout> | null = null;

const colorClasses = computed(() =>
    props.type === "error"
        ? "border-lfg-pink text-lfg-pink"
        : "border-lfg-cyan text-lfg-cyan",
);

function showIfPresent() {
    const message = page.props.flash[props.type];
    if (!message) return;

    currentMessage.value = message;
    visible.value = true;

    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        visible.value = false;
    }, 4000);
}

let removeListener: (() => void) | null = null;

onMounted(() => {
    showIfPresent();
    removeListener = router.on("finish", showIfPresent);
});

onUnmounted(() => {
    removeListener?.();
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-300 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            :class="colorClasses"
            class="fixed top-4 left-1/2 -translate-x-1/2 bg-lfg-surface border text-sm px-4 py-3 rounded shadow-lg z-50"
        >
            {{ currentMessage }}
        </div>
    </Transition>
</template>
