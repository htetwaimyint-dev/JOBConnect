<script setup lang="ts">
import { AlertCircle, RefreshCw } from "lucide-vue-next";

defineProps<{
    message: string;
}>();

const emit = defineEmits<{
    retry: [];
}>();
</script>

<template>
    <div class="rounded-2xl border border-red-100 bg-white px-6 py-16 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500">
            <AlertCircle class="h-7 w-7" />
        </div>

        <h3 class="mt-5 text-lg font-bold text-slate-900">
            Unable to load applications
        </h3>

        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            {{ message }}
        </p>

        <button type="button" @click="emit('retry')"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-800">
            <RefreshCw class="h-4 w-4" />
            Try Again
        </button>
    </div>
</template>
