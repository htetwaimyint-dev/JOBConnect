<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";

import {
  BriefcaseBusiness,
  MapPin,
  CalendarDays,
  Pencil,
  LockKeyhole,
  Trash2,
  Eye,
  X,
  RefreshCw,
  ChevronLeft,
  ChevronRight,
  AlertCircle,
  Plus,
  ListFilter,
} from "lucide-vue-next";

import type { Job, JobListParams, JobStatus } from "../types/job.types";
import { useJobs } from "../composables/useJobs";
import { useJobCategories } from "../composables/useJobCategories";
import JobRejectionModal from "../components/JobRejectionModal.vue";

// Router & Rejection Modal
const router = useRouter();
const selectedRejectedJob = ref<Job | null>(null);

function handleViewJob(job: Job) {
  if (job.status === "rejected") {
    selectedRejectedJob.value = job;
    return;
  }

  router.push(`/employer/jobs/${job.id}`);
}

function closeRejectionModal() {
  selectedRejectedJob.value = null;
}

function handleEditRejectedJob() {
  if (!selectedRejectedJob.value) return;

  const jobId = selectedRejectedJob.value.id;
  selectedRejectedJob.value = null;

  router.push(`/employer/jobs/${jobId}/edit`);
}

// Filters & Pagination
const currentPage = ref(1);
const selectedStatus = ref<JobStatus | "all">("all");
const selectedCategoryId = ref<number | "all">("all");

const { data: categories, isPending: isCategoriesPending } = useJobCategories();

const filters = computed<JobListParams>(() => ({
  page: currentPage.value,
  ...(selectedStatus.value !== "all" ? { status: selectedStatus.value } : {}),
  ...(selectedCategoryId.value !== "all"
    ? { category_id: selectedCategoryId.value }
    : {}),
}));

watch([selectedStatus, selectedCategoryId], () => {
  currentPage.value = 1;
});

// Fetch Employer Jobs
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

const hasActiveFilters = computed(
  () => selectedStatus.value !== "all" || selectedCategoryId.value !== "all",
);

function clearFilters() {
  selectedStatus.value = "all";
  selectedCategoryId.value = "all";
}

// Close Job
function handleCloseJob(jobId: number, title: string) {
  const confirmed = window.confirm(
    `Are you sure you want to close "${title}"?`,
  );

  if (confirmed) {
    closeJob.mutate(jobId);
  }
}

// Delete Job
function handleDeleteJob(jobId: number, title: string) {
  const confirmed = window.confirm(
    `Are you sure you want to delete "${title}"? This action cannot be undone.`,
  );

  if (confirmed) {
    deleteJob.mutate(jobId);
  }
}

// Pagination
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

const visiblePages = computed(() => {
  const pages: number[] = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(lastPage.value, currentPage.value + 2);

  for (let page = start; page <= end; page++) {
    pages.push(page);
  }

  return pages;
});

const firstVisiblePage = computed(() => visiblePages.value[0] ?? 1);

const lastVisiblePage = computed(
  () => visiblePages.value[visiblePages.value.length - 1] ?? 1,
);

watch(lastPage, (newLastPage) => {
  if (currentPage.value > newLastPage) {
    currentPage.value = Math.max(1, newLastPage);
  }
});

// Status Helpers
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
    draft: "bg-slate-100 text-slate-700",
    pending: "bg-amber-50 text-amber-700",
    published: "bg-emerald-50 text-emerald-700",
    rejected: "bg-rose-50 text-rose-700",
    closed: "bg-slate-100 text-slate-600",
  };

  return classes[status];
}

function getStatusDotClass(status: JobStatus): string {
  const classes: Record<JobStatus, string> = {
    draft: "bg-slate-400",
    pending: "bg-amber-500",
    published: "bg-emerald-500",
    rejected: "bg-rose-500",
    closed: "bg-slate-400",
  };

  return classes[status];
}

// Date Formatter
function formatDate(date: string): string {
  if (!date) return "-";

  const parsedDate = new Date(date);

  if (Number.isNaN(parsedDate.getTime())) return "-";

  return parsedDate.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}
