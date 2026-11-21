<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { Eye, RotateCcw, Trash2, ExternalLink } from "lucide-vue-next";

import UserStatsCards from "../components/users/UserStatsCards.vue";
import UserSearchFilter from "../components/users/UserSearchFilter.vue";
import UserStatusBadge from "../components/users/UserStatusBadge.vue";

import BasePagination from "@/components/common/BasePagination.vue";

import userRepository from "../repositories/userRepository";
import type { JobSeeker } from "../types/user.types";

// ================================
// State
// ================================

const users = ref<JobSeeker[]>([]);

const searchQuery = ref("");
const selectedStatus = ref("All Statuses");

const currentPage = ref(1);
const perPage = ref(5);

const loading = ref(false);

// ================================
// Fetch Job Seekers
// ================================

onMounted(async () => {
  loading.value = true;

  try {
    users.value = await userRepository.getJobSeekers();
  } finally {
    loading.value = false;
  }
});

// ================================
// Search & Filter
// ================================

const filteredUsers = computed(() =>
  users.value.filter((user) => {
    const q = searchQuery.value.trim().toLowerCase();

    const matchesSearch =
      user.name.toLowerCase().includes(q) ||
      user.email.toLowerCase().includes(q) ||
      user.profileTitle.toLowerCase().includes(q);

    const matchesStatus =
      selectedStatus.value === "All Statuses" ||
      (selectedStatus.value === "Active" ? !user.isDeleted : user.isDeleted);

    return matchesSearch && matchesStatus;
  }),
);

// Reset page when search or status filter changes
watch([searchQuery, selectedStatus], () => {
  currentPage.value = 1;
});

// ================================
// Pagination
// ================================

const total = computed(() => filteredUsers.value.length);

const lastPage = computed(() =>
  Math.max(1, Math.ceil(total.value / perPage.value)),
);

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;

  const end = start + perPage.value;

  return filteredUsers.value.slice(start, end);
});

// Change page
function changePage(page: number) {
  currentPage.value = page;
}

// Change rows per page
function changePerPage(value: number) {
  perPage.value = value;
  currentPage.value = 1;
}

// ================================
// Statistics
// ================================

const stats = computed(() => ({
  total: users.value.length,

  active: users.value.filter((user) => !user.isDeleted).length,

  suspended: users.value.filter((user) => user.isDeleted).length,
}));

// ================================
// Job Seeker Actions
// ================================

async function toggleStatus(user: JobSeeker) {
  await userRepository.toggleJobSeekerStatus(user.id);

  user.isDeleted = !user.isDeleted;
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
              <!-- Job Seeker Rows -->
              <tr
                v-for="(user, index) in paginatedUsers"
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
                      :src="user.logo"
                      alt="Profile"
                      class="h-10 w-10 rounded-full border object-cover"
                    />

                    <div>
                      <p
                        class="font-semibold text-slate-900"
                        :class="user.isDeleted && 'line-through opacity-50'"
                      >
                        {{ user.name }}
                      </p>

                      <p class="text-xs text-slate-500">
                        {{ user.role }}
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
                    <!-- View Details (Eye Icon) -->
                    <RouterLink
                      :to="`/admin/job-seekers/${user.id}`"
                      class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors whitespace-nowrap"
                    >
                      <Eye class="h-3.5 w-3.5 shrink-0" />
                      <span>View</span>
                    </RouterLink>

                    <!-- Restore -->
                    <button
                      v-if="user.isDeleted"
                      type="button"
                      class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100 transition-colors whitespace-nowrap"
                      @click="toggleStatus(user)"
                    >
                      <RotateCcw class="h-3.5 w-3.5 shrink-0" />
                      <span>Restore</span>
                    </button>

                    <!-- Suspend -->
                    <button
                      v-else
                      type="button"
                      class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors whitespace-nowrap"
                      @click="toggleStatus(user)"
                    >
                      <Trash2 class="h-3.5 w-3.5 shrink-0" />
                      <span>Suspend</span>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Loading -->
              <tr v-if="loading">
                <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                  Loading job seekers...
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="filteredUsers.length === 0">
                <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                  No job seekers found.
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
