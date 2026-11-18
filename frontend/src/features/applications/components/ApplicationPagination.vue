<script setup lang="ts">
import { ChevronLeft, ChevronRight } from "lucide-vue-next";

defineProps<{
    currentPage: number;
    lastPage: number;
    total: number;
    loading: boolean;
}>();

const emit = defineEmits<{
    changePage: [page: number];
}>();
</script>

<template>
    <div v-if="lastPage > 1"
        class="flex flex-col items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-sm sm:flex-row">
        <p class="text-sm text-slate-500">
            Page
            <span class="font-bold text-slate-900">
                {{ currentPage }}
            </span>
            of
            <span class="font-bold text-slate-900">
                {{ lastPage }}
            </span>
            <span class="ml-1">
                ({{ total }} total)
            </span>
        </p>

        <div class="flex items-center gap-3">
            <button type="button" :disabled="currentPage <= 1 || loading" @click="emit('changePage', currentPage - 1)"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40">
                <ChevronLeft class="h-4 w-4" />
                Previous
            </button>

            <button type="button" :disabled="currentPage >= lastPage || loading"
                @click="emit('changePage', currentPage + 1)"
                class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-40">
                Next
                <ChevronRight class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>
