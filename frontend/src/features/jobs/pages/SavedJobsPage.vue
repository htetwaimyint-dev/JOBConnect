<script setup lang="ts">
import { computed } from "vue";
import { RouterLink } from "vue-router";

import { useSavedJobs } from "../composables/useSavedJobs";
import JobCard from "../components/JobCard.vue";

const { savedJobsQuery } = useSavedJobs();

const savedJobs = computed(
    () => savedJobsQuery.data.value?.data ?? []
);
</script>

<template>
    <div class="min-h-screen bg-[#f7f7fb]">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Saved Jobs
                </h1>

                <p class="mt-2 text-gray-500">
                    Jobs you've saved to apply for later.
                </p>
            </div>

            <!-- Loading -->
            <div v-if="savedJobsQuery.isLoading.value" class="flex min-h-[300px] items-center justify-center">
                <p class="text-gray-500">
                    Loading saved jobs...
                </p>
            </div>

            <!-- Error -->
            <div v-else-if="savedJobsQuery.isError.value"
                class="rounded-2xl border border-red-200 bg-white p-8 text-center">
                <h2 class="text-lg font-semibold text-gray-900">
                    Failed to load saved jobs
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    Something went wrong while loading your saved jobs.
                </p>

                <button type="button"
                    class="mt-5 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700"
                    @click="savedJobsQuery.refetch()">
                    Try Again
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="savedJobs.length === 0"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50">
                    <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18l-6-4-6 4V4Z" />
                    </svg>
                </div>

                <h2 class="mt-5 text-xl font-bold text-gray-900">
                    No saved jobs yet
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    Save jobs that interest you and find them here later.
                </p>

                <RouterLink to="/jobs"
                    class="mt-6 inline-flex items-center rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white hover:bg-blue-600">
                    Browse Jobs
                </RouterLink>
            </div>

            <!-- Saved Jobs -->
            <div v-else>
                <p class="mb-5 text-sm text-gray-500">
                    {{ savedJobs.length }}
                    saved job(s)
                </p>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <JobCard v-for="savedJob in savedJobs" :key="savedJob.id" :job="savedJob.job" />
                </div>
            </div>

        </div>
    </div>
</template>