<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { RouterLink } from "vue-router";

import { useJobs } from "../composables/useJobs";
import type { JobListParams, JobStatus } from "../types/job.types";
import { useJobCategories } from "../composables/useJobCategories";

// ----------------------------------------
// Filters & Pagination
// ----------------------------------------

const currentPage = ref(1);
const selectedStatus = ref<JobStatus | "all">("all");
const selectedCategoryId = ref<number | "all">("all");

// Fetch categories
const {
    data: categories,
    isPending: isCategoriesPending,
} = useJobCategories();

// Build filters
const filters = computed<JobListParams>(() => ({
    page: currentPage.value,

    ...(selectedStatus.value !== "all"
        ? { status: selectedStatus.value }
        : {}),

    ...(selectedCategoryId.value !== "all"
        ? { category_id: selectedCategoryId.value }
        : {}),
}));

// Reset pagination when filters change
watch(
    [selectedStatus, selectedCategoryId],
    () => {
        currentPage.value = 1;
    }
);

// ----------------------------------------
// Fetch Employer Jobs
// ----------------------------------------

const { getJobsByCompany, closeJob, deleteJob } = useJobs();

const isClosing = computed(() => closeJob.isPending.value);
const isDeleting = computed(() => deleteJob.isPending.value);

const {
    data: jobsResponse,
    isPending,
    isFetching,
    isError,
    refetch,
} = getJobsByCompany(filters);

const jobs = computed(() => jobsResponse.value?.data ?? []);

const pagination = computed(() => jobsResponse.value?.meta);

const lastPage = computed(() => pagination.value?.last_page ?? 1);

const totalJobs = computed(() => pagination.value?.total ?? 0);

// ----------------------------------------
// Close Job Action
// ----------------------------------------

function handleCloseJob(jobId: number, title: string) {
    const confirmed = window.confirm(
        `Are you sure you want to close "${title}"? This action cannot be undone.`
    );

    if (!confirmed) return;

    closeJob.mutate(jobId);
}

function handleDeleteJob(jobId: number, title: string) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${title}"? This action cannot be undone.`
    );

    if (!confirmed) return;

    deleteJob.mutate(jobId);
}

// ----------------------------------------
// Pagination Actions
// ----------------------------------------

function goToPreviousPage() {
    if (currentPage.value > 1 && !isFetching.value) {
        currentPage.value--;
    }
}

function goToNextPage() {
    if (currentPage.value < lastPage.value && !isFetching.value) {
        currentPage.value++;
    }
}

function goToPage(page: number) {
    if (
        page >= 1 &&
        page <= lastPage.value &&
        page !== currentPage.value &&
        !isFetching.value
    ) {
        currentPage.value = page;
    }
}

// Display up to 5 page numbers around current page
const visiblePages = computed(() => {
    const pages: number[] = [];

    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(lastPage.value, currentPage.value + 2);

    for (let page = start; page <= end; page++) {
        pages.push(page);
    }

    return pages;
});

// First visible page number
const firstVisiblePage = computed(
    () => visiblePages.value[0] ?? 1
);

// Last visible page number
const lastVisiblePage = computed(
    () => visiblePages.value[visiblePages.value.length - 1] ?? 1
);

// Keep current page valid when total pages decrease
watch(lastPage, (newLastPage) => {
    if (currentPage.value > newLastPage) {
        currentPage.value = Math.max(1, newLastPage);
    }
});

// ----------------------------------------
// Status Helpers
// ----------------------------------------

function getStatusLabel(status: JobStatus): string {
    const labels: Record<JobStatus, string> = {
        draft: "Draft",
        pending: "Pending",
        published: "Published",
        rejected: "Rejected",
        closed: "Closed",
    };

    return labels[status];
}

function getStatusClass(status: JobStatus): string {
    const classes: Record<JobStatus, string> = {
        draft: "bg-slate-100 text-slate-700 ring-slate-200",
        pending: "bg-amber-50 text-amber-700 ring-amber-200",
        published: "bg-emerald-50 text-emerald-700 ring-emerald-200",
        rejected: "bg-rose-50 text-rose-700 ring-rose-200",
        closed: "bg-gray-100 text-gray-600 ring-gray-200",
    };

    return classes[status];
}

function getStatusDotClass(status: JobStatus): string {
    const classes: Record<JobStatus, string> = {
        draft: "bg-slate-400",
        pending: "bg-amber-500",
        published: "bg-emerald-500",
        rejected: "bg-rose-500",
        closed: "bg-gray-400",
    };

    return classes[status];
}

