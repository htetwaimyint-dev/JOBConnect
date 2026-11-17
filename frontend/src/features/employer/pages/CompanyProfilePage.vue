<script setup lang="ts">
import CompanyBanner from "../components/CompanyBanner.vue";
import CompanyOverview from "../components/CompanyOverview.vue";
import CompanyJobs from "../components/CompanyJobs.vue";
import CompanyGallery from "../components/CompanyGallery.vue";
import CompanySidebar from "../components/CompanySidebar.vue";

import type { Job, JobListParams } from "@/features/jobs/types/job.types.ts";
import { useCompanyProfile } from "@/features/employer/composables/useUpdateCompanyProfile.ts";
import { useJobs } from "@/features/jobs/composables/useJobs.ts";
import { computed } from "vue";

const {
    data: company,
    isPending,
    isError,
    error,
    refetch,
} = useCompanyProfile();

const params = computed<JobListParams>(() => ({
    page: 3,
}));

const { getJobsByCompany } = useJobs();
const jobsQuery = getJobsByCompany(params);

const jobs = computed(() => jobsQuery.data.value?.data ?? []);

function goToJobs() {}
function selectJob(job: Job) {}
</script>

<template>
    <main class="min-h-screen bg-[#f7f7fb] pb-16">
        <!-- Loading -->
        <div v-if="isPending" class="max-w-7xl mx-auto px-4 py-16 text-center">
            <p class="text-sm text-gray-500">Loading company profile...</p>
        </div>

        <!-- Error -->
        <div
            v-else-if="isError"
            class="max-w-7xl mx-auto px-4 py-16 text-center"
        >
            <p class="font-semibold text-red-600">
                {{ error?.message || "Unable to load company profile." }}
            </p>
            <button
                type="button"
                @click="refetch()"
                class="mt-4 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition"
            >
                Try Again
            </button>
        </div>

        <!-- Company Profile Layout -->
        <template v-else-if="company">
            <!-- Banner & Header Card -->
            <CompanyBanner
                :company="company"
                :is-following="false"
                @follow="() => {}"
                @view-jobs="goToJobs"
            />

            <!-- Two-Column Body Layout -->
            <div class="mx-auto max-w-290 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-8 space-y-8">
                        <CompanyOverview :company="company" />
                        <CompanyJobs
                            :jobs="jobs"
                            :company="company"
                            @select-job="selectJob"
                        />
                        <CompanyGallery
                            :existing-images="company.gallery ?? []"
                            :company-name="company.name"
                        />
                    </div>

                    <!-- Right Column (People at, People Also View) -->
                    <div class="lg:col-span-4">
                        <div class="sticky top-6">
                            <CompanySidebar :company="company" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </main>
</template>
