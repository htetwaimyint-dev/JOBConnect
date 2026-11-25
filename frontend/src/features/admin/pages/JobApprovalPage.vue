<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";

import { Search, AlertCircle, X, LoaderCircle } from "lucide-vue-next";

import jobApprovalRepository from "../repositories/jobApprovalRepository";
import JobApprovalTable from "../components/jobs/JobApprovalTable.vue";
import JobApprovalModal from "../components/jobs/JobApprovalModal.vue";
import JobApprovalDetailModal from "../components/jobs/JobApprovalDetailModal.vue";

import BasePagination from "@/components/common/BasePagination.vue";
import BaseFilterActions from "@/components/common/BaseFilterActions.vue";

import type { Job } from "../types/job-approval.types";

type ApprovalAction = "published" | "rejected";
type ApprovalTab = "all" | "pending" | "published" | "rejected";

const jobs = ref<Job[]>([]);

const loading = ref(false);
const submitting = ref(false);

const searchQuery = ref("");

const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(5);

const activeFilter = ref<ApprovalTab>("all");

const errorMessage = ref("");

// Request control
let requestId = 0;

// Job Details Modal
const selectedJob = ref<Job | null>(null);
const showDetailsModal = ref(false);

// Approval Modal
const approvalJob = ref<Job | null>(null);
const approvalAction = ref<ApprovalAction | null>(null);
const rejectionReason = ref("");

// Filters
const filters: {
  label: string;
  value: ApprovalTab;
}[] = [
  { label: "All Jobs", value: "all" },
  { label: "Pending", value: "pending" },
  { label: "Approved", value: "published" },
  { label: "Rejected", value: "rejected" },
];

// Status Helpers
const statusLabel = (status: string | null | undefined) => {
  const labels: Record<string, string> = {
    pending: "Pending",
    published: "Approved",
    rejected: "Rejected",
    draft: "Draft",
    closed: "Closed",
  };

  return labels[status ?? ""] ?? status ?? "Unknown";
};

const statusClass = (status: string | null | undefined) => {
  const classes: Record<string, string> = {
    pending: "bg-amber-50 text-amber-700 ring-amber-200",
    published: "bg-emerald-50 text-emerald-700 ring-emerald-200",
    rejected: "bg-red-50 text-red-700 ring-red-200",
    draft: "bg-slate-100 text-slate-600 ring-slate-200",
    closed: "bg-gray-100 text-gray-600 ring-gray-200",
  };

  return classes[status ?? ""] ?? "bg-slate-100 text-slate-600 ring-slate-200";
};

// Fetch Jobs
const fetchJobs = async (page = 1) => {
  const currentRequestId = ++requestId;

  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await jobApprovalRepository.getJobsByStatus(
      activeFilter.value,
      {
        search: searchQuery.value.trim(),
        page,
        per_page: perPage.value,
      },
    );

    // Ignore outdated responses
    if (currentRequestId !== requestId) return;

    jobs.value = response.data;
    currentPage.value = response.meta.current_page;
    lastPage.value = response.meta.last_page;
    total.value = response.meta.total;
  } catch (error: any) {
    if (currentRequestId !== requestId) return;

    console.error("Failed to fetch jobs:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Failed to load jobs.";
  } finally {
    if (currentRequestId === requestId) {
      loading.value = false;
    }
  }
};

// Search - Click Only
const applySearch = () => {
  if (loading.value) return;

  currentPage.value = 1;
  fetchJobs(1);
};

// Clear Search
const clearSearch = () => {
  if (loading.value) return;

  searchQuery.value = "";
  currentPage.value = 1;

  fetchJobs(1);
};

// Filter
const changeFilter = (status: ApprovalTab) => {
  if (activeFilter.value === status || loading.value) return;

  activeFilter.value = status;
  searchQuery.value = "";
  currentPage.value = 1;

  fetchJobs(1);
};

// Pagination
const goToPage = (page: number) => {
  if (page < 1 || page > lastPage.value || loading.value) return;

  fetchJobs(page);
};

const changePerPage = (value: number) => {
  if (loading.value) return;

  perPage.value = value;
  currentPage.value = 1;

  fetchJobs(1);
};

