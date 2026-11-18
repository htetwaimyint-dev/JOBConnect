<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import {
  Search,
  RefreshCw,
  Eye,
  CheckCircle2,
  XCircle,
  LoaderCircle,
  BriefcaseBusiness,
} from "lucide-vue-next";

import jobApprovalRepository from "../repositories/jobApprovalRepository";
import type { Job } from "../types/job-approval.types";

import JobApprovalModal from "../components/jobs/JobApprovalModal.vue";
import BasePagination from "@/components/common/BasePagination.vue";
import JobDetailContent from "@/features/jobs/components/JobDetailContent.vue";

import type { JobFormData } from "@/features/jobs/types/job.types";

type ApprovalAction = "published" | "rejected";

const jobs = ref<Job[]>([]);
const loading = ref(false);
const errorMessage = ref("");

const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);
const lastPage = ref(1);
const total = ref(0);

const selectedJob = ref<Job | null>(null);
const showDetails = ref(false);

const approvalJob = ref<Job | null>(null);
const approvalAction = ref<ApprovalAction | null>(null);
const rejectionReason = ref("");
const submitting = ref(false);

// Convert Admin Job data to the format expected by JobDetailContent.
const detailJob = computed(() => {
  const job = selectedJob.value;

  if (!job) return null;

  return {
    id: job.id,
    title: job.title,

    category_id: job.category?.id ?? null,

    deadline: job.deadline ?? "",

    employment_type: job.employment_type as JobFormData["employment_type"],

    vacancies: job.vacancies ?? 1,

    gender: (job.gender ?? "any") as JobFormData["gender"],

    workplace_type: job.workplace_type as JobFormData["workplace_type"],

    country: job.country ?? "",
    city: job.city ?? "",

    salary_type: (job.salary_type ??
      "negotiable") as JobFormData["salary_type"],

    salary_min: job.salary_min === null ? undefined : Number(job.salary_min),

    salary_max: job.salary_max === null ? undefined : Number(job.salary_max),

    description: job.description ?? "",
    requirements: job.requirements ?? "",
    responsibilities: job.responsibilities ?? "",

    submission_status: "pending" as const,

    job_questions: [],

    company: job.company,
    category: job.category,
  };
});

// Client-side search for the currently loaded page.
const filteredJobs = computed(() => {
  const keyword = search.value.trim().toLowerCase();

  if (!keyword) return jobs.value;

  return jobs.value.filter((job) =>
    [job.title, job.company?.name, job.category?.name, job.city]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(keyword)),
  );
});

async function fetchPendingJobs() {
  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await jobApprovalRepository.getPendingJobs({
      page: currentPage.value,
      per_page: perPage.value,
    });

    jobs.value = response.data;

    if (response.meta) {
      currentPage.value = response.meta.current_page;
      lastPage.value = response.meta.last_page;
      total.value = response.meta.total;
    } else {
      lastPage.value = 1;
      total.value = jobs.value.length;
    }
  } catch (error: any) {
    console.error("Failed to load pending jobs:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Unable to load pending jobs.";
  } finally {
    loading.value = false;
  }
}

