<script setup lang="ts">
import type { Job, JobFormData } from "../types/job.types";
import { formatJobLabel, formatJobSalary } from "@/utils/jobs.utils";

defineProps<{
    job: Job | JobFormData;
}>();
</script>

<template>
    <section class="space-y-4">
        <h3 class="text-base font-bold text-gray-900">Job Overview</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Employment Type</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatJobLabel(job.employment_type) }}
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-purple-50 p-2 text-purple-600">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.196-2.045M17 20H7m10 0v-2c0-.728-.195-1.411-.536-2M7 20H2v-2a3 3 0 015.196-2.045M7 20v-2c0-.728.195-1.411.536-2m0 0a5.002 5.002 0 019.928 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Available Positions</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ job.vacancies || 1 }}
                        Position(s)
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-green-50 p-2 text-green-600">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Salary</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatJobSalary(job) }}
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="rounded-lg bg-orange-50 p-2 text-orange-600">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Gender Preference</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatJobLabel(job.gender) || "Any Gender" }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="job.deadline"
            class="rounded-lg bg-gray-50 p-3 text-sm text-gray-600"
        >
            Application deadline:
            <strong class="text-gray-900">
                {{ job.deadline }}
            </strong>
        </div>
    </section>
</template>
