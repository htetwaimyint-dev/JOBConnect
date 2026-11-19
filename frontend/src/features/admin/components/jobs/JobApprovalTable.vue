<script setup lang="ts">
import { Eye, CheckCircle2, XCircle } from "lucide-vue-next";

import { formatDate } from "@/utils/formatDate.ts";
import type { Job } from "../../types/job-approval.types";

defineProps<{
  jobs: Job[];
}>();

const emit = defineEmits<{
  view: [job: Job];
  approve: [job: Job];
  reject: [job: Job];
}>();

// Status Label
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

// Status Badge
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

// Status Dot
const statusDotClass = (status: string | null | undefined) => {
  const classes: Record<string, string> = {
    pending: "bg-amber-500",
    published: "bg-emerald-500",
    rejected: "bg-red-500",
    draft: "bg-slate-500",
    closed: "bg-gray-500",
  };

  return classes[status ?? ""] ?? "bg-slate-400";
};
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <!-- Table Header -->
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

      <!-- Table Body -->
      <tbody class="divide-y divide-slate-100">
        <tr
          v-for="job in jobs"
          :key="job.id"
          class="transition hover:bg-slate-50"
        >
          <!-- Job -->
          <td class="px-5 py-4">
            <p class="font-semibold text-slate-800">
              {{ job.title }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
              {{ job.city || "Location not specified" }}
            </p>
          </td>

          <!-- Company -->
          <td class="px-5 py-4 text-slate-600">
            {{ job.company?.name || "—" }}
          </td>

          <!-- Category -->
          <td class="px-5 py-4 text-slate-600">
            {{ job.category?.name || "—" }}
          </td>

          <!-- Posted Date -->
          <td class="px-5 py-4 whitespace-nowrap text-slate-600">
            {{ formatDate(job.created_at) }}
          </td>

          <!-- Status -->
          <td class="px-5 py-4">
            <span
              class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
              :class="statusClass(job.status)"
            >
              <span
                class="h-1.5 w-1.5 rounded-full"
                :class="statusDotClass(job.status)"
              />

              {{ statusLabel(job.status) }}
            </span>
          </td>

          <!-- Actions -->
          <td class="px-5 py-4">
            <div class="flex items-center justify-end gap-2">
              <!-- View: Always Visible -->
              <button
                type="button"
                @click="emit('view', job)"
                title="View Details"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-blue-200 px-3 py-2 text-xs font-medium text-blue-600 transition hover:bg-blue-50"
              >
                <Eye :size="15" />
                <span>View</span>
              </button>

              <!-- Approve: Pending & Rejected -->
              <button
                v-if="job.status === 'pending' || job.status === 'rejected'"
                type="button"
                @click="emit('approve', job)"
                title="Approve Job"
                class="inline-flex items-center justify-center rounded-lg border border-emerald-200 p-2 text-emerald-600 transition hover:bg-emerald-50"
              >
                <CheckCircle2 :size="16" />
              </button>

              <!-- Reject: Pending & Approved -->
              <button
                v-if="job.status === 'pending' || job.status === 'published'"
                type="button"
                @click="emit('reject', job)"
                title="Reject Job"
                class="inline-flex items-center justify-center rounded-lg border border-red-200 p-2 text-red-600 transition hover:bg-red-50"
              >
                <XCircle :size="16" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
