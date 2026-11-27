<script setup lang="ts">
import { computed } from "vue";
import {
  BriefcaseBusiness,
  X,
  CalendarDays,
  Users,
  MapPin,
  Globe,
  Clock,
  Banknote,
  CheckCircle2,
  XCircle,
} from "lucide-vue-next";

import type { Job } from "../../types/job-approval.types";
import DOMPurify from "dompurify";

const props = defineProps<{
  job: Job | null;
  statusClass: (status: string | null | undefined) => string;
  statusLabel: (status: string | null | undefined) => string;
}>();

const emit = defineEmits<{
  close: [];
  approve: [job: Job];
  reject: [job: Job];
}>();

// Action Visibility
const canApprove = computed(() => {
  return props.job?.status === "pending" || props.job?.status === "rejected";
});

const canReject = computed(() => {
  return props.job?.status === "pending" || props.job?.status === "published";
});

// Sanitize HTML content before rendering
const sanitizedDescription = computed(() =>
  DOMPurify.sanitize(props.job?.description || ""),
);

const sanitizedRequirements = computed(() =>
  DOMPurify.sanitize(props.job?.requirements || ""),
);

const sanitizedResponsibilities = computed(() =>
  DOMPurify.sanitize(props.job?.responsibilities || ""),
);

// Salary
const salaryText = computed(() => {
  if (!props.job) return "—";

  const job = props.job;

  if (job.salary_type === "negotiable") {
    return "Negotiable";
  }

  const min = job.salary_min;
  const max = job.salary_max;

  if (min != null && max != null) {
    return `${Number(min).toLocaleString()} - ${Number(max).toLocaleString()}`;
  }

  if (min != null) {
    return `From ${Number(min).toLocaleString()}`;
  }

  if (max != null) {
    return `Up to ${Number(max).toLocaleString()}`;
  }

  return "Not specified";
});

// Location
const locationText = computed(() => {
  if (!props.job) return "—";

  return (
    [props.job.city, props.job.country].filter(Boolean).join(", ") ||
    "Location not specified"
  );
});

