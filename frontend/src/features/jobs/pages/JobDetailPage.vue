<script setup lang="ts">
import { computed } from "vue";
import { useRoute, RouterLink } from "vue-router";

import { useJobs } from "../composables/useJobs";

const route = useRoute();

const jobId = computed(() => Number(route.params.id));

const { getJobById } = useJobs();

const jobQuery = getJobById(jobId.value);

const job = computed(() => jobQuery.data.value?.data);

const formatSalary = () => {
    if (!job.value) return "";

    if (job.value.salary_type === "negotiable") {
        return "Negotiable";
    }

    if (
        job.value.salary_min === null &&
        job.value.salary_max === null
    ) {
        return "Not specified";
    }

    const min = job.value.salary_min?.toLocaleString() ?? "";
    const max = job.value.salary_max?.toLocaleString() ?? "";

    if (min && max) return `${min} - ${max}`;
    return min || max;
};

const formatDate = (date: string | null) => {
    if (!date) return "Not specified";

    return new Date(date).toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });
};
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

        <!-- Back -->
        <RouterLink to="/" class="text-sm font-medium text-blue-600 hover:text-blue-700">
            ← Back to Jobs
        </RouterLink>

        <!-- Loading -->
        <div v-if="jobQuery.isLoading.value" class="py-20 text-center text-slate-500">
            Loading job details...
        </div>

        <!-- Error -->
        <div v-else-if="jobQuery.isError.value" class="mt-8 rounded-xl bg-red-50 p-6 text-center text-red-600">
            Failed to load job details. Please try again.
        </div>

        <!-- Job Not Found -->
        <div v-else-if="!job" class="mt-8 rounded-xl border border-slate-200 bg-white p-10 text-center">
            <h2 class="text-xl font-semibold text-slate-800">
                Job not found
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                This job may no longer be available.
            </p>
        </div>

        <!-- Job Details -->
        <div v-else class="mt-6 space-y-6">

            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <div class="flex items-start gap-4">

                    <img v-if="job.company.logo" :src="job.company.logo" :alt="job.company.name"
                        class="h-16 w-16 rounded-xl border border-slate-100 object-cover" />

                    <div v-else
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-2xl font-bold text-blue-600">
                        {{ job.company.name.charAt(0).toUpperCase() }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">
                            {{ job.title }}
                        </h1>

                        <p class="mt-2 text-slate-600">
                            {{ job.company.name }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ [job.city, job.country].filter(Boolean).join(", ") || "Location not specified" }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700">
                        {{ job.employment_type.replace("_", " ") }}
                    </span>

                    <span class="rounded-full bg-purple-50 px-3 py-1.5 text-sm font-medium text-purple-700">
                        {{ job.workplace_type }}
                    </span>

                    <span v-if="job.category"
                        class="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                        {{ job.category.name }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 border-t border-slate-100 pt-6 sm:grid-cols-3">
                    <div>
                        <p class="text-sm text-slate-500">Salary</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ formatSalary() }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Vacancies</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ job.vacancies }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Application Deadline</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ formatDate(job.deadline) }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-xl font-bold text-slate-900">
                    Job Description
                </h2>

                <p class="mt-4 whitespace-pre-line leading-7 text-slate-600">
                    {{ job.description }}
                </p>
            </section>

            <!-- Requirements -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-xl font-bold text-slate-900">
                    Requirements
                </h2>

                <p class="mt-4 whitespace-pre-line leading-7 text-slate-600">
                    {{ job.requirements }}
                </p>
            </section>

            <!-- Responsibilities -->
            <section v-if="job.responsibilities" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-xl font-bold text-slate-900">
                    Responsibilities
                </h2>

                <p class="mt-4 whitespace-pre-line leading-7 text-slate-600">
                    {{ job.responsibilities }}
                </p>
            </section>

        </div>
    </div>
</template>
