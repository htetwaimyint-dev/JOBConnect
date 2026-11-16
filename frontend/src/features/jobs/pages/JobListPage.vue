<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

import JobSearch from "../components/JobSearch.vue";
import JobCard from "../components/JobCard.vue";

import { useJobs } from "../composables/useJobs";
import type { JobListParams } from "../types/job.types";

const route = useRoute();
const router = useRouter();

// Search values
const search = ref(
    typeof route.query.search === "string"
        ? route.query.search
        : ""
);

const city = ref("");
const country = ref("");
const currentPage = ref(1);

// Category filter from URL
const categoryId = ref(
    typeof route.query.category_id === "string" &&
        Number.isInteger(Number(route.query.category_id)) &&
        Number(route.query.category_id) > 0
        ? Number(route.query.category_id)
        : undefined
);

// API parameters
const params = computed<JobListParams>(() => ({
    page: currentPage.value,
    search: search.value || undefined,
    category_id: categoryId.value,
    city: city.value || undefined,
    country: country.value || undefined,
}));

// Get jobs from API
const { getJobs } = useJobs();

const jobsQuery = getJobs(params);

// Job list
const jobs = computed(
    () => jobsQuery.data.value?.data ?? []
);

// Pagination information
const pagination = computed(
    () => jobsQuery.data.value?.meta
);

// Search handler
const handleSearch = (value: {
    search: string;
    city: string;
    country: string;
}) => {
    search.value = value.search;
    city.value = value.city;
    country.value = value.country;
    currentPage.value = 1;

    router.replace({
        query: {
            ...route.query,
            search: value.search || undefined,
            category_id: categoryId.value
                ? String(categoryId.value)
                : undefined,
        },
    });
};

// Follow search keyword changes from Header
watch(
    () => route.query.search,
    (value) => {
        search.value =
            typeof value === "string" ? value : "";

        currentPage.value = 1;
    }
);

// Follow category changes from URL
watch(
    () => route.query.category_id,
    (value) => {
        const id =
            typeof value === "string"
                ? Number(value)
                : undefined;

        categoryId.value =
            id && Number.isInteger(id) && id > 0
                ? id
                : undefined;

        currentPage.value = 1;
    }
);

// Pagination
const changePage = (page: number) => {
    if (
        page < 1 ||
        page > (pagination.value?.last_page ?? 1)
    ) {
        return;
    }

    currentPage.value = page;
};
</script>

<template>
    <main class="min-h-screen bg-slate-50">

        <!-- Hero Section -->
        <section class="relative isolate overflow-hidden bg-[#102D59]">
            <!-- Background Decoration -->
            <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl"></div>

            <div class="absolute -bottom-40 -left-20 h-80 w-80 rounded-full bg-cyan-400/10 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-4 pb-28 pt-16 sm:px-6 sm:pt-20 lg:px-8 lg:pb-32">
                <div class="max-w-3xl">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-100 backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                        Explore Career Opportunities
                    </span>

                    <h1
                        class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Find a Job That
                        <span class="text-cyan-300">
                            Moves You Forward.
                        </span>
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-blue-100/80 sm:text-lg">
                        Discover meaningful opportunities, explore leading
                        companies, and take the next step in your career
                        with JobConnect.
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="relative mx-auto -mt-16 max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">

            <!-- Search Panel -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xl shadow-slate-900/5 sm:p-6">
                <div class="mb-5">
                    <h2 class="text-lg font-bold text-[#102D59]">
                        Search Jobs
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Find opportunities by keyword and location.
                    </p>
                </div>

                <JobSearch :search="search" :city="city" :country="country" @search="handleSearch" />
            </div>

            <!-- Results Heading -->
            <div class="mb-6 mt-12 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-blue-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        JOB OPPORTUNITIES
                    </div>

                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Available Jobs
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Explore positions that match your career goals.
                    </p>
                </div>

                <div v-if="pagination"
                    class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm">
                    <svg class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>

                    <span class="text-sm font-semibold text-slate-700">
                        {{ pagination.total }}
                        <span class="font-normal text-slate-500">
                            jobs found
                        </span>
                    </span>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="jobsQuery.isLoading.value"
                class="rounded-2xl border border-slate-200 bg-white py-24 text-center shadow-sm">
                <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-blue-100 border-t-blue-600">
                </div>

                <h3 class="mt-5 text-base font-semibold text-slate-800">
                    Finding opportunities...
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Please wait while we load available jobs.
                </p>
            </div>

            <!-- Error State -->
            <div v-else-if="jobsQuery.isError.value"
                class="rounded-2xl border border-red-100 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 8v5m0 3h.01" />
                    </svg>
                </div>

                <h3 class="mt-5 text-lg font-bold text-slate-900">
                    Unable to load jobs
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Something went wrong while loading jobs. Please try again.
                </p>

                <button type="button"
                    class="mt-6 rounded-xl bg-[#102D59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    @click="jobsQuery.refetch()">
                    Try Again
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="jobs.length === 0"
                class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="7" width="18" height="14" rx="2" />
                        <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18m-11 0v2h4v-2" />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-bold text-slate-900">
                    No jobs found
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    We couldn't find any jobs matching your current search.
                    Try different keywords or change your city and country.
                </p>
            </div>

            <!-- Job Cards -->
            <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <JobCard v-for="job in jobs" :key="job.id" :job="job" />
            </div>

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1"
                class="mt-12 flex flex-col items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-sm sm:flex-row">
                <p class="text-sm text-slate-500">
                    Page
                    <span class="font-bold text-slate-800">
                        {{ pagination.current_page }}
                    </span>
                    of
                    <span class="font-bold text-slate-800">
                        {{ pagination.last_page }}
                    </span>
                </p>

                <div class="flex items-center gap-3">
                    <button type="button" :disabled="currentPage <= 1 ||
                        jobsQuery.isFetching.value
                        "
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(currentPage - 1)">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>

                        Previous
                    </button>

                    <button type="button" :disabled="currentPage >= pagination.last_page ||
                        jobsQuery.isFetching.value
                        "
                        class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                        @click="changePage(currentPage + 1)">
                        Next

                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                </div>
            </div>

        </section>
    </main>
</template>