async function viewJob(job: Job) {
  errorMessage.value = "";

  try {
    const response = await jobApprovalRepository.getJobById(job.id);

    selectedJob.value = response.data;
    showDetails.value = true;
  } catch (error: any) {
    console.error("Failed to load job details:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Unable to load job details.";
  }
}

function changePage(page: number) {
  if (page < 1 || page > lastPage.value) return;

  currentPage.value = page;
  fetchPendingJobs();
}

function changePerPage(value: number) {
  perPage.value = value;
  currentPage.value = 1;
  fetchPendingJobs();
}

function openApproval(action: ApprovalAction) {
  if (!selectedJob.value) return;

  approvalJob.value = selectedJob.value;
  approvalAction.value = action;
  rejectionReason.value = "";

  showDetails.value = false;
}

function closeApproval() {
  if (submitting.value) return;

  approvalJob.value = null;
  approvalAction.value = null;
  rejectionReason.value = "";
}

async function confirmApproval() {
  if (!approvalJob.value || !approvalAction.value) return;

  if (approvalAction.value === "rejected" && !rejectionReason.value.trim()) {
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

    await fetchPendingJobs();
  } catch (error: any) {
    console.error("Failed to update job approval:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Unable to update job approval.";
  } finally {
    submitting.value = false;
  }
}

function formatDate(date: string | null) {
  if (!date) return "—";

  return new Date(date).toLocaleDateString();
}

onMounted(fetchPendingJobs);
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div
      class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
    >
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Job Approval</h1>

        <p class="mt-1 text-sm text-slate-500">
          Review and manage pending job applications.
        </p>
      </div>

      <button
        type="button"
        @click="fetchPendingJobs"
        :disabled="loading"
        class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
      >
        <RefreshCw :size="16" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </div>

    <!-- Summary -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="flex items-center gap-4">
        <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
          <BriefcaseBusiness :size="24" />
        </div>

        <div>
          <p class="text-sm text-slate-500">Pending Jobs</p>

          <p class="text-2xl font-bold text-slate-800">
            {{ total }}
          </p>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="relative max-w-md">
        <Search
          :size="18"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
        />

        <input
          v-model="search"
          type="text"
          placeholder="Search job title, company, category..."
          class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
        />
      </div>
    </div>

    <!-- Error -->
    <div
      v-if="errorMessage"
      class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"
    >
      {{ errorMessage }}
    </div>

    <!-- Jobs Table -->
    <div
      class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
      <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="font-semibold text-slate-800">Pending Job Listings</h2>
      </div>

      <div
        v-if="loading"
        class="flex items-center justify-center gap-2 py-16 text-sm text-slate-500"
      >
        <LoaderCircle :size="20" class="animate-spin" />
        Loading pending jobs...
      </div>

      <div v-else-if="filteredJobs.length === 0" class="py-16 text-center">
        <BriefcaseBusiness :size="36" class="mx-auto mb-3 text-slate-300" />

        <p class="font-medium text-slate-700">No pending jobs found</p>

        <p class="mt-1 text-sm text-slate-500">
          There are no jobs matching your search.
        </p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th class="px-5 py-4 font-semibold">Job</th>
              <th class="px-5 py-4 font-semibold">Company</th>
              <th class="px-5 py-4 font-semibold">Category</th>
              <th class="px-5 py-4 font-semibold">Posted Date</th>
              <th class="px-5 py-4 font-semibold">Status</th>
              <th class="px-5 py-4 text-center font-semibold">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="job in filteredJobs"
              :key="job.id"
              class="transition hover:bg-slate-50"
            >
              <td class="px-5 py-4">
                <p class="font-semibold text-slate-800">
                  {{ job.title }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                  {{ job.city || "Location not specified" }}
                </p>
              </td>

              <td class="px-5 py-4 text-slate-600">
                {{ job.company?.name || "—" }}
              </td>

              <td class="px-5 py-4 text-slate-600">
                {{ job.category?.name || "—" }}
              </td>

              <td class="px-5 py-4 text-slate-600">
                {{ formatDate(job.created_at) }}
              </td>

              <td class="px-5 py-4">
                <span
                  class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700"
                >
                  Pending
                </span>
              </td>

              <td class="px-5 py-4 text-center">
                <button
                  type="button"
                  @click="viewJob(job)"
                  class="inline-flex items-center gap-2 rounded-lg border border-blue-200 px-3 py-2 text-xs font-medium text-blue-600 transition hover:bg-blue-50"
                >
                  <Eye :size="15" />
                  View Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Common Pagination -->
      <BasePagination
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        :per-page="perPage"
        :loading="loading"
        @change="changePage"
        @update:per-page="changePerPage"
      />
    </div>

    <!-- Job Details Modal -->
    <div
      v-if="showDetails && selectedJob && detailJob"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
      @click.self="showDetails = false"
    >
      <div
        class="max-h-[90vh] w-full max-w-6xl overflow-y-auto rounded-2xl bg-white shadow-xl"
      >
        <!-- Modal Header -->
        <div
          class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4"
        >
          <h2 class="text-lg font-bold text-slate-800">Job Details</h2>

          <button
            type="button"
            @click="showDetails = false"
            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100"
          >
            <XCircle :size="20" />
          </button>
        </div>

        <div class="space-y-6 p-6">
          <!-- Reusable Job Detail Content -->
          <JobDetailContent
            :job="detailJob"
            mode="admin"
            :category-name="selectedJob.category?.name ?? undefined"
          />

          <!-- Approval Actions -->
          <div
            class="flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"
          >
            <button
              type="button"
              @click="openApproval('rejected')"
              class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
            >
              <XCircle :size="17" />
              Reject Job
            </button>

            <button
              type="button"
              @click="openApproval('published')"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
            >
              <CheckCircle2 :size="17" />
              Approve Job
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Approval Confirmation Modal -->
    <JobApprovalModal
      :job="approvalJob"
      :action="approvalAction"
      :rejection-reason="rejectionReason"
      :submitting="submitting"
      @close="closeApproval"
      @confirm="confirmApproval"
      @update:rejection-reason="rejectionReason = $event"
    />
  </div>
</template>