// ----------------------------------------
// Date Formatter
// ----------------------------------------

function formatDate(date: string): string {
    if (!date) return "-";

    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}
</script>
<template>
    <div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-8">

            <!-- Page Header -->
            <div
                class="overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-700 p-6 text-white shadow-lg sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="mb-2 text-sm font-medium text-indigo-100">
                            EMPLOYER DASHBOARD
                        </p>

                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            My Job Posts
                        </h1>

                        <p class="mt-2 max-w-xl text-sm text-indigo-100 sm:text-base">
                            Manage your job postings, track their status and find the right candidates.
                        </p>
                    </div>

                    <RouterLink to="/employer/jobs/create"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-indigo-700 shadow-md transition hover:-translate-y-0.5 hover:bg-indigo-50">
                        <span class="text-lg">＋</span>
                        Create New Job
                    </RouterLink>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- Total Jobs -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Total Job Posts
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900">
                                {{ totalJobs }}
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-2xl">
                            💼
                        </div>
                    </div>
                </div>

                <!-- Jobs on Current Page -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">
                                Jobs on This Page
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900">
                                {{ jobs.length }}
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-2xl">
                            📋
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Toolbar -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Your Job Listings
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Browse and manage all your job posts.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">

                        <!-- Category Filter -->
                        <select v-model="selectedCategoryId" :disabled="isCategoriesPending"
                            class="min-w-48 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            <option value="all">All Categories</option>

                            <option v-for="category in categories ?? []" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>

                        <!-- Status Filter -->
                        <select v-model="selectedStatus"
                            class="min-w-48 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            <option value="all">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="published">Published</option>
                            <option value="rejected">Rejected</option>
                            <option value="closed">Closed</option>
                        </select>

                        <!-- Clear Filters -->
                        <button type="button" :disabled="selectedCategoryId === 'all' && selectedStatus === 'all'"
                            @click="
                                selectedCategoryId = 'all';
                            selectedStatus = 'all';
                            "
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-red-300 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50">
                            <span>✕</span>
                            Clear Filters
                        </button>

                        <!-- Refresh -->
                        <button type="button" :disabled="isFetching" @click="refetch()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 disabled:cursor-not-allowed disabled:opacity-50">
                            <span :class="{ 'animate-spin': isFetching }">↻</span>
                            Refresh
                        </button>

                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isPending" class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-indigo-100 border-t-indigo-600">
                </div>

                <p class="mt-4 font-medium text-slate-700">
                    Loading your job posts...
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Please wait a moment.
                </p>
            </div>

            <!-- Error State -->
            <div v-else-if="isError" class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-12 text-center">
                <div class="text-3xl">⚠️</div>

                <h3 class="mt-3 text-lg font-bold text-rose-800">
                    Unable to Load Job Posts
                </h3>

                <p class="mt-2 text-sm text-rose-600">
                    Something went wrong while loading your jobs.
                </p>

                <button type="button" @click="refetch()"
                    class="mt-5 rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                    Try Again
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="jobs.length === 0"
                class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl">
                    💼
                </div>

                <h3 class="mt-5 text-xl font-bold text-slate-900">
                    No Job Posts Found
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                    You haven't created any jobs yet, or no jobs match your selected filter.
                </p>

                <RouterLink to="/employer/jobs/create"
                    class="mt-6 inline-flex items-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    Create Your First Job
                </RouterLink>
            </div>

            <!-- Horizontal Job Cards -->
            <div v-else class="space-y-4">

                <article v-for="job in jobs" :key="job.id"
                    class="group flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-lg sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <!-- Left: Job Information -->
                    <div class="flex min-w-0 flex-1 items-center gap-4">

                        <!-- Job Icon -->
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-50 to-violet-100 text-indigo-600 transition group-hover:from-indigo-100 group-hover:to-violet-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="7" width="18" height="13" rx="2" />
                                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <path d="M3 12h18" />
                                <path d="M10 12v2h4v-2" />
                            </svg>
                        </div>

                        <!-- Job Title and Details -->
                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-3">

                                <!-- Job Title -->
                                <h3
                                    class="truncate text-base font-bold text-slate-900 transition group-hover:text-indigo-700 sm:text-lg">
                                    {{ job.title }}
                                </h3>

                                <!-- Job Status -->
                                <span :class="getStatusClass(job.status)"
                                    class="inline-flex shrink-0 items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset">
                                    <span :class="getStatusDotClass(job.status)" class="h-2 w-2 rounded-full"></span>

                                    {{ getStatusLabel(job.status) }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ job.category?.name || "Uncategorized" }}

                                <span class="mx-1.5 text-slate-300">•</span>

                                Job ID #{{ job.id }}
                            </p>

                            <!-- Job Metadata -->
                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2">

                                <!-- Location -->
                                <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>

                                    {{ [job.city, job.country].filter(Boolean).join(", ") || "No location" }}
                                </div>

                                <!-- Employment Type -->
                                <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="7" width="18" height="13" rx="2" />
                                        <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>

                                    <span class="capitalize">
                                        {{ job.employment_type?.replace(/_/g, " ") || "Not specified" }}
                                    </span>
                                </div>

                                <!-- Created Date -->
                                <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="17" rx="2" />
                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>

                                    {{ formatDate(job.created_at) }}
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div
                        class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 sm:justify-end sm:border-t-0 sm:pt-0">
                        <div class="flex items-center gap-2">

                            <!-- Close Job Button -->
                            <button v-if="job.status === 'published'" type="button" :disabled="isClosing"
                                @click="handleCloseJob(job.id, job.title)"
                                class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <span>✕</span>
                                {{ isClosing ? "Closing..." : "Close Job" }}
                            </button>

                            <!-- Edit Button -->
                            <RouterLink v-if="job.status !== 'published' && job.status !== 'closed'"
                                :to="`/employer/jobs/${job.id}/edit`"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                </svg>

                                Edit
                            </RouterLink>

                            <!-- Locked Indicator -->
                            <span v-else
                                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-400"
                                title="Published or closed jobs cannot be edited">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="4" y="11" width="16" height="10" rx="2" />
                                    <path d="M8 11V7a4 4 0 1 1 8 0v4" />
                                </svg>
                            </span>

                            <!-- Delete Button -->
                            <button v-if="job.status !== 'published'" type="button" :disabled="isDeleting"
                                @click="handleDeleteJob(job.id, job.title)"
                                class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <span>🗑</span>
                                {{ isDeleting ? "Deleting..." : "Delete" }}
                            </button>


                            <!-- View Button -->
                            <RouterLink :to="`/employer/jobs/${job.id}`"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                View
                            </RouterLink>

                        </div>
                    </div>
                </article>
            </div>


            <!-- Numbered Pagination -->
            <div v-if="!isPending && !isError && lastPage > 1"
                class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <!-- Page Information -->
                <p class="text-sm text-slate-500">
                    Page
                    <span class="font-semibold text-slate-900">{{ currentPage }}</span>
                    of
                    <span class="font-semibold text-slate-900">{{ lastPage }}</span>
                </p>

                <div class="flex flex-wrap items-center gap-2">

                    <!-- Previous -->
                    <button type="button" :disabled="currentPage <= 1 || isFetching" @click="goToPreviousPage"
                        aria-label="Previous page"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                        ‹
                    </button>

                    <!-- First Page -->
                    <button v-if="firstVisiblePage > 1" type="button" :disabled="isFetching" @click="goToPage(1)"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-40">
                        1
                    </button>

                    <!-- Left Ellipsis -->
                    <span v-if="firstVisiblePage > 2" class="px-1 text-slate-400">
                        ...
                    </span>

                    <!-- Page Numbers -->
                    <button v-for="page in visiblePages" :key="page" type="button" :disabled="isFetching"
                        @click="goToPage(page)" :class="[
                            'rounded-xl border px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50',
                            currentPage === page
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-slate-200 text-slate-700 hover:bg-slate-50'
                        ]" :aria-current="currentPage === page ? 'page' : undefined">
                        {{ page }}
                    </button>

                    <!-- Right Ellipsis -->
                    <span v-if="lastVisiblePage < lastPage - 1" class="px-1 text-slate-400">
                        ...
                    </span>

                    <!-- Last Page -->
                    <button v-if="lastVisiblePage < lastPage" type="button" :disabled="isFetching"
                        @click="goToPage(lastPage)"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-40">
                        {{ lastPage }}
                    </button>

                    <!-- Next -->
                    <button type="button" :disabled="currentPage >= lastPage || isFetching" @click="goToNextPage"
                        aria-label="Next page"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                        ›
                    </button>

                </div>
            </div>

            <!-- Footer Note -->
            <div class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50/70 p-4">
                <span class="text-lg">💡</span>

                <p class="text-sm leading-relaxed text-indigo-800">
                    <span class="font-semibold">Tip:</span>
                    Published and closed jobs are locked from editing. You can still view their details.
                </p>
            </div>

        </div>
    </div>
</template>