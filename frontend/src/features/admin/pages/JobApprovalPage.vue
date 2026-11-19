<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from "vue";

import {
  Search,
  RefreshCw,
  AlertCircle,
  X,
  LoaderCircle,
} from "lucide-vue-next";

import jobApprovalRepository from "../repositories/jobApprovalRepository";
import JobApprovalTable from "../components/jobs/JobApprovalTable.vue";
import JobApprovalModal from "../components/jobs/JobApprovalModal.vue";
import JobApprovalDetailModal from "../components/jobs/JobApprovalDetailModal.vue";
import BasePagination from "@/components/common/BasePagination.vue";

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
let searchTimeout: ReturnType<typeof setTimeout>;
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

// Backend Search with Debounce
watch(searchQuery, () => {
  clearTimeout(searchTimeout);

  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchJobs(1);
  }, 500);
});

// Filter
const changeFilter = (status: ApprovalTab) => {
  if (activeFilter.value === status) return;

  activeFilter.value = status;
  searchQuery.value = "";
  currentPage.value = 1;

  clearTimeout(searchTimeout);
  fetchJobs(1);
};

// Pagination
const goToPage = (page: number) => {
  if (page < 1 || page > lastPage.value || loading.value) return;

  fetchJobs(page);
};

const changePerPage = (value: number) => {
  perPage.value = value;
  currentPage.value = 1;
  fetchJobs(1);
};

// Refresh
const refreshJobs = () => {
  if (loading.value) return;

  clearTimeout(searchTimeout);
  fetchJobs(currentPage.value);
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
  clearTimeout(searchTimeout);
  requestId++;
});
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div
      class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="min-w-0">
        <h1 class="text-2xl font-bold text-slate-800">Job Approval</h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage and review job listings submitted by employers.
        </p>
      </div>

      <button
        type="button"
        @click="refreshJobs"
        :disabled="loading"
        class="mr-auto flex items-center justify-center gap-2 self-end rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-50 sm:self-auto"
      >
        <RefreshCw :size="14" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </div>

    <!-- Error Message -->
    <div
      v-if="errorMessage"
      class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
    >
      <AlertCircle :size="18" class="mt-0.5 shrink-0" />

      <p class="flex-1">
        {{ errorMessage }}
      </p>

      <button
        type="button"
        @click="errorMessage = ''"
        class="text-red-500 hover:text-red-700"
      >
        <X :size="18" />
      </button>
    </div>

    <!-- Job Approval Table -->
    <div
      class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
      <!-- Filters, Search, Table and Pagination -->
      <div class="space-y-5 p-5">
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2">
          <button
            v-for="filter in filters"
            :key="filter.value"
            type="button"
            @click="changeFilter(filter.value)"
            :disabled="loading"
            class="rounded-xl border px-4 py-2.5 text-sm font-semibold transition disabled:opacity-50"
            :class="
              activeFilter === filter.value
                ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
            "
          >
            {{ filter.label }}
          </button>
        </div>

        <!-- Search -->
        <div class="relative max-w-xl">
          <Search
            :size="18"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
          />

          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search job, company, category..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
          />
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
      <div v-else-if="jobs.length === 0" class="py-16 text-center">
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
      <div v-else>
        <JobApprovalTable
          :jobs="jobs"
          @view="openDetails"
          @approve="openApprovalDialog($event, 'published')"
          @reject="openApprovalDialog($event, 'rejected')"
        />
      </div>

      <!-- Pagination -->
      <BasePagination
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
