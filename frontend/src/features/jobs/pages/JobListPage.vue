<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

import {
  Users,
  AlertCircle,
  BriefcaseBusiness,
  ChevronLeft,
  ChevronRight,
} from "lucide-vue-next";

import JobSearch from "../components/JobSearch.vue";
import JobCard from "../components/JobCard.vue";

import { useJobs } from "../composables/useJobs";
import type { JobListParams } from "../types/job.types";

import AppSectionContainer from "@/components/layout/AppSectionContainer.vue";

const route = useRoute();
const router = useRouter();

const search = ref(
  typeof route.query.search === "string" ? route.query.search : "",
);

const city = ref("");
const country = ref("");
const currentPage = ref(1);

const categoryId = ref(
  typeof route.query.category_id === "string" &&
    Number.isInteger(Number(route.query.category_id)) &&
    Number(route.query.category_id) > 0
    ? Number(route.query.category_id)
    : undefined,
);

const companyId = ref(
  typeof route.query.company_id === "string" &&
    Number.isInteger(Number(route.query.company_id)) &&
    Number(route.query.company_id) > 0
    ? Number(route.query.company_id)
    : undefined,
);

const params = computed<JobListParams>(() => ({
  page: currentPage.value,
  search: search.value || undefined,
  company_id: companyId.value,
  category_id: categoryId.value,
  city: city.value || undefined,
  country: country.value || undefined,
}));

const { getJobs } = useJobs();
const jobsQuery = getJobs(params);

const jobs = computed(() => jobsQuery.data.value?.data ?? []);
const pagination = computed(() => jobsQuery.data.value?.meta);

const handleSearch = (value: {
  search: string;
  city: string;
  country: string;
}) => {
  search.value = value.search;
  city.value = value.city;
  country.value = value.country;
  currentPage.value = 1;

  router.replace({
    query: {
      ...route.query,
      search: value.search || undefined,
      city: value.city || undefined,
      country: value.country || undefined,
      category_id: categoryId.value ? String(categoryId.value) : undefined,
      company_id: companyId.value ? String(companyId.value) : undefined,
    },
  });
};

watch(
  () => route.query.search,
  (value) => {
    search.value = typeof value === "string" ? value : "";
    currentPage.value = 1;
  },
);

watch(
  () => route.query.category_id,
  (value) => {
    const id = typeof value === "string" ? Number(value) : undefined;

    categoryId.value = id && Number.isInteger(id) && id > 0 ? id : undefined;

    currentPage.value = 1;
  },
);

const changePage = (page: number) => {
  if (page < 1 || page > (pagination.value?.last_page ?? 1)) {
    return;
  }

  currentPage.value = page;
};
</script>

<template>
  <div class="w-full min-w-0 bg-slate-50">
    <!-- Hero -->
    <section class="bg-[#102D59]">
      <AppSectionContainer width="wide" class="py-14 sm:py-16 lg:py-20">
        <div class="max-w-3xl">
          <p
            class="text-sm font-semibold uppercase tracking-wider text-blue-300"
          >
            Explore Career Opportunities
          </p>

          <h1
            class="mt-4 text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl"
          >
            Find a Job That
            <span class="text-cyan-300"> Moves You Forward.</span>
          </h1>

          <p
            class="mt-4 max-w-2xl text-sm leading-7 text-blue-100 sm:text-base"
          >
            Discover meaningful opportunities, explore leading companies, and
            take the next step in your career with JobConnect.
          </p>
        </div>
      </AppSectionContainer>
    </section>

    <!-- Job Content -->
    <section class="pb-16 sm:pb-20">
      <AppSectionContainer width="wide">
        <!-- Search -->
        <div
          class="-mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-900/5 sm:p-7"
        >
          <div class="mb-5">
            <h2 class="text-lg font-bold text-[#102D59]">Find Your Next Job</h2>

            <p class="mt-1 text-sm text-slate-500">
              Search by job title, keyword, or location.
            </p>
          </div>

          <JobSearch
            :search="search"
            :city="city"
            :country="country"
            @search="handleSearch"
          />
        </div>

        <!-- Results Header -->
        <div
          class="mb-6 mt-10 flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
              Available Jobs
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              Explore opportunities that match your career goals.
            </p>
          </div>

          <div
            v-if="pagination"
            class="flex w-fit items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700"
          >
            <Users class="h-4 w-4" />
            {{ pagination.total }} jobs found
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="jobsQuery.isLoading.value"
          class="rounded-xl border border-slate-200 bg-white px-5 py-16 text-center"
        >
          <div
            class="mx-auto h-9 w-9 animate-spin rounded-full border-4 border-blue-100 border-t-blue-600"
          ></div>

          <p class="mt-4 text-sm font-medium text-slate-600">
            Finding available jobs...
          </p>
        </div>

        <!-- Error -->
        <div
          v-else-if="jobsQuery.isError.value"
          class="rounded-xl border border-slate-200 bg-white px-5 py-14 text-center"
        >
          <AlertCircle class="mx-auto h-10 w-10 text-red-500" />

          <h3 class="mt-4 font-semibold text-slate-900">Unable to load jobs</h3>

          <p class="mt-2 text-sm text-slate-500">
            Something went wrong. Please try again.
          </p>

          <button
            type="button"
            class="mt-5 rounded-lg bg-[#102D59] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
            @click="jobsQuery.refetch()"
          >
            Try Again
          </button>
        </div>

        <!-- Empty -->
        <div
          v-else-if="jobs.length === 0"
          class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-16 text-center"
        >
          <BriefcaseBusiness class="mx-auto h-10 w-10 text-slate-400" />

          <h3 class="mt-4 font-semibold text-slate-900">No jobs found</h3>

          <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
            Try different keywords or change your location to find more
            opportunities.
          </p>
        </div>

        <!-- Job Cards -->
        <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <JobCard v-for="job in jobs" :key="job.id" :job="job" />
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination && pagination.last_page > 1"
          class="mt-8 flex flex-col items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 sm:flex-row"
        >
          <p class="text-sm text-slate-500">
            Page
            <span class="font-semibold text-slate-900">
              {{ pagination.current_page }}
            </span>
            of
            <span class="font-semibold text-slate-900">
              {{ pagination.last_page }}
            </span>
          </p>

          <div class="flex items-center gap-2">
            <button
              type="button"
              :disabled="currentPage <= 1 || jobsQuery.isFetching.value"
              class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
              @click="changePage(currentPage - 1)"
            >
              <ChevronLeft class="h-4 w-4" />
              Previous
            </button>

            <button
              type="button"
              :disabled="
                currentPage >= pagination.last_page ||
                jobsQuery.isFetching.value
              "
              class="inline-flex items-center gap-2 rounded-lg bg-[#102D59] px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-40"
              @click="changePage(currentPage + 1)"
            >
              Next
              <ChevronRight class="h-4 w-4" />
            </button>
          </div>
        </div>
      </AppSectionContainer>
    </section>
  </div>
</template>
