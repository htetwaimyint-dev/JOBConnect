<script setup lang="ts">
import { Search, RotateCcw } from "lucide-vue-next";

import type { ApplicationStatus } from "@/features/applications/types/employer-application.types";

defineProps<{
    searchKeyword: string;
    selectedStatus: ApplicationStatus | "";
    statusOptions: ApplicationStatus[];
    loading: boolean;
}>();

const emit = defineEmits<{
    "update:searchKeyword": [value: string];
    "update:selectedStatus": [value: ApplicationStatus | ""];
    search: [];
    reset: [];
}>();

const formatLabel = (value: string): string =>
    value.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_220px_auto]">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Search applicants
                </label>

                <div class="relative">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

                    <input :value="searchKeyword" type="text" placeholder="Search applicant name or job..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        @input="emit('update:searchKeyword', ($event.target as HTMLInputElement).value)"
                        @keyup.enter="emit('search')" />
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Application status
                </label>

                <select :value="selectedStatus"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    @change="emit('update:selectedStatus', ($event.target as HTMLSelectElement).value as ApplicationStatus | '')">
                    <option value="">All statuses</option>

                    <option v-for="status in statusOptions" :key="status" :value="status">
                        {{ formatLabel(status) }}
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="button" :disabled="loading" @click="emit('search')"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-900 disabled:opacity-50 md:flex-none">
                    <Search class="h-4 w-4" />
                    Search
                </button>

                <button type="button" :disabled="loading" @click="emit('reset')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50">
                    <RotateCcw class="h-4 w-4" />
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>
        </div>
    </section>
</template>
