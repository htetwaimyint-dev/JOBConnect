<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

import { Search, Users, LoaderCircle } from "lucide-vue-next";

import JobSeekerCard from "../components/JobSeekerCard.vue";
import jobSeekerRepository from "../repositories/jobSeekerRepository";

import BasePagination from "@/components/common/BasePagination.vue";
import BaseFilterActions from "@/components/common/BaseFilterActions.vue";

import type { JobSeeker } from "../types/job-seeker.types";

const router = useRouter();

// Job Seeker Data
const jobSeekers = ref<JobSeeker[]>([]);

// Search
const search = ref("");
const appliedSearch = ref("");

// Pagination
const perPage = ref(5);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

// Loading and Error
const loading = ref(false);
const errorMessage = ref("");

// Prevent overlapping requests
let requestId = 0;

// Fetch Job Seekers
const fetchJobSeekers = async () => {
  const currentRequestId = ++requestId;

  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await jobSeekerRepository.getJobSeekers({
      search: appliedSearch.value.trim() || undefined,
      per_page: perPage.value,
      page: currentPage.value,
    });

    // Ignore an outdated response
    if (currentRequestId !== requestId) return;

    jobSeekers.value = response.data;
    currentPage.value = response.meta.current_page;
    lastPage.value = response.meta.last_page;
    total.value = response.meta.total;
  } catch (error: any) {
    if (currentRequestId !== requestId) return;

    console.error("Failed to fetch job seekers:", error);
    console.error("API Error Response:", error.response?.data);
    console.error("API Status:", error.response?.status);

    errorMessage.value = "Unable to load job seekers. Please try again.";
  } finally {
    if (currentRequestId === requestId) {
      loading.value = false;
    }
  }
};

// Search only when Search button is clicked
const handleSearch = () => {
  if (loading.value) return;

  appliedSearch.value = search.value.trim();
  currentPage.value = 1;

  fetchJobSeekers();
};

// Clear Search
const handleClear = () => {
  if (loading.value) return;

  search.value = "";
  appliedSearch.value = "";
  currentPage.value = 1;

  fetchJobSeekers();
};

// Retry
const handleRetry = () => {
  fetchJobSeekers();
};

// Pagination
const handlePageChange = (page: number) => {
  if (
    loading.value ||
    page < 1 ||
    page > lastPage.value ||
    page === currentPage.value
  ) {
    return;
  }

  currentPage.value = page;
  fetchJobSeekers();
};

// Per Page
const handlePerPageChange = (value: number) => {
  if (loading.value || value === perPage.value) return;

  perPage.value = value;
  currentPage.value = 1;

  fetchJobSeekers();
};

// View Profile
const handleViewProfile = (id: number) => {
  router.push({
    name: "EmployerJobSeekerDetail",
    params: { id },
  });
};

// Direct Offer
const handleDirectOffer = (id: number) => {
  console.log("Direct offer:", id);
};

// Initial Fetch
onMounted(fetchJobSeekers);
</script>

<template>
  <div class="w-full min-w-0 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Job Seeker Profiles</h1>

      <p class="mt-1 text-sm text-slate-500">
        Discover and connect with talented candidates.
      </p>
    </div>

    <!-- Search Section -->
    <div
      class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center"
    >
      <!-- Search Input -->
      <div class="relative w-full min-w-0 flex-1">
        <Search
          :size="18"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
        />

        <input
          v-model="search"
          type="text"
          placeholder="Search by name, role, city or skill..."
          :disabled="loading"
          @keyup.enter="handleSearch"
          class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
        />
      </div>

      <!-- Common Search and Clear Buttons -->
      <BaseFilterActions
        :loading="loading"
        @search="handleSearch"
        @clear="handleClear"
      />
    </div>

    <!-- Result Count -->
    <div class="flex items-center gap-2 text-sm text-slate-500">
      <Users :size="17" />

      <span> {{ total }} candidates found </span>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="flex justify-center rounded-xl border border-slate-200 bg-white py-16"
    >
      <LoaderCircle :size="32" class="animate-spin text-blue-600" />
    </div>

    <!-- Error -->
    <div
      v-else-if="errorMessage"
      class="rounded-xl border border-red-100 bg-red-50 p-5 text-center text-sm text-red-600"
    >
      {{ errorMessage }}

      <button
        type="button"
        @click="handleRetry"
        class="ml-2 font-semibold underline"
      >
        Retry
      </button>
    </div>

    <!-- Job Seeker List -->
    <div v-else-if="jobSeekers.length > 0" class="space-y-4">
      <JobSeekerCard
        v-for="jobSeeker in jobSeekers"
        :key="jobSeeker.id"
        :job-seeker="jobSeeker"
        @view-profile="handleViewProfile"
        @direct-offer="handleDirectOffer"
      />
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-16 text-center"
    >
      <Users :size="36" class="mx-auto text-slate-300" />

      <h3 class="mt-3 font-semibold text-slate-800">No job seekers found</h3>

      <p class="mt-1 text-sm text-slate-500">
        Try searching with a different keyword.
      </p>
    </div>

    <!-- Pagination -->
    <BasePagination
      v-if="!loading && !errorMessage && total > 0"
      :current-page="currentPage"
      :last-page="lastPage"
      :total="total"
      :per-page="perPage"
      :loading="loading"
      @change="handlePageChange"
      @update:per-page="handlePerPageChange"
    />
  </div>
</template>