// Job Details
const openDetails = async (job: Job) => {
  errorMessage.value = "";

  try {
    const response = await jobApprovalRepository.getJobById(job.id);

    selectedJob.value = response.data;
    showDetailsModal.value = true;
  } catch (error: any) {
    console.error("Failed to load job details:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Unable to load job details.";
  }
};

const closeDetails = () => {
  showDetailsModal.value = false;
  selectedJob.value = null;
};

// Approval Dialog
const openApprovalDialog = (job: Job, action: ApprovalAction) => {
  errorMessage.value = "";
  approvalJob.value = job;
  approvalAction.value = action;
  rejectionReason.value = "";
};

// Close Approval Dialog
const closeApprovalDialog = () => {
  if (submitting.value) return;

  approvalJob.value = null;
  approvalAction.value = null;
  rejectionReason.value = "";
};

// Confirm Approval / Rejection
const confirmApproval = async () => {
  if (!approvalJob.value || !approvalAction.value) return;

  if (approvalAction.value === "rejected" && !rejectionReason.value.trim()) {
    errorMessage.value = "Please provide a rejection reason.";
    return;
  }

  submitting.value = true;
  errorMessage.value = "";

  try {
    const payload =
      approvalAction.value === "rejected"
        ? {
            status: "rejected" as const,
            rejection_reason: rejectionReason.value.trim(),
          }
        : {
            status: "published" as const,
          };

    await jobApprovalRepository.updateJobApproval(
      approvalJob.value.id,
      payload,
    );

    approvalJob.value = null;
    approvalAction.value = null;
    rejectionReason.value = "";

    selectedJob.value = null;
    showDetailsModal.value = false;

    // Move to previous page if current page only had one record
    const nextPage =
      jobs.value.length === 1 && currentPage.value > 1
        ? currentPage.value - 1
        : currentPage.value;

    await fetchJobs(nextPage);
  } catch (error: any) {
    console.error("Failed to update job:", error);

    errorMessage.value =
      error.response?.data?.message ??
      "Unable to update job status. Please try again.";
  } finally {
    submitting.value = false;
  }
};

// Initial Load
onMounted(() => {
  fetchJobs(1);
});

// Cleanup
onUnmounted(() => {
  requestId++;
});
</script>

<template>
  <div class="w-full min-w-0 space-y-5 sm:space-y-6">
    <!-- Page Header -->
    <div class="min-w-0">
      <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Job Approval</h1>

      <p class="mt-1 text-xs text-slate-500 sm:text-sm">
        Manage and review job listings submitted by employers.
      </p>
    </div>

    <!-- Error Message -->
    <div
      v-if="errorMessage"
      class="flex min-w-0 items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700 sm:p-4 sm:text-sm"
    >
      <AlertCircle :size="18" class="mt-0.5 shrink-0" />

      <p class="min-w-0 flex-1 break-words">
        {{ errorMessage }}
      </p>

      <button
        type="button"
        @click="errorMessage = ''"
        class="shrink-0 text-red-500 transition hover:text-red-700"
      >
        <X :size="18" />
      </button>
    </div>

    <!-- Job Approval Table Container -->
    <div
      class="w-full min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
      <div class="space-y-5 p-4 sm:p-5">
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2">
          <button
            v-for="filter in filters"
            :key="filter.value"
            type="button"
            @click="changeFilter(filter.value)"
            :disabled="loading"
            class="rounded-lg border px-3 py-2 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50 sm:rounded-xl sm:px-4 sm:py-2.5 sm:text-sm"
            :class="
              activeFilter === filter.value
                ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
            "
          >
            {{ filter.label }}
          </button>
        </div>

        <!-- Search Input + Search / Clear Buttons -->
        <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center">
          <!-- Search Input -->
          <div class="relative w-full min-w-0 sm:max-w-md sm:flex-1">
            <Search
              :size="18"
              class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
            />

            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search job, company, category..."
              @keyup.enter="applySearch"
              class="w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- Common Search / Clear Buttons -->
          <div class="flex shrink-0 items-center">
            <BaseFilterActions
              :loading="loading"
              @search="applySearch"
              @clear="clearSearch"
            />
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="flex items-center justify-center gap-2 py-16 text-sm text-slate-500"
      >
        <LoaderCircle :size="20" class="animate-spin" />
        Loading jobs...
      </div>

      <!-- Empty State -->
      <div v-else-if="jobs.length === 0" class="px-4 py-16 text-center">
        <div
          class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"
        >
          <Search :size="22" />
        </div>

        <p class="font-medium text-slate-700">No jobs found</p>

        <p class="mt-1 text-sm text-slate-500">
          {{
            searchQuery
              ? "No jobs match your search."
              : "There are no job listings at the moment."
          }}
        </p>
      </div>

      <!-- Jobs Table Component -->
      <div v-else class="w-full min-w-0">
        <JobApprovalTable
          :jobs="jobs"
          @view="openDetails"
          @approve="openApprovalDialog($event, 'published')"
          @reject="openApprovalDialog($event, 'rejected')"
        />
      </div>

      <!-- Pagination -->
      <BasePagination
        v-if="total > 0"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        :per-page="perPage"
        :loading="loading"
        @change="goToPage"
        @update:per-page="changePerPage"
      />
    </div>

    <!-- Job Approval Detail Modal -->
    <JobApprovalDetailModal
      :job="showDetailsModal ? selectedJob : null"
      :status-class="statusClass"
      :status-label="statusLabel"
      @close="closeDetails"
      @approve="openApprovalDialog($event, 'published')"
      @reject="openApprovalDialog($event, 'rejected')"
    />

    <!-- Approval Modal -->
    <JobApprovalModal
      :job="approvalJob"
      :action="approvalAction"
      :rejection-reason="rejectionReason"
      :submitting="submitting"
      @close="closeApprovalDialog"
      @confirm="confirmApproval"
      @update:rejection-reason="rejectionReason = $event"
    />
  </div>
</template>
