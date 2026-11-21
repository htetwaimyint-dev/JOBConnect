<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { Eye, Trash2, RotateCcw } from "lucide-vue-next";

import UserStatsCards from "../components/users/UserStatsCards.vue";
import UserSearchFilter from "../components/users/UserSearchFilter.vue";
import UserStatusBadge from "../components/users/UserStatusBadge.vue";
import CompanyDetailModal from "../components/users/CompanyDetailModal.vue";
import BasePagination from "@/components/common/BasePagination.vue";

import { adminRepository } from "../repositories/adminRepository";
import type { Employer } from "../types/user.types";

// ================================
// State
// ================================
const users = ref<Employer[]>([]);
const searchQuery = ref("");
const selectedStatus = ref("All Statuses");

const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(10);

const selectedEmployer = ref<Employer | null>(null);
const loading = ref(false);
const deletingId = ref<number | null>(null);

const stats = ref({
  total: 0,
  active: 0,
  suspended: 0,
});

// ================================
// Fetch Companies
// ================================
async function fetchCompanies(page = 1) {
  loading.value = true;

  try {
    const response = await adminRepository.getCompanies({
      page: page,
      search: searchQuery.value.trim(),
      status: selectedStatus.value,
      per_page: perPage.value,
    });

    console.log("API Response:", response);

    // Support Axios response or extracted response.data
    const paginatedData = response.data?.data ? response.data : response;

    const employerData = paginatedData.data;

    if (!Array.isArray(employerData)) {
      console.error("Employer data is not an array:", employerData);
      users.value = [];
      return;
    }

    users.value = employerData.map((user: any) => ({
      ...user,
      isDeleted: user.deleted_at !== null,
    }));

    currentPage.value = paginatedData.meta?.current_page ?? page;
    lastPage.value = paginatedData.meta?.last_page ?? 1;
    total.value = paginatedData.meta?.total ?? users.value.length;
    perPage.value = paginatedData.meta?.per_page ?? 10;
  } catch (error) {
    console.error("Failed to fetch companies:", error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchCompanies(1);
});

// ================================
// Search & Filter
// ================================
const filteredUsers = computed(() =>
  users.value.filter((user) => {
    const q = searchQuery.value.trim().toLowerCase();

    const matchesSearch =
      (user.name?.toLowerCase() || "").includes(q) ||
      (user.email?.toLowerCase() || "").includes(q) ||
      (user.company?.name?.toLowerCase() || "").includes(q);

    const matchesStatus =
      selectedStatus.value === "All Statuses" ||
      (selectedStatus.value === "Active" ? !user.isDeleted : user.isDeleted);

    return matchesSearch && matchesStatus;
  }),
);

// ================================
// Pagination Handlers
// ================================
function changePage(page: number) {
  fetchCompanies(page);
}

function changePerPage(value: number) {
  perPage.value = value;
  fetchCompanies(1);
}

// ================================
// Statistics
// ================================
const stats = computed(() => ({
  total: total.value,
  active: users.value.filter((user) => !user.isDeleted).length,
  suspended: users.value.filter((user) => user.isDeleted).length,
}));

// ================================
// Suspend Employer
// ================================
async function handleSuspendCompany(user: Employer) {
  const confirmed = window.confirm(
    `Are you sure you want to suspend "${user.company?.name || user.name}"?`,
  );

  if (!confirmed) return;

  deletingId.value = user.id;

  try {
    await adminRepository.suspendCompany(user.id);

    // Update frontend after successful API request
    user.isDeleted = true;
    user.status = "suspended";
  } catch (error: any) {
    console.error("Suspend employer error:", error);

    alert(error?.response?.data?.message || "Failed to suspend employer.");
  } finally {
    deletingId.value = null;
  }
}

// ================================
// Restore Employer
// ================================
async function handleRestoreCompany(user: Employer) {
  const confirmed = window.confirm(
    `Are you sure you want to restore "${user.company?.name || user.name}"?`,
  );

  if (!confirmed) return;

  deletingId.value = user.id;

  try {
    await adminRepository.restoreCompany(user.id);

    // Update frontend after successful API request
    user.isDeleted = false;
    user.status = "active";
  } catch (error: any) {
    console.error("Restore employer error:", error);

    alert(error?.response?.data?.message || "Failed to restore employer.");
  } finally {
    deletingId.value = null;
  }
}

// ================================
// View Company
// ================================
function viewCompany(user: Employer) {
  selectedEmployer.value = user;
}

// ================================
// Storage URL
// ================================
const STORAGE_URL = "http://localhost:8000/storage";

// Company Logo URL
function getCompanyLogo(logoPath?: string | null) {
  if (!logoPath) return null;

  if (logoPath.startsWith("http")) return logoPath;

  return `${STORAGE_URL}/${logoPath}`;
}

// User Image URL
function getImageUrl(path?: string | null) {
  if (!path) return null;

  if (path.startsWith("http")) return path;

  return `${STORAGE_URL}/${path}`;
}
</script>

<template>
  <main class="min-h-screen bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">
      <!-- Header -->
      <header class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Employer Management</h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage employer accounts, companies and posted jobs.
        </p>
      </header>

      <!-- Statistics -->
      <UserStatsCards v-bind="stats" noun="Employers" />

      <!-- Employer Table Section -->
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
          <table class="w-full min-w-[900px] text-left text-sm text-slate-600">
            <!-- Table Header -->
            <thead
              class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500"
            >
              <tr>
                <th class="px-5 py-3.5">No.</th>
                <th class="px-5 py-3.5">Employer</th>
                <th class="px-5 py-3.5">Email</th>
                <th class="px-5 py-3.5">Company</th>
                <th class="px-5 py-3.5 text-center">Job Count</th>
                <th class="px-5 py-3.5 text-center">Status</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
              </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="divide-y divide-slate-100">
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                  Loading employers...
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="filteredUsers.length === 0">
                <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                  No employers found.
                </td>
              </tr>

              <!-- Employer Rows -->
              <tr
                v-else
                v-for="(user, index) in filteredUsers"
                :key="user.id"
                class="hover:bg-slate-50 transition-colors"
              >
                <!-- Number -->
                <td class="px-5 py-4">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </td>

                <!-- Employer Information -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <!-- Profile Photo -->
                    <img
                      v-if="user.profile?.profile_photo"
                      :src="getImageUrl(user.profile.profile_photo)!"
                      alt="Profile photo"
                      class="h-10 w-10 rounded-full border object-cover shrink-0"
                    />

                    <!-- Default Avatar -->
                    <div
                      v-else
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border bg-slate-100 text-sm font-bold text-slate-600"
                    >
                      {{ user.name?.charAt(0)?.toUpperCase() || "U" }}
                    </div>

                    <div>
                      <p
                        class="font-semibold text-slate-900"
                        :class="user.isDeleted && 'opacity-50 line-through'"
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

                <!-- Company -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2">
                    <!-- Company Logo -->
                    <img
                      v-if="user.company?.logo"
                      :src="getCompanyLogo(user.company.logo)!"
                      alt="Company logo"
                      class="h-7 w-7 rounded border object-cover shrink-0"
                    />

                    <!-- Default Company Logo -->
                    <div
                      v-else
                      class="flex h-7 w-7 shrink-0 items-center justify-center rounded border bg-indigo-50 text-xs font-bold text-indigo-600"
                    >
                      {{ user.company?.name?.charAt(0)?.toUpperCase() || "C" }}
                    </div>

                    <span class="font-medium text-slate-800">
                      {{ user.company?.name || "No Company" }}
                    </span>

                    <RouterLink
                      v-if="user.company?.id"
                      :to="`/admin/companies/${user.company.id}`"
                      class="inline-flex items-center justify-center rounded-md bg-indigo-50 p-1.5 text-indigo-600 hover:bg-indigo-100 transition-colors"
                      title="View company"
                    >
                      <Eye class="h-4 w-4" />
                    </RouterLink>
                  </div>
                </td>

                <!-- Job Count -->
                <td class="px-5 py-4 text-center">
                  <span
                    class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700"
                  >
                    {{ user.jobs_count ?? user.company?.jobs_count ?? 0 }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4 text-center">
                  <UserStatusBadge :is-deleted="user.isDeleted" />
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="inline-flex items-center justify-end gap-2">
                    <!-- Restore -->
                    <button
                      v-if="user.isDeleted"
                      type="button"
                      :disabled="deletingId === user.id"
                      class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50 transition-colors whitespace-nowrap"
                      @click="handleRestoreCompany(user)"
                    >
                      <RotateCcw class="h-3.5 w-3.5 shrink-0" />

                      <span>
                        {{
                          deletingId === user.id ? "Restoring..." : "Restore"
                        }}
                      </span>
                    </button>

                    <!-- Suspend -->
                    <button
                      v-else
                      type="button"
                      :disabled="deletingId === user.id"
                      class="inline-flex items-center gap-1.5 rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50 transition-colors whitespace-nowrap"
                      @click="handleSuspendCompany(user)"
                    >
                      <Trash2 class="h-3.5 w-3.5 shrink-0" />

                      <span>
                        {{
                          deletingId === user.id ? "Suspending..." : "Suspend"
                        }}
                      </span>
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

      <!-- Company Detail Modal -->
      <CompanyDetailModal
        :employer="selectedEmployer"
        @close="selectedEmployer = null"
      />
    </div>
  </main>
</template>
