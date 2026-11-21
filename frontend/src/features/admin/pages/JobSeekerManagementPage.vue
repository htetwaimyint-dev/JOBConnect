<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { Eye, RotateCcw, Trash2 } from "lucide-vue-next";

import UserStatsCards from "../components/users/UserStatsCards.vue";
import UserSearchFilter from "../components/users/UserSearchFilter.vue";
import UserStatusBadge from "../components/users/UserStatusBadge.vue";
import BasePagination from "@/components/common/BasePagination.vue";

import { adminRepository } from "../repositories/adminRepository";
import type { JobSeeker } from "../types/user.types";

// ================================
// State
// ================================

const users = ref<JobSeeker[]>([]);

const searchQuery = ref("");
const selectedStatus = ref("All Statuses");

const currentPage = ref(1);
const perPage = ref(5);

const lastPage = ref(1);
const total = ref(0);

const loading = ref(false);

const stats = ref({
  total: 0,
  active: 0,
  suspended: 0,
});

// ================================
// Fetch Job Seekers
// ================================

async function fetchJobSeekers(page = 1) {
  loading.value = true;

  try {
    const response = await adminRepository.getJobSeekers({
      page,
      search: searchQuery.value,
      status: selectedStatus.value,
      per_page: perPage.value,
    });

    // Laravel Resource Collection response
    users.value = (response.data ?? []).map((user: JobSeeker) => ({
      ...user,
      isDeleted: user.status === "suspended" || user.deleted_at !== null,
    }));

    // Backend pagination information
    currentPage.value = response.meta?.current_page ?? page;
    lastPage.value = response.meta?.last_page ?? 1;
    total.value = response.meta?.total ?? 0;
    perPage.value = response.meta?.per_page ?? perPage.value;

    // Backend global statistics
    stats.value = response.stats ?? {
      total: 0,
      active: 0,
      suspended: 0,
    };
  } catch (error) {
    console.error("Failed to fetch job seekers:", error);

    users.value = [];
    total.value = 0;
    lastPage.value = 1;
  } finally {
    loading.value = false;
  }
}

// ================================
// Initial Fetch
// ================================

onMounted(() => {
  fetchJobSeekers(1);
});

// ================================
// Search & Filter
// ================================

// When search or status changes,
// request the first page from Backend.
watch([searchQuery, selectedStatus], () => {
  fetchJobSeekers(1);
});

// ================================
// Pagination
// ================================

// Change page
function changePage(page: number) {
  fetchJobSeekers(page);
}

// Change rows per page
function changePerPage(value: number) {
  perPage.value = value;
  fetchJobSeekers(1);
}

// ================================
// Job Seeker Actions
// ================================

async function toggleStatus(user: JobSeeker) {
  const isSuspended = user.isDeleted;

  const action = isSuspended ? "restore" : "suspend";

  const confirmed = window.confirm(
    `Are you sure you want to ${action} ${user.name}?`,
  );

  if (!confirmed) return;

  try {
    if (isSuspended) {
      await adminRepository.restoreJobSeeker(user.id);
    } else {
      await adminRepository.suspendJobSeeker(user.id);
    }

    // Reload current page and refresh global statistics
    await fetchJobSeekers(currentPage.value);
  } catch (error) {
    console.error(`Failed to ${action} job seeker:`, error);
  }
}

// ================================
// Profile Photo
// ================================

function getProfileImage(path?: string | null): string {
  if (!path) return "";

  if (path.startsWith("http")) {
    return path;
  }

  return `http://127.0.0.1:8000/storage/${path}`;
}
</script>

<template>
  <main class="min-h-screen bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">
      <!-- Header -->
      <header class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Job Seeker Management</h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage job seeker accounts and profiles.
        </p>
      </header>

      <!-- Statistics -->
      <UserStatsCards v-bind="stats" noun="Job Seekers" />

      <!-- Job Seeker Table Section -->
      <section
        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
      >
        <!-- Search & Filter -->
        <UserSearchFilter
          v-model:search="searchQuery"
          v-model:status="selectedStatus"
        />

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px] text-left text-sm text-slate-600">
            <!-- Table Header -->
            <thead
              class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500"
            >
              <tr>
                <th class="px-5 py-3.5">No.</th>
                <th class="px-5 py-3.5">Job Seeker</th>
                <th class="px-5 py-3.5">Email</th>
                <th class="px-5 py-3.5 text-center">Status</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
              </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="divide-y divide-slate-100">
              <!-- Loading -->
              <tr v-if="loading">
                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                  Loading job seekers...
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="users.length === 0">
                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                  No job seekers found.
                </td>
              </tr>

              <!-- Job Seeker Rows -->
              <tr
                v-for="(user, index) in users"
                :key="user.id"
                class="hover:bg-slate-50"
              >
                <!-- Number -->
                <td class="px-5 py-4">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </td>

                <!-- Job Seeker Information -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      v-if="user.profile?.profile_photo"
                      :src="getProfileImage(user.profile.profile_photo)"
                      alt="Profile"
                      class="h-10 w-10 rounded-full border object-cover"
                    />

                    <div
                      v-else
                      class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700"
                    >
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>

                    <div>
                      <p
                        class="font-semibold text-slate-900"
                        :class="user.isDeleted && 'line-through opacity-50'"
                      >
                        {{ user.name }}
                      </p>

                      <p class="text-xs text-slate-500">
                        {{ user.profile?.professional_title || user.role }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td class="px-5 py-4">
                  {{ user.email }}
                </td>

                <!-- Status -->
                <td class="px-5 py-4 text-center">
                  <UserStatusBadge :is-deleted="user.isDeleted" />
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="inline-flex items-center justify-end gap-2">
                    <!-- View Details -->
                    <RouterLink
                      :to="`/admin/job-seekers/${user.id}`"
                      class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-medium text-indigo-700 transition-colors hover:bg-indigo-100"
                    >
                      <Eye class="h-3.5 w-3.5 shrink-0" />
                      <span>View</span>
                    </RouterLink>

                    <!-- Restore -->
                    <button
                      v-if="user.isDeleted"
                      type="button"
                      class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-medium text-emerald-700 transition-colors hover:bg-emerald-100"
                      @click="toggleStatus(user)"
                    >
                      <RotateCcw class="h-3.5 w-3.5 shrink-0" />
                      <span>Restore</span>
                    </button>

                    <!-- Suspend -->
                    <button
                      v-else
                      type="button"
                      class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-100"
                      @click="toggleStatus(user)"
                    >
                      <Trash2 class="h-3.5 w-3.5 shrink-0" />
                      <span>Suspend</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Base Pagination -->
        <BasePagination
          :current-page="currentPage"
          :last-page="lastPage"
          :total="total"
          :per-page="perPage"
          :loading="loading"
          @change="changePage"
          @update:per-page="changePerPage"
        />
      </section>
    </div>
  </main>
</template>
