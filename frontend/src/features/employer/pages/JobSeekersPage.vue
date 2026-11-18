<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { Search, Users, LoaderCircle, RefreshCw } from "lucide-vue-next";

import JobSeekerCard from "../components/JobSeekerCard.vue";
import jobSeekerRepository from "../repositories/jobSeekerRepository.ts";
import BasePagination from "@/components/common/BasePagination.vue";

import type { JobSeeker } from "../types/job-seeker.types.ts";

const router = useRouter();

const jobSeekers = ref<JobSeeker[]>([]);

const search = ref("");
const perPage = ref(5);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const loading = ref(false);
const errorMessage = ref("");

const fetchJobSeekers = async () => {
  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await jobSeekerRepository.getJobSeekers({
      search: search.value.trim() || undefined,
      per_page: perPage.value,
      page: currentPage.value,
    });

    jobSeekers.value = response.data;
    currentPage.value = response.meta.current_page;
    lastPage.value = response.meta.last_page;
    total.value = response.meta.total;
  } catch (error: any) {
    console.error("Failed to fetch job seekers:", error);
    console.error("API Error Response:", error.response?.data);
    console.error("API Status:", error.response?.status);

    errorMessage.value = "Unable to load job seekers. Please try again.";
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  currentPage.value = 1;
  fetchJobSeekers();
};

const handlePageChange = (page: number) => {
  currentPage.value = page;
  fetchJobSeekers();
};

const handlePerPageChange = (value: number) => {
  perPage.value = value;
  currentPage.value = 1;
  fetchJobSeekers();
};

const handleViewProfile = (id: number) => {
  router.push({
    name: "EmployerJobSeekerDetail",
    params: { id },
  });
};

const handleDirectOffer = (id: number) => {
  console.log("Direct offer:", id);
};

onMounted(fetchJobSeekers);
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Job Seeker Profiles</h1>

      <p class="mt-1 text-sm text-slate-500">
        Discover and connect with talented candidates.
      </p>
    </div>

    <!-- Search and Refresh -->
    <div
      class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="relative w-full sm:max-w-md">
        <Search
          :size="18"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
        />

        <input
          v-model="search"
          @keyup.enter="handleSearch"
          type="text"
          placeholder="Search by name, role, city or skill..."
          class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
        />
      </div>

      <button
        type="button"
        @click="fetchJobSeekers"
        :disabled="loading"
        class="mr-auto flex items-center justify-center gap-2 self-end rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-50 sm:self-auto"
      >
        <RefreshCw :size="16" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </div>

    <!-- Result Count -->
    <div class="flex items-center gap-2 text-sm text-slate-500">
      <Users :size="17" />
      <span>{{ total }} candidates found</span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center rounded-xl bg-white py-16">
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
        @click="fetchJobSeekers"
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
      v-if="!loading && !errorMessage"
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
