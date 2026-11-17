<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import type { Company } from "../types/company.types";
import type { Job } from "@/features/jobs/types/job.types";

defineProps<{
    jobs: Job[];
    company?: Company;
}>();

const emit = defineEmits<{
    selectJob: [job: Job];
    viewAllJobs: [];
}>();
</script>

<template>
    <section class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">
                Jobs From {{ company?.name || "Company" }}
            </h2>
            <button type="button" class="text-sm font-semibold text-blue-600 hover:underline"
                @click="emit('viewAllJobs')">
                View All Jobs
            </button>
        </div>

        <!-- Job Cards List -->
        <div v-if="jobs.length" class="space-y-4">
            <article v-for="job in jobs" :key="job.id"
                class="rounded-md border border-gray-100 bg-white p-6 transition hover:border-gray-200">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            {{ job.title }}
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ company?.name }} •
                            {{
                                job.city && job.country
                                    ? `${job.city}, ${job.country}`
                                    : job.city || job.country || company?.city || company?.address || "Location not specified"
                            }}
                        </p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </button>
                </div>

                <!-- Tags -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <span v-if="job.employment_type"
                        class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                        {{ job.employment_type }}
                    </span>
                    <!-- <span
                        v-if="job.locationType"
                        class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                    >
                        {{ job.locationType }}
                    </span> -->
                    <!-- <span
                        v-if="job.experienceLevel"
                        class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                    >
                        {{ job.experienceLevel }}
                    </span> -->
                </div>

                <!-- Footer meta -->
                <div class="mt-5 flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-100">
                    <!-- <span
                        >Posted {{ job.postedAt || "Recently" }} •
                        {{ job.applicantsCount || 0 }} Applicants</span
                    > -->
                    <BaseButton type="button"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition"
                        @click="emit('selectJob', job)">
                        View Details
                    </BaseButton>
                </div>
            </article>
        </div>

        <!-- Empty State -->
        <div v-else
            class="flex items-stretch justify-center rounded-md border border-gray-100 bg-white p-8 text-center">
            <div
                class="px-5 py-6 w-full flex flex-col justify-center items-center border-2 border-dashed border-gray-200 rounded-md">
                <h3 class="font-semibold text-gray-800">No open positions</h3>
                <p class="mt-1 text-sm text-gray-500">
                    There are currently no available jobs at this company.
                </p>
            </div>
        </div>
    </section>
</template>
