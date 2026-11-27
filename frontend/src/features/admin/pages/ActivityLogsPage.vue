<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";
import { Activity, AlertCircle, Clock3, RefreshCw, X } from "lucide-vue-next";

import BasePagination from "@/components/common/BasePagination.vue";
import { formatDate } from "@/utils/formatDate";
import { adminRepository } from "@/features/admin/repositories/adminRepository";

// Types
interface ActivityLog {
  id: number;
  actor: string;
  action: string;
  description: string | null;
  created_at: string | null;
}

// States
const activities = ref<ActivityLog[]>([]);

const loading = ref(false);
const errorMessage = ref("");

const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(5);

// Request control
let requestId = 0;

// Fetch Activity Logs
const fetchActivityLogs = async (page = 1) => {
  const currentRequestId = ++requestId;

  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await adminRepository.getActivityLogs({
      page,
      per_page: perPage.value,
    });

    // Ignore outdated responses
    if (currentRequestId !== requestId) return;

    activities.value = response.data ?? [];

    currentPage.value = response.pagination.current_page;
    lastPage.value = response.pagination.last_page;
    total.value = response.pagination.total;
  } catch (error: any) {
    if (currentRequestId !== requestId) return;

    console.error("Failed to fetch activity logs:", error);

    errorMessage.value =
      error.response?.data?.message ?? "Failed to load activity logs.";
  } finally {
    if (currentRequestId === requestId) {
      loading.value = false;
    }
  }
};

// Pagination
const goToPage = (page: number) => {
  if (page < 1 || page > lastPage.value || loading.value) return;

  fetchActivityLogs(page);
};

const changePerPage = (value: number) => {
  if (loading.value) return;

  perPage.value = value;
  currentPage.value = 1;

  fetchActivityLogs(1);
};

// Refresh
const refreshActivities = () => {
  if (loading.value) return;

  fetchActivityLogs(currentPage.value);
};

// Initial Load
onMounted(() => {
  fetchActivityLogs(1);
});

// Cleanup
onUnmounted(() => {
  requestId++;
});
</script>

<template>
  <div class="w-full min-w-0 space-y-5 sm:space-y-6">
    <!-- Page Header -->
    <section
      class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
    >
      <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-800 sm:text-2xl">
          Activity Logs
        </h1>

        <p class="mt-1 text-xs text-slate-500 sm:text-sm">
          View and monitor all activities performed across the platform.
        </p>
      </div>

      <button
        type="button"
        @click="refreshActivities"
        :disabled="loading"
        class="inline-flex w-fit items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
      >
        <RefreshCw :size="16" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </section>

    <!-- Error Message -->
    <div
      v-if="errorMessage"
      class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
    >
      <AlertCircle :size="18" class="mt-0.5 shrink-0" />

      <p class="min-w-0 flex-1">
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

    <!-- Activity Logs Container -->
    <section
      class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
      <!-- Table Header -->
      <div
        class="flex flex-col gap-2 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5"
      >
        <div>
          <h2 class="font-semibold text-slate-800">All Activities</h2>

          <p class="mt-1 text-xs text-slate-400">
            Complete platform activity history
          </p>
        </div>

        <div
          class="flex w-fit items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600"
        >
          <Activity :size="16" />
          {{ total }} Activities
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="flex items-center justify-center gap-2 py-16 text-sm text-slate-500"
      >
        <RefreshCw :size="20" class="animate-spin" />
        Loading activities...
      </div>

      <!-- Empty State -->
      <div v-else-if="activities.length === 0" class="px-4 py-16 text-center">
        <div
          class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"
        >
          <Activity :size="22" />
        </div>

        <p class="font-medium text-slate-700">No activities found</p>

        <p class="mt-1 text-sm text-slate-500">
          There are no recorded activities at the moment.
        </p>
      </div>

      <!-- Activity List -->
      <div v-else class="divide-y divide-slate-100">
        <article
          v-for="activity in activities"
          :key="activity.id"
          class="flex gap-3 px-4 py-5 transition hover:bg-slate-50/70 sm:gap-4 sm:px-5"
        >
          <!-- Activity Icon -->
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"
          >
            <Activity :size="19" />
          </div>

          <!-- Activity Information -->
          <div class="min-w-0 flex-1">
            <div
              class="flex flex-col gap-1 sm:flex-row sm:flex-wrap sm:items-center sm:gap-2"
            >
              <h3 class="text-sm font-semibold text-slate-800">
                {{ activity.action }}
              </h3>

              <span
                class="w-fit rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-500"
              >
                #{{ activity.id }}
              </span>
            </div>

            <p class="mt-1 break-words text-sm leading-6 text-slate-600">
              {{ activity.description || "No description available." }}
            </p>

            <div
              class="mt-2 flex flex-col gap-1 text-xs text-slate-400 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4"
            >
              <span class="flex items-center gap-1.5">
                <Activity :size="13" />
                By {{ activity.actor || "System" }}
              </span>

              <span class="flex items-center gap-1.5">
                <Clock3 :size="13" />
                {{ formatDate(activity.created_at) }}
              </span>
            </div>
          </div>
        </article>
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
    </section>
  </div>
</template>
