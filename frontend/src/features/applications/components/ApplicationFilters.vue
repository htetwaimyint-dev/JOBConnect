<script setup lang="ts">
import {
    Search,
    SlidersHorizontal,
    X,
} from "lucide-vue-next";

import type { ApplicationStatus } from "../types/application.types";

defineProps<{
    searchKeyword: string;
    selectedStatus: ApplicationStatus | "";
    resultCount: number;
}>();

const emit = defineEmits<{
    "update:searchKeyword": [value: string];
    "update:selectedStatus": [value: ApplicationStatus | ""];
    statusChange: [];
    clear: [];
}>();

const updateSearch = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit("update:searchKeyword", target.value);
};

const updateStatus = (event: Event) => {
    const target = event.target as HTMLSelectElement;

    emit(
        "update:selectedStatus",
        target.value as ApplicationStatus | ""
    );

    emit("statusChange");
};

const clearSearch = () => {
    emit("update:searchKeyword", "");
};

</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <!-- Filter Header -->
        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <SlidersHorizontal class="h-5 w-5" />
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-800">
                    Search & Filters
                </h3>

                <p class="mt-0.5 text-xs text-slate-500">
                    Find and manage your applications
                </p>
            </div>
        </div>

        <!-- Search and Status Filter -->
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                <!-- Search -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Search Applications
                    </label>

                    <div class="relative">
                        <!-- Search Icon -->
                        <Search
                            class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />

                        <!-- Search Input -->
                        <input type="text" :value="searchKeyword" @input="updateSearch"
                            placeholder="Search job title or company..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-11 text-sm text-slate-800 outline-none transition duration-200 placeholder:text-slate-400 hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10" />

                        <!-- Clear Search -->
                        <button v-if="searchKeyword" type="button" @click="clearSearch" aria-label="Clear search"
                            class="absolute right-3 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700">
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Filter by Status
                    </label>

                    <div class="relative">
                        <select :value="selectedStatus" @change="updateStatus"
                            class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 text-sm text-slate-700 outline-none transition duration-200 hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                            <option value="">
                                All Applications
                            </option>

                            <option value="pending">
                                Pending
                            </option>

                            <option value="reviewed">
                                Reviewed
                            </option>

                            <option value="shortlisted">
                                Shortlisted
                            </option>

                            <option value="interview">
                                Interview
                            </option>

                            <option value="accepted">
                                Accepted
                            </option>

                            <option value="rejected">
                                Rejected
                            </option>
                        </select>

                        <!-- Dropdown Icon -->
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filter Footer -->
            <div
                class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <!-- Result Count -->
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                        <Search class="h-4 w-4 text-blue-600" />
                    </div>

                    <p class="text-sm text-slate-500">
                        Showing

                        <span class="font-bold text-slate-800">
                            {{ resultCount }}
                        </span>

                        applications on this page
                    </p>
                </div>

                <!-- Clear Filters -->
                <button v-if="searchKeyword || selectedStatus" type="button" @click="emit('clear')"
                    class="inline-flex items-center justify-center gap-2 self-start rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 sm:self-auto">
                    <X class="h-3.5 w-3.5" />

                    Clear all filters
                </button>
            </div>
        </div>
    </div>
</template>