<script setup lang="ts">
import { ref, onMounted } from "vue";
import AdminDataTable from "../components/UserDataTable.vue";
import { adminRepository } from "../repositories/adminRepository";
import type { JobSeeker, PaginationMeta } from "../types/user.types";

const columns = [
  { key: "index", label: "No." },
  { key: "name", label: "Name" },
  { key: "email", label: "Email" },
  { key: "profile", label: "Jobs Count" },
  { key: "actions", label: "Option", align: "text-right" },
];

const jobSeekers = ref<JobSeeker[]>([]);
const meta = ref<PaginationMeta | null>(null);
const loading = ref(false);

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);
const isDeleting = ref(false);

const loadData = async (page = 1) => {
  loading.value = true;
  try {
    const response = await adminRepository.getJobSeekers(page);
    jobSeekers.value = response.data.map((item: any, idx: number) => ({
      ...item,
      index: (response.meta.from ?? 1) + idx,
    }));
    meta.value = response.meta;
  } catch (error) {
    console.error("Error:", error);
  } finally {
    loading.value = false;
  }
};

const confirmDelete = (id: number) => {
  deleteId.value = id;
  showDeleteModal.value = true;
};

const executeDelete = async () => {
  if (!deleteId.value) return;
  isDeleting.value = true;
  try {
    await adminRepository.deleteJobSeeker(deleteId.value);
    showDeleteModal.value = false;
    await loadData(meta.value?.current_page || 1);
  } catch (error) {
    console.error("Delete failed:", error);
  } finally {
    isDeleting.value = false;
  }
};

onMounted(() => loadData(1));
</script>

<template>
  <div>
    <AdminDataTable
      title="Job Seekers Management"
      subtitle="Manage all registered job seekers"
      :columns="columns"
      :data="jobSeekers"
      :meta="meta"
      :loading="loading"
      @page-change="loadData"
    >
      <template #cell-name="{ row }">
        <div class="font-medium text-gray-900">{{ row.name }}</div>
      </template>

      <template #cell-profile="{ row }">
        <span v-if="row.profile">{{ row.profile }}</span>
        <span v-else class="text-gray-400">No jobs</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="flex items-center justify-end gap-3">
          <button
            @click="confirmDelete(row.id)"
            class="text-red-500 hover:text-red-700"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg>
          </button>
        </div>
      </template>
    </AdminDataTable>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="w-96 rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900">Confirm Suspend</h3>
        <p class="mt-2 text-sm text-gray-500">
          Are you sure you want to suspend this job seeker? This action cannot
          be undone.
        </p>
        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="showDeleteModal = false"
            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
          >
            Cancel
          </button>
          <button
            @click="executeDelete"
            :disabled="isDeleting"
            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
          >
            {{ isDeleting ? "Suspending..." : "Yes, Suspend" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
