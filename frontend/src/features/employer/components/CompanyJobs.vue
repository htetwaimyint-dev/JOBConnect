<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import type { Job } from "../types/company.types";

defineProps<{
    jobs: Job[];
}>();

const emit = defineEmits<{
    selectJob: [job: Job];
}>();
</script>

<template>
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Open Positions</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Explore career opportunities at this company.
                </p>
            </div>

            <span
                class="rounded-full bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700"
            >
                {{ jobs.length }} positions
            </span>
        </div>

        <div v-if="jobs.length" class="space-y-4">
            <article
                v-for="job in jobs"
                :key="job.id"
                class="rounded-none border border-gray-100 bg-white p-5 transition hover:border-violet-200 hover:shadow-md sm:p-6"
            >
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start"
                >
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            {{ job.title }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            {{ job.department }}
                        </p>
                    </div>

                    <span
                        class="w-fit rounded-lg bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700"
                    >
                        {{ job.employmentType }}
                    </span>
                </div>

                <div
                    class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs text-gray-500"
                >
                    <span>{{ job.location }}</span>
                    <span>{{ job.experienceLevel }}</span>
                    <span>Posted {{ job.postedAt }}</span>
                </div>

                <div
                    class="mt-5 flex justify-end border-t border-gray-100 pt-4"
                >
                    <BaseButton
                        type="button"
                        class="rounded-none! px-10! py-2!"
                        @click="emit('selectJob', job)"
                    >
                        View Details
                    </BaseButton>
                </div>
            </article>
        </div>

        <div
            v-else
            class="rounded-2xl border border-gray-100 bg-white px-6 py-16 text-center"
        >
            <h3 class="font-semibold text-gray-800">No open positions</h3>
            <p class="mt-2 text-sm text-gray-500">
                There are currently no available jobs at this company.
            </p>
        </div>
    </section>
</template>