// Employment Type
const employmentTypeLabel = computed(() => {
  if (!props.job?.employment_type) return "—";

  return props.job.employment_type
    .replace(/_/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
});

// Workplace Type
const workplaceTypeLabel = computed(() => {
  if (!props.job?.workplace_type) return "—";

  return props.job.workplace_type
    .replace(/_/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
});

// Gender
const genderLabel = computed(() => {
  if (!props.job?.gender) return "Any";

  return props.job.gender
    .replace(/_/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
});

// Date Format
const formatDate = (date: string | null | undefined) => {
  if (!date) return "—";

  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};
</script>

<template>
  <Teleport to="body">
    <div
      v-if="job"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
      @click.self="emit('close')"
    >
      <div
        class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
      >
        <!-- Header -->
        <div
          class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4"
        >
          <div>
            <h2 class="text-lg font-bold text-slate-900">Job Details</h2>

            <p class="mt-1 text-xs text-slate-500">Complete job information</p>
          </div>

          <button
            type="button"
            @click="emit('close')"
            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
          >
            <X :size="20" />
          </button>
        </div>

        <div class="space-y-6 p-6">
          <!-- Job Identity -->
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div
              class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
            >
              <BriefcaseBusiness :size="32" />
            </div>

            <div class="min-w-0 flex-1">
              <h3 class="text-xl font-bold text-slate-900">
                {{ job.title }}
              </h3>

              <p class="mt-1 text-sm text-slate-500">
                {{ job.company?.name || "Company not specified" }}
              </p>

              <span
                class="mt-2 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                :class="statusClass(job.status)"
              >
                {{ statusLabel(job.status) }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Job Description</h4>

            <div
              v-if="sanitizedDescription"
              class="job-html-content text-sm leading-7 text-slate-600"
              v-html="sanitizedDescription"
            ></div>

            <p v-else class="text-sm text-slate-400">
              No job description provided.
            </p>
          </div>

          <!-- Job Information -->
          <div>
            <h4 class="mb-3 font-semibold text-slate-900">Job Information</h4>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <!-- Category -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <BriefcaseBusiness :size="15" />
                  Category
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ job.category?.name || "—" }}
                </p>
              </div>

              <!-- Employment Type -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Clock :size="15" />
                  Employment Type
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ employmentTypeLabel }}
                </p>
              </div>

              <!-- Salary -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Banknote :size="15" />
                  Salary
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ salaryText }}
                </p>

                <p
                  v-if="job.salary_type !== 'negotiable'"
                  class="mt-1 text-xs text-slate-500"
                >
                  {{ job.salary_type || "Salary" }}
                </p>
              </div>

              <!-- Vacancies -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Users :size="15" />
                  Vacancies
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ job.vacancies ?? "—" }}
                </p>
              </div>

              <!-- Workplace Type -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Globe :size="15" />
                  Workplace Type
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ workplaceTypeLabel }}
                </p>
              </div>

              <!-- Gender -->
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Users :size="15" />
                  Gender
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ genderLabel }}
                </p>
              </div>

              <!-- Location -->
              <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <MapPin :size="15" />
                  Location
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ locationText }}
                </p>
              </div>

              <!-- Deadline -->
              <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <CalendarDays :size="15" />
                  Application Deadline
                </div>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ formatDate(job.deadline) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Requirements -->
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Requirements</h4>

            <div
              v-if="sanitizedRequirements"
              class="job-html-content text-sm leading-7 text-slate-600"
              v-html="sanitizedRequirements"
            ></div>

            <p v-else class="text-sm text-slate-400">
              No requirements provided.
            </p>
          </div>

          <!-- Responsibilities -->
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">Responsibilities</h4>

            <div
              v-if="sanitizedResponsibilities"
              class="job-html-content text-sm leading-7 text-slate-600"
              v-html="sanitizedResponsibilities"
            ></div>

            <p v-else class="text-sm text-slate-400">
              No responsibilities provided.
            </p>
          </div>

          <!-- Rejection Reason -->
          <div
            v-if="job.rejection_reason"
            class="rounded-xl border border-red-200 bg-red-50 p-4"
          >
            <h4 class="font-semibold text-red-800">Rejection Reason</h4>

            <p class="mt-2 whitespace-pre-line text-sm text-red-700">
              {{ job.rejection_reason }}
            </p>
          </div>

          <!-- Dates -->
          <div
            class="grid grid-cols-1 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2"
          >
            <div>
              <p class="text-xs text-slate-400">Posted Date</p>

              <p class="mt-1 text-sm font-medium text-slate-700">
                {{ formatDate(job.created_at) }}
              </p>
            </div>

            <div>
              <p class="text-xs text-slate-400">Application Deadline</p>

              <p class="mt-1 text-sm font-medium text-slate-700">
                {{ formatDate(job.deadline) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="flex flex-wrap justify-end gap-3 border-t border-slate-100 px-6 py-4"
        >
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
          >
            Close
          </button>

          <button
            v-if="canApprove"
            type="button"
            @click="emit('approve', job)"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
          >
            <CheckCircle2 :size="17" />
            Approve
          </button>

          <button
            v-if="canReject"
            type="button"
            @click="emit('reject', job)"
            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
          >
            <XCircle :size="17" />
            Reject
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.job-html-content :deep(p) {
  margin-bottom: 0.5rem;
}

.job-html-content :deep(p:last-child) {
  margin-bottom: 0;
}

.job-html-content :deep(ul) {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
  padding-left: 1.5rem;
  list-style-type: disc;
}

.job-html-content :deep(ol) {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
  padding-left: 1.5rem;
  list-style-type: decimal;
}

.job-html-content :deep(li) {
  margin-top: 0.25rem;
  margin-bottom: 0.25rem;
}

.job-html-content :deep(strong),
.job-html-content :deep(b) {
  font-weight: 600;
}

.job-html-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
}
</style>