</script>

<template>
  <div class="w-full min-w-0 space-y-6">
    <!-- Page Header -->
    <section
      class="overflow-hidden rounded-2xl bg-[#102D59] px-5 py-7 text-white shadow-sm sm:px-8 sm:py-8"
    >
      <div
        class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="min-w-0">
          <p
            class="text-xs font-semibold uppercase tracking-[0.15em] text-blue-200"
          >
            Employer Dashboard
          </p>

          <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
            My Job Posts
          </h1>

          <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100">
            Manage your job postings, track their status and connect with
            potential candidates.
          </p>
        </div>

        <RouterLink
          to="/employer/jobs/create"
          class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#102D59] shadow-sm transition hover:bg-blue-50"
        >
          <Plus class="h-5 w-5" />
          Create New Job
        </RouterLink>
      </div>
    </section>

    <!-- Summary Cards -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <!-- Total Jobs -->
      <div
        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
      >
        <div>
          <p class="text-sm font-medium text-slate-500">Matching Job Posts</p>

          <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
            {{ totalJobs }}
          </p>
        </div>

        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
        >
          <BriefcaseBusiness class="h-6 w-6" />
        </div>
      </div>

      <!-- Current Page Jobs -->
      <div
        class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
      >
        <div>
          <p class="text-sm font-medium text-slate-500">Jobs on This Page</p>

          <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
            {{ jobs.length }}
          </p>
        </div>

        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600"
        >
          <ListFilter class="h-6 w-6" />
        </div>
      </div>
    </section>

    <!-- Filter Section -->
    <section
      class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
    >
      <div
        class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between"
      >
        <div>
          <h2 class="text-lg font-bold text-slate-900">Your Job Listings</h2>

          <p class="mt-1 text-sm text-slate-500">
            Filter and manage all your job postings.
          </p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:flex xl:flex-wrap">
          <!-- Category Filter -->
          <select
            v-model="selectedCategoryId"
            :disabled="isCategoriesPending"
            aria-label="Filter by category"
            class="min-w-0 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-50"
          >
            <option value="all">All Categories</option>

            <option
              v-for="category in categories ?? []"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>

          <!-- Status Filter -->
          <select
            v-model="selectedStatus"
            aria-label="Filter by status"
            class="min-w-0 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
            <option value="all">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="published">Published</option>
            <option value="rejected">Rejected</option>
            <option value="closed">Closed</option>
          </select>

          <!-- Clear Filters -->
          <button
            type="button"
            :disabled="!hasActiveFilters"
            @click="clearFilters"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 disabled:cursor-not-allowed disabled:opacity-40"
          >
            <X class="h-4 w-4" />
            Clear
          </button>

          <!-- Refresh -->
          <button
            type="button"
            :disabled="isFetching"
            @click="refetch()"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
          >
            <RefreshCw
              class="h-4 w-4"
              :class="{ 'animate-spin': isFetching }"
            />
            Refresh
          </button>
        </div>
      </div>

      <div
        v-if="hasActiveFilters"
        class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4"
      >
        <span class="text-xs font-medium text-slate-500">
          Active filters:
        </span>

        <span
          v-if="selectedCategoryId !== 'all'"
          class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
        >
          Category:
          {{
            categories?.find((category) => category.id === selectedCategoryId)
              ?.name ?? "Selected"
          }}
        </span>

        <span
          v-if="selectedStatus !== 'all'"
          class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold capitalize text-blue-700"
        >
          Status: {{ selectedStatus }}
        </span>
      </div>
    </section>

    <!-- Loading State -->
    <section
      v-if="isPending"
      class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm"
    >
      <div
        class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-blue-100 border-t-blue-600"
      ></div>

      <p class="mt-4 text-sm font-semibold text-slate-700">
        Loading your job posts...
      </p>

      <p class="mt-1 text-xs text-slate-500">Please wait a moment.</p>
    </section>

    <!-- Error State -->
    <section
      v-else-if="isError"
      class="rounded-2xl border border-rose-200 bg-white px-6 py-14 text-center shadow-sm"
    >
      <div
        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-600"
      >
        <AlertCircle class="h-7 w-7" />
      </div>

      <h3 class="mt-4 text-lg font-bold text-slate-900">
        Unable to Load Job Posts
      </h3>

      <p class="mt-2 text-sm text-slate-500">
        Something went wrong while loading your jobs.
      </p>

      <button
        type="button"
        @click="refetch()"
        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
      >
        <RefreshCw class="h-4 w-4" />
        Try Again
      </button>
    </section>

    <!-- Empty State -->
    <section
      v-else-if="jobs.length === 0"
      class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
    >
      <div
        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
      >
        <BriefcaseBusiness class="h-8 w-8" />
      </div>

      <h3 class="mt-5 text-xl font-bold text-slate-900">No Job Posts Found</h3>

      <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
        {{
          hasActiveFilters
            ? "No jobs match your selected filters. Try changing or clearing your filters."
            : "You haven't created any job posts yet. Create your first job post to get started."
        }}
      </p>

      <div class="mt-6 flex flex-wrap justify-center gap-3">
        <button
          v-if="hasActiveFilters"
          type="button"
          @click="clearFilters"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
        >
          <X class="h-4 w-4" />
          Clear Filters
        </button>

        <RouterLink
          to="/employer/jobs/create"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
          <Plus class="h-4 w-4" />
          Create New Job
        </RouterLink>
      </div>
    </section>

    <!-- Job Cards -->
    <section v-else class="space-y-4">
      <article
        v-for="job in jobs"
        :key="job.id"
        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md sm:p-6"
      >
        <div
          class="flex min-w-0 flex-col gap-5 xl:flex-row xl:items-center xl:justify-between"
        >
          <!-- Job Information -->
          <div class="flex min-w-0 flex-1 items-start gap-4">
            <!-- Job Icon -->
            <div
              class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 sm:h-14 sm:w-14"
            >
              <BriefcaseBusiness class="h-6 w-6 sm:h-7 sm:w-7" />
            </div>

            <!-- Job Details -->
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2.5">
                <h3
                  class="min-w-0 break-words text-base font-bold text-slate-900 sm:text-lg"
                >
                  {{ job.title }}
                </h3>

                <!-- Status -->
                <span
                  :class="getStatusClass(job.status)"
                  class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                >
                  <span
                    :class="getStatusDotClass(job.status)"
                    class="h-1.5 w-1.5 rounded-full"
                  ></span>

                  {{ getStatusLabel(job.status) }}
                </span>
              </div>

              <p class="mt-1.5 text-sm text-slate-500">
                {{ job.category?.name || "Uncategorized" }}
                <span class="mx-1 text-slate-300">·</span>
                <span class="font-medium text-slate-600">
                  Job ID #{{ job.id }}
                </span>
              </p>

              <!-- Metadata -->
              <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2.5">
                <!-- Location -->
                <div class="flex items-center gap-1.5 text-sm text-slate-500">
                  <MapPin class="h-4 w-4 shrink-0 text-slate-400" />

                  <span>
                    {{
                      [job.city, job.country].filter(Boolean).join(", ") ||
                      "No location"
                    }}
                  </span>
                </div>

                <!-- Employment Type -->
                <div class="flex items-center gap-1.5 text-sm text-slate-500">
                  <BriefcaseBusiness class="h-4 w-4 shrink-0 text-slate-400" />

                  <span class="capitalize">
                    {{
                      job.employment_type?.replace(/_/g, " ") || "Not specified"
                    }}
                  </span>
                </div>

                <!-- Created Date -->
                <div class="flex items-center gap-1.5 text-sm text-slate-500">
                  <CalendarDays class="h-4 w-4 shrink-0 text-slate-400" />

                  <span>{{ formatDate(job.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div
            class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4 xl:shrink-0 xl:justify-end xl:border-l xl:border-t-0 xl:pl-5 xl:pt-0"
          >
            <!-- Close Job -->
            <button
              v-if="job.status === 'published'"
              type="button"
              :disabled="isClosing"
              @click="handleCloseJob(job.id, job.title)"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-50 px-3.5 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <X class="h-4 w-4" />
              {{ isClosing ? "Closing..." : "Close" }}
            </button>

            <!-- Edit -->
            <RouterLink
              v-if="job.status !== 'published' && job.status !== 'closed'"
              :to="`/employer/jobs/${job.id}/edit`"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
              <Pencil class="h-4 w-4" />
              Edit
            </RouterLink>

            <!-- Locked Indicator -->
            <span
              v-else
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-3.5 py-2.5 text-sm font-medium text-slate-400"
              :title="
                job.status === 'published'
                  ? 'Published jobs cannot be edited'
                  : 'Closed jobs cannot be edited'
              "
            >
              <LockKeyhole class="h-4 w-4" />
              Locked
            </span>

            <!-- Delete -->
            <button
              v-if="job.status !== 'published'"
              type="button"
              :disabled="isDeleting"
              @click="handleDeleteJob(job.id, job.title)"
              class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3.5 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <Trash2 class="h-4 w-4" />
              {{ isDeleting ? "Deleting..." : "Delete" }}
            </button>

            <!-- View -->
            <button
              type="button"
              @click="handleViewJob(job)"
              class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
              <Eye class="h-4 w-4" />
              View
            </button>
          </div>
        </div>
      </article>
    </section>

    <!-- Pagination -->
    <section
      v-if="!isPending && !isError && lastPage > 1"
      class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
    >
      <p class="text-sm text-slate-500">
        Page
        <span class="font-semibold text-slate-900">
          {{ currentPage }}
        </span>
        of
        <span class="font-semibold text-slate-900">
          {{ lastPage }}
        </span>
      </p>

      <div class="flex flex-wrap items-center gap-1.5">
        <!-- Previous -->
        <button
          type="button"
          :disabled="currentPage <= 1 || isFetching"
          @click="goToPreviousPage"
          aria-label="Previous page"
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        >
          <ChevronLeft class="h-4 w-4" />
        </button>

        <!-- First Page -->
        <button
          v-if="firstVisiblePage > 1"
          type="button"
          :disabled="isFetching"
          @click="goToPage(1)"
          class="h-9 min-w-9 rounded-lg border border-slate-200 px-3 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-40"
        >
          1
        </button>

        <!-- Left Ellipsis -->
        <span v-if="firstVisiblePage > 2" class="px-1 text-sm text-slate-400">
          ...
        </span>

        <!-- Page Numbers -->
        <button
          v-for="page in visiblePages"
          :key="page"
          type="button"
          :disabled="isFetching"
          @click="goToPage(page)"
          :class="[
            'h-9 min-w-9 rounded-lg border px-3 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50',
            currentPage === page
              ? 'border-blue-600 bg-blue-600 text-white'
              : 'border-slate-200 text-slate-600 hover:bg-slate-50',
          ]"
          :aria-current="currentPage === page ? 'page' : undefined"
        >
          {{ page }}
        </button>

        <!-- Right Ellipsis -->
        <span
          v-if="lastVisiblePage < lastPage - 1"
          class="px-1 text-sm text-slate-400"
        >
          ...
        </span>

        <!-- Last Page -->
        <button
          v-if="lastVisiblePage < lastPage"
          type="button"
          :disabled="isFetching"
          @click="goToPage(lastPage)"
          class="h-9 min-w-9 rounded-lg border border-slate-200 px-3 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-40"
        >
          {{ lastPage }}
        </button>

        <!-- Next -->
        <button
          type="button"
          :disabled="currentPage >= lastPage || isFetching"
          @click="goToNextPage"
          aria-label="Next page"
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        >
          <ChevronRight class="h-4 w-4" />
        </button>
      </div>
    </section>
  </div>

  <!-- Job Rejection Modal -->
  <JobRejectionModal
    v-if="selectedRejectedJob"
    :reason="selectedRejectedJob.rejection_reason ?? ''"
    @close="closeRejectionModal"
    @edit="handleEditRejectedJob"
  />
</template>
