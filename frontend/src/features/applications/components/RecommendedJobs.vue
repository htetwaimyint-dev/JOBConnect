<script setup lang="ts">
import { ref } from "vue";
import { RouterLink } from "vue-router";

import {
    ArrowUpRight,
    BriefcaseBusiness,
    Building2,
    MapPin,
} from "lucide-vue-next";

import { getCompanyLogoUrl } from "../utils/companyLogo";
import { formatEmploymentType } from "../utils/formatEmploymentType";
import type { Job } from "@/features/jobs/types/job.types";

defineProps<{
    jobs: Job[];
    loading: boolean;
    error: boolean;
}>();

const emit = defineEmits<{
    retry: [];
}>();

// Keep track of logos that fail to load.
const failedLogos = ref<number[]>([]);

const hasLogoFailed = (jobId: number) => {
    return failedLogos.value.includes(jobId);
};

const handleLogoError = (jobId: number) => {
    if (!failedLogos.value.includes(jobId)) {
        failedLogos.value.push(jobId);
    }
};
</script>

<template>
    <div class="p-5">
        <!-- Loading State -->

        <div v-if="loading" class="space-y-3">
            <div
                v-for="item in 3"
                :key="item"
                class="animate-pulse rounded-2xl border border-slate-100 bg-white p-4"
            >
                <div class="flex items-start gap-3">
                    <div
                        class="h-12 w-12 shrink-0 rounded-xl bg-slate-100"
                    ></div>

                    <div class="min-w-0 flex-1 space-y-3">
                        <div class="h-4 w-3/4 rounded bg-slate-100"></div>

                        <div class="h-3 w-1/2 rounded bg-slate-100"></div>

                        <div class="h-3 w-2/3 rounded bg-slate-100"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->

        <div
            v-else-if="error"
            class="rounded-2xl border border-red-100 bg-red-50/70 px-4 py-7 text-center"
        >
            <div
                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-red-500 shadow-sm"
            >
                <BriefcaseBusiness class="h-6 w-6" />
            </div>

            <p class="mt-3 text-sm font-semibold text-red-700">
                Unable to load job opportunities.
            </p>

            <p class="mt-1 text-xs text-red-500">Please try again.</p>

            <button
                type="button"
                @click="emit('retry')"
                class="mt-4 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-red-600 shadow-sm transition hover:bg-red-100"
            >
                Try Again
            </button>
        </div>

        <!-- Empty State -->

        <div
            v-else-if="jobs.length === 0"
            class="rounded-2xl border border-slate-100 bg-slate-50/70 px-4 py-8 text-center"
        >
            <div
                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm"
            >
                <BriefcaseBusiness class="h-7 w-7" />
            </div>

            <h4 class="mt-4 text-sm font-bold text-slate-800">
                No Jobs Available
            </h4>

            <p class="mx-auto mt-2 max-w-xs text-xs leading-5 text-slate-500">
                No published jobs are available right now. Please check again
                later.
            </p>
        </div>

        <!-- Job List -->

        <div v-else class="space-y-3">
            <RouterLink
                v-for="job in jobs"
                :key="job.id"
                :to="`/jobs/${job.id}`"
                class="group block rounded-2xl border border-slate-200 bg-white p-4 transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50/30 hover:shadow-md"
            >
                <!-- Top Section -->

                <div class="flex items-start gap-3">
                    <!-- Company Logo -->

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm transition group-hover:border-blue-200"
                    >
                        <img
                            v-if="job.company.logo && !hasLogoFailed(job.id)"
                            :src="getCompanyLogoUrl(job.company.logo)"
                            :alt="`${job.company.name} logo`"
                            class="h-full w-full rounded-full object-cover"
                            @error="handleLogoError(job.id)"
                        />

                        <Building2 v-else class="h-5 w-5 text-slate-400" />
                    </div>

                    <!-- Job Information -->

                    <div class="min-w-0 flex-1">
                        <h4
                            class="line-clamp-2 wrap-break-word text-sm font-bold leading-5 text-slate-800 transition group-hover:text-blue-700"
                        >
                            {{ job.title }}
                        </h4>

                        <p
                            class="mt-1 truncate text-xs font-medium text-slate-500"
                        >
                            {{ job.company.name }}
                        </p>
                    </div>
                </div>

                <!-- Job Metadata -->

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <!-- Location -->

                    <span
                        v-if="job.city || job.country"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs text-slate-600"
                    >
                        <MapPin class="h-3.5 w-3.5 shrink-0 text-slate-400" />

                        <span class="truncate">
                            {{
                                [job.city, job.country]
                                    .filter(Boolean)
                                    .join(", ")
                            }}
                        </span>
                    </span>

                    <!-- Employment Type -->

                    <span
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700"
                    >
                        <BriefcaseBusiness class="h-3.5 w-3.5" />

                        {{ formatEmploymentType(job.employment_type) }}
                    </span>
                </div>

                <!-- Bottom Action -->

                <div
                    class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3"
                >
                    <span
                        class="text-xs font-medium text-slate-400 transition group-hover:text-blue-600"
                    >
                        Explore this opportunity
                    </span>

                    <span
                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600"
                    >
                        View Job

                        <ArrowUpRight
                            class="h-3.5 w-3.5 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                        />
                    </span>
                </div>
            </RouterLink>
        </div>

        <!-- Browse All Jobs -->

        <RouterLink
            to="/jobseeker/jobs"
            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#102D59] px-4 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-blue-800 hover:shadow-md"
        >
            Browse All Jobs

            <ArrowUpRight
                class="h-4 w-4 transition-transform hover:translate-x-0.5"
            />
        </RouterLink>
    </div>
</template>
