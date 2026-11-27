<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  MapPin,
  Globe,
  Briefcase,
  Calendar,
  Users,
  ChevronDown,
  ChevronUp,
  Image as ImageIcon,
  ArrowRight,
  Mail,
  Phone,
  Bookmark,
  Banknote,
  Search,
  X,
} from "lucide-vue-next";
import { companyRepository } from "../../employer/repositories/companyRepository";
import type {
  Company,
  CompanyJobItem,
} from "../../employer/types/company.types";
import { useAuthStore } from "@/stores/auth.store";
import { useSavedJobs } from "@/features/jobs/composables/useSavedJobs";

const route = useRoute();
const router = useRouter();

const authStore = useAuthStore();

const { isSaved, toggleSavedJob, saveMutation, removeMutation, isJobSeeker } =
  useSavedJobs();

const isSaving = computed(
  () => saveMutation.isPending.value || removeMutation.isPending.value,
);

async function handleSaveJob(jobId: number) {
  // Guest → Login
  if (!authStore.isAuthenticated) {
    router.push({ name: "login" });
    return;
  }

  // Only Job Seeker can save jobs
  if (!isJobSeeker.value) {
    return;
  }

  try {
    await toggleSavedJob(jobId);
  } catch (error) {
    console.error("Failed to save job:", error);
  }
}

const companyJobs = ref<CompanyJobItem[]>([]);
const company = ref<Company | null>(null);
const loading = ref(true);
const error = ref("");

// Posted on date format
const formatPostedDate = (dateStr: string) => {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

//Click 'View Details' to go to the Job Detail Page.
const goToJobDetail = (jobId: number) => {
  router.push(`/jobs/${jobId}`);
};

const STORAGE_URL = "http://127.0.0.1:8000/storage/";

// Tabs State
const activeTab = ref<"about" | "jobs" | "gallery">("about");

// Read More & Gallery States
const isDescriptionExpanded = ref(false);
const isGalleryExpanded = ref(false);

//filter states
const searchKeyword = ref("");
const filterLocation = ref("");
const selectedJobType = ref("");
const selectedWorkplaceType = ref("");

const isSearchingJobs = ref(false);

// Fetch Company Jobs
const fetchCompanyJobs = async () => {
  try {
    isSearchingJobs.value = true;

    const id = Number(route.params.id);

    const data = await companyRepository.getCompanyJobs(id, {
      keyword: searchKeyword.value.trim() || undefined,
      location: filterLocation.value.trim() || undefined,
      employment_type: selectedJobType.value || undefined,
      workplace_type: selectedWorkplaceType.value || undefined,
    });

    console.log("Company Jobs API Response:", data);
    console.log("Jobs:", data.jobs);

    companyJobs.value = data.jobs ?? [];

    console.log("companyJobs ref:", companyJobs.value);

    companyJobs.value = data.jobs ?? [];
  } catch (error) {
    console.error("Failed to fetch company jobs:", error);
  } finally {
    isSearchingJobs.value = false;
  }
};

let searchTimer: ReturnType<typeof setTimeout>;

watch(
  [searchKeyword, filterLocation, selectedJobType, selectedWorkplaceType],
  () => {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(() => {
      fetchCompanyJobs();
    }, 400);
  },
);

//clear
const clearFilters = () => {
  searchKeyword.value = "";
  selectedJobType.value = "";
  selectedWorkplaceType.value = "";
  filterLocation.value = "";
};

// Fetch company detail & jobs
const fetchCompanyDetail = async () => {
  loading.value = true;
  error.value = "";
  try {
    const id = Number(route.params.id);
    //fetch company details
    const companyData = await companyRepository.getCompanyDetails(id);
    company.value = companyData.company;
    // Fetch Company Jobs
    await fetchCompanyJobs();
  } catch (err: any) {
    error.value = err.message || "Failed to load company details.";
  } finally {
    loading.value = false;
  }
};

const getImageUrl = (path: string | null) => {
  if (!path) return "";
  return path.startsWith("http") ? path : `${STORAGE_URL}${path}`;
};

// Description JSON string parsing and formatting
const parsedDescription = computed(() => {
  if (!company.value?.description || company.value.description === "[]") {
    return "No description provided by the company.";
  }
  try {
    const parsed = JSON.parse(company.value.description);
    if (Array.isArray(parsed) && parsed.length > 0) {
      const text = parsed
        .map((item: any) => item?.content || "")
        .filter(Boolean)
        .join("\n\n");
      return text || "No description provided by the company.";
    }
    return String(company.value.description);
  } catch {
    return String(company.value.description);
  }
});
const formatSalary = (job: CompanyJobItem) => {
  if (
    job.salary_type === "negotiable" ||
    (!job.salary_min && !job.salary_max)
  ) {
    return "Negotiable";
  }
  if (job.salary_min && job.salary_max) {
    return `${Number(job.salary_min).toLocaleString()} - ${Number(job.salary_max).toLocaleString()}`;
  }
  return `${Number(job.salary_min || job.salary_max).toLocaleString()}`;
};

const goBack = () => router.back();

const toggleDescription = () => {
  isDescriptionExpanded.value = !isDescriptionExpanded.value;
};

const toggleGallery = () => {
  isGalleryExpanded.value = !isGalleryExpanded.value;
};

const displayedGallery = computed(() => {
  if (!company.value?.gallery) return [];
  const urls = company.value.gallery.map((img) => getImageUrl(img));
  return isGalleryExpanded.value ? urls : urls.slice(0, 6);
});

const latestJobs = computed(() => companyJobs.value.slice(0, 4));

const latestPhotos = computed(() => {
  if (!company.value?.gallery) return [];
  return company.value.gallery.slice(0, 5).map((img) => getImageUrl(img));
});

const filteredJobs = computed(() => companyJobs.value);
// Check if any filter is active
const hasActiveFilters = computed(() => {
  return Boolean(
    searchKeyword.value.trim() ||
    selectedJobType.value ||
    selectedWorkplaceType.value ||
    filterLocation.value.trim(),
  );
});

onMounted(() => {
  fetchCompanyDetail();
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
      <!-- Back Button -->
      <button
        @click="goBack"
        class="mb-4 text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors"
      >
        &larr; Back
      </button>

      <!-- Loading & Error States -->
      <div v-if="loading" class="flex justify-center py-20">
        <div
          class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"
        ></div>
      </div>
      <div
        v-else-if="error"
        class="rounded-xl bg-red-50 p-6 text-center text-red-600"
      >
        {{ error }}
      </div>

      <div
        v-else-if="company"
        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
      >
        <!-- 1. Cover Image & Header Section -->
        <div class="relative w-full">
          <div class="h-48 w-full bg-slate-800 sm:h-64 relative">
            <img
              v-if="company.cover_image"
              :src="getImageUrl(company.cover_image)"
              class="w-full h-full object-cover"
              alt="Cover"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-r from-indigo-900 to-slate-900"
            ></div>
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"
            ></div>
          </div>

          <!-- Logo & Company Name (Overlapping) -->
          <div
            class="relative px-6 sm:px-8 -mt-16 sm:-mt-20 pb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4"
          >
            <div class="flex items-end gap-4 sm:gap-6">
              <!-- Logo -->
              <div
                class="h-28 w-28 sm:h-32 sm:w-32 rounded-xl border-4 border-white bg-white shadow-md shrink-0 overflow-hidden flex items-center justify-center"
              >
                <img
                  v-if="company.logo"
                  :src="getImageUrl(company.logo)"
                  alt="Logo"
                  class="h-full w-full object-contain p-2"
                />
                <Briefcase v-else class="h-10 w-10 text-gray-300" />
              </div>
              <!-- Title & Tagline -->
              <div class="mb-4">
                <h1
                  class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white line-clamp-2"
                >
                  {{ company.name }}
                </h1>
                <p
                  class="text-xs sm:text-sm md:text-base font-medium text-gray-700 mt-1 line-clamp-2"
                >
                  {{ company.tagline || "No tagline provided" }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Navigation Tabs -->
        <div class="border-y border-gray-200 bg-white px-6 sm:px-8">
          <nav class="flex space-x-8 -mb-px overflow-x-auto">
            <button
              @click="activeTab = 'about'"
              :class="[
                activeTab === 'about'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap pb-4 pt-4 px-1 border-b-2 font-medium text-sm transition-colors',
              ]"
            >
              About Company
            </button>
            <button
              @click="activeTab = 'jobs'"
              :class="[
                activeTab === 'jobs'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap pb-4 pt-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2',
              ]"
            >
              Jobs
              <span
                class="bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-bold"
                >{{ companyJobs.length }}</span
              >
            </button>
            <button
              @click="activeTab = 'gallery'"
              :class="[
                activeTab === 'gallery'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap pb-4 pt-4 px-1 border-b-2 font-medium text-sm transition-colors',
              ]"
            >
              Gallery
            </button>
          </nav>
        </div>

        <!-- 3. Tab Content Area -->
        <div class="p-6 sm:p-8">
          <!-- ================= ABOUT TAB ================= -->
          <div v-if="activeTab === 'about'" class="space-y-10">
            <!-- Our Story Section -->
            <div>
              <h2 class="text-xl font-bold text-gray-900 mb-4">
                About {{ company.name }}
              </h2>
              <div class="relative">
                <p
                  :class="[
                    isDescriptionExpanded ? '' : 'line-clamp-5',
                    'text-gray-600 leading-relaxed text-base whitespace-pre-line',
                  ]"
                >
                  {{ parsedDescription }}
                </p>
                <button
                  v-if="parsedDescription.length > 200"
                  @click="toggleDescription"
                  class="mt-3 text-sm font-semibold text-indigo-600 flex items-center gap-1 hover:text-indigo-800 transition-colors"
                >
                  {{ isDescriptionExpanded ? "Show Less" : "View More" }}
                  <ChevronUp v-if="isDescriptionExpanded" class="h-4 w-4" />
                  <ChevronDown v-else class="h-4 w-4" />
                </button>
              </div>
            </div>

            <!-- Full Width Details Section -->
            <div class="border-t border-gray-100 pt-8">
              <h3 class="text-lg font-bold text-gray-900 mb-5">
                Company Details
              </h3>

              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <!-- Industry -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Briefcase class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Industry
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.industry || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- Company Size -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Users class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Company Size
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.employee_count || "N/A" }} employees
                    </p>
                  </div>
                </div>

                <!-- Founded -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Calendar class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Founded
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.founded_year || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- City -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <MapPin class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      City
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.city || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- Address -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <MapPin class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Address
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.address || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Phone class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Phone
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.phone || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Mail class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Email
                    </p>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ company.company_email || "N/A" }}
                    </p>
                  </div>
                </div>

                <!-- Website -->
                <div class="flex items-start gap-3">
                  <div class="p-2.5 bg-indigo-50 rounded-lg shrink-0">
                    <Globe class="h-5 w-5 text-indigo-600" />
                  </div>
                  <div>
                    <p
                      class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1"
                    >
                      Website
                    </p>
                    <a
                      :href="company.website ?? undefined"
                      target="_blank"
                      class="text-sm font-semibold text-indigo-600 hover:underline truncate block"
                    >
                      {{
                        company.website
                          ? company.website.replace("https://", "")
                          : "N/A"
                      }}
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3 Latest Jobs Section -->
            <div class="border-t border-gray-100 pt-8">
              <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Latest Jobs</h3>
                <button
                  @click="activeTab = 'jobs'"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
                >
                  View All Jobs <ArrowRight class="h-4 w-4" />
                </button>
              </div>

              <div v-if="latestJobs.length === 0" class="text-sm text-gray-500">
                No open positions at the moment.
              </div>

              <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                  v-for="(job, index) in filteredJobs"
                  :key="job.id"
                  @click="goToJobDetail(job.id)"
                  class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5 cursor-pointer"
                >
                  <!-- Top Accent -->
                  <div
                    class="absolute left-0 top-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-cyan-400 transition-all duration-500 group-hover:w-full"
                  ></div>

                  <!-- Company & Job Title -->
                  <div class="flex items-start gap-4">
                    <!-- Company Logo -->
                    <div class="shrink-0">
                      <img
                        v-if="company.logo"
                        :src="getImageUrl(company.logo)"
                        :alt="company.name"
                        class="h-14 w-14 rounded-2xl border border-slate-100 bg-white object-cover p-1"
                      />

                      <div
                        v-else
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 text-xl font-bold text-white shadow-md"
                      >
                        {{ company.name.charAt(0).toUpperCase() }}
                      </div>
                    </div>

                    <!-- Job Information -->
                    <div class="min-w-0 flex-1">
                      <h3
                        class="line-clamp-2 text-lg font-bold leading-snug text-[#102D59] transition group-hover:text-blue-600"
                      >
                        {{ job.title }}
                      </h3>

                      <p
                        class="mt-1 truncate text-sm font-medium text-slate-500"
                      >
                        {{ company.name }}
                      </p>
                    </div>

                    <!-- Save Job Button -->
                    <button
                      v-if="!authStore.isAuthenticated || isJobSeeker"
                      type="button"
                      :disabled="isSaving"
                      @click.stop="handleSaveJob(job.id)"
                      class="shrink-0 flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition"
                      :class="
                        isSaved(job.id)
                          ? 'border-blue-200 bg-blue-50 text-blue-700'
                          : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700'
                      "
                    >
                      <Bookmark
                        class="h-4 w-4"
                        :fill="isSaved(job.id) ? 'currentColor' : 'none'"
                      />

                      {{ isSaved(job.id) ? "Saved" : "Save" }}
                    </button>
                  </div>

                  <!-- Job Tags -->
                  <div class="mt-5 flex flex-wrap items-center gap-2">
                    <span
                      class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 capitalize"
                    >
                      {{ job.employment_type.replace("_", " ") }}
                    </span>

                    <span
                      class="rounded-full bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700 capitalize"
                    >
                      {{ job.workplace_type }}
                    </span>

                    <span
                      v-if="job.vacancies"
                      class="rounded-full bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-700"
                    >
                      {{ job.vacancies }}
                      {{ job.vacancies > 1 ? "Positions" : "Position" }}
                    </span>
                  </div>

                  <!-- Job Details -->
                  <div class="mt-6 space-y-4">
                    <!-- Location -->
                    <div class="flex items-center gap-3 text-sm text-slate-600">
                      <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-blue-600"
                      >
                        <MapPin class="h-5 w-5" />
                      </div>

                      <span class="min-w-0 truncate">
                        {{
                          [job.city, job.country].filter(Boolean).join(", ") ||
                          "Location not specified"
                        }}
                      </span>
                    </div>

                    <!-- Salary -->
                    <div class="flex items-center gap-3 text-sm">
                      <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600"
                      >
                        <Banknote class="h-5 w-5" />
                      </div>

                      <span class="font-semibold text-slate-800">
                        {{ formatSalary(job) }}
                      </span>
                    </div>
                  </div>

                  <!-- Footer -->
                  <div
                    class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-5 mt-6"
                  >
                    <!-- Posted Date -->
                    <div>
                      <p class="text-xs text-slate-400">Posted on</p>

                      <p class="mt-1 text-sm font-medium text-slate-600">
                        {{ formatPostedDate(job.created_at) }}
                      </p>
                    </div>

                    <!-- View Details -->
                    <button
                      @click.stop="goToJobDetail(job.id)"
                      class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:bg-blue-600 hover:shadow-lg shrink-0"
                    >
                      View Details

                      <ArrowRight
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                      />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 5 Latest Photos Section -->
            <div
              v-if="latestPhotos.length > 0"
              class="border-t border-gray-100 pt-8"
            >
              <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Latest Photos</h3>
                <button
                  @click="activeTab = 'gallery'"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
                >
                  View All Photos <ArrowRight class="h-4 w-4" />
                </button>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                <div
                  v-for="(img, i) in latestPhotos"
                  :key="i"
                  class="aspect-square rounded-lg overflow-hidden bg-gray-100 group cursor-pointer"
                  @click="activeTab = 'gallery'"
                >
                  <img
                    :src="img"
                    alt="Latest Photo"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- ================= JOBS TAB ================= -->
          <div v-if="activeTab === 'jobs'" class="block w-full space-y-6">
            <!-- Header Row -->
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-900">
                All Open Positions
              </h2>
              <span class="text-sm text-gray-500">
                Showing {{ filteredJobs.length }} of
                {{ companyJobs.length }} jobs
              </span>
            </div>

            <div
              class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center"
            >
              <!-- 1. Keyword Search (3 columns) -->
              <div class="relative lg:col-span-3">
                <Search
                  class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                />
                <input
                  v-model="searchKeyword"
                  type="text"
                  placeholder="Job title (e.g. UIUX)..."
                  class="w-full pl-9 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <!-- 2. Location Filter (3 columns) -->
              <div class="relative lg:col-span-3">
                <MapPin
                  class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                />
                <input
                  v-model="filterLocation"
                  type="text"
                  placeholder="City or Country..."
                  class="w-full pl-9 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <!-- 3. Employment Type Filter (2 columns) -->
              <div class="lg:col-span-2">
                <select
                  v-model="selectedJobType"
                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="">All Job Types</option>
                  <option value="full_time">Full Time</option>
                  <option value="part_time">Part Time</option>
                  <option value="contract">Contract</option>
                  <option value="internship">Internship</option>
                </select>
              </div>

              <!-- 4. Workplace Type Filter (2 columns) -->
              <div class="lg:col-span-2">
                <select
                  v-model="selectedWorkplaceType"
                  class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="">All Workplaces</option>
                  <option value="onsite">Onsite</option>
                  <option value="remote">Remote</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>

              <!-- 5. Clear Button (2 columns) -->
              <div class="lg:col-span-2">
                <button
                  type="button"
                  @click="clearFilters"
                  :disabled="!hasActiveFilters"
                  :class="[
                    hasActiveFilters
                      ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100 cursor-pointer'
                      : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed',
                    'w-full inline-flex items-center justify-center gap-1.5 text-sm font-medium py-2 px-3 rounded-lg border transition-colors',
                  ]"
                >
                  <X class="h-4 w-4" />
                  Clear
                </button>
              </div>
            </div>

            <!-- Empty State -->
            <div
              v-if="filteredJobs.length === 0"
              class="py-12 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200"
            >
              <p class="text-gray-500 text-sm">
                No open positions match your search criteria.
              </p>
              <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800 underline"
              >
                Clear all filters
              </button>
            </div>

            <!-- 2-Column Job Cards Grid -->
            <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
              <div
                v-for="(job, index) in filteredJobs"
                :key="job.id"
                @click="goToJobDetail(job.id)"
                class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5 cursor-pointer"
              >
                <!-- Top Accent -->
                <div
                  class="absolute left-0 top-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-cyan-400 transition-all duration-500 group-hover:w-full"
                ></div>

                <!-- Company & Job Title -->
                <div class="flex items-start gap-4">
                  <!-- Company Logo -->
                  <div class="shrink-0">
                    <img
                      v-if="company.logo"
                      :src="getImageUrl(company.logo)"
                      :alt="company.name"
                      class="h-14 w-14 rounded-2xl border border-slate-100 bg-white object-cover p-1"
                    />

                    <div
                      v-else
                      class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 text-xl font-bold text-white shadow-md"
                    >
                      {{ company.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>

                  <!-- Job Information -->
                  <div class="min-w-0 flex-1">
                    <h3
                      class="line-clamp-2 text-lg font-bold leading-snug text-[#102D59] transition group-hover:text-blue-600"
                    >
                      {{ job.title }}
                    </h3>

                    <p class="mt-1 truncate text-sm font-medium text-slate-500">
                      {{ company.name }}
                    </p>
                  </div>

                  <!-- Save Job Button -->
                  <button
                    v-if="!authStore.isAuthenticated || isJobSeeker"
                    type="button"
                    :disabled="isSaving"
                    @click.stop="handleSaveJob(job.id)"
                    class="shrink-0 flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition"
                    :class="
                      isSaved(job.id)
                        ? 'border-blue-200 bg-blue-50 text-blue-700'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700'
                    "
                  >
                    <Bookmark
                      class="h-4 w-4"
                      :fill="isSaved(job.id) ? 'currentColor' : 'none'"
                    />

                    {{ isSaved(job.id) ? "Saved" : "Save" }}
                  </button>
                </div>

                <!-- Job Tags -->
                <div class="mt-5 flex flex-wrap items-center gap-2">
                  <span
                    class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 capitalize"
                  >
                    {{ job.employment_type.replace("_", " ") }}
                  </span>

                  <span
                    class="rounded-full bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700 capitalize"
                  >
                    {{ job.workplace_type }}
                  </span>

                  <span
                    v-if="job.vacancies"
                    class="rounded-full bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-700"
                  >
                    {{ job.vacancies }}
                    {{ job.vacancies > 1 ? "Positions" : "Position" }}
                  </span>
                </div>

                <!-- Job Details -->
                <div class="mt-6 space-y-4">
                  <!-- Location -->
                  <div class="flex items-center gap-3 text-sm text-slate-600">
                    <div
                      class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-blue-600"
                    >
                      <MapPin class="h-5 w-5" />
                    </div>

                    <span class="min-w-0 truncate">
                      {{
                        [job.city, job.country].filter(Boolean).join(", ") ||
                        "Location not specified"
                      }}
                    </span>
                  </div>

                  <!-- Salary -->
                  <div class="flex items-center gap-3 text-sm">
                    <div
                      class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600"
                    >
                      <Banknote class="h-5 w-5" />
                    </div>

                    <span class="font-semibold text-slate-800">
                      {{ formatSalary(job) }}
                    </span>
                  </div>
                </div>

                <!-- Footer -->
                <div
                  class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-5 mt-6"
                >
                  <!-- Posted Date -->
                  <div>
                    <p class="text-xs text-slate-400">Posted on</p>

                    <p class="mt-1 text-sm font-medium text-slate-600">
                      {{ formatPostedDate(job.created_at) }}
                    </p>
                  </div>

                  <!-- View Details -->
                  <button
                    @click.stop="goToJobDetail(job.id)"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:bg-blue-600 hover:shadow-lg shrink-0"
                  >
                    View Details

                    <ArrowRight
                      class="h-4 w-4 transition-transform group-hover:translate-x-1"
                    />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= GALLERY TAB ================= -->
          <div v-if="activeTab === 'gallery'">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-gray-900">
                Life at {{ company.name }}
              </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <div
                v-for="(img, i) in displayedGallery"
                :key="i"
                class="aspect-square rounded-xl overflow-hidden bg-gray-100 group"
              >
                <img
                  :src="img"
                  alt="Gallery Image"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>

              <div
                v-if="displayedGallery.length === 0"
                class="col-span-full py-12 text-center text-gray-500 flex flex-col items-center"
              >
                <ImageIcon class="h-10 w-10 text-gray-300 mb-2" />
                No photos available yet.
              </div>
            </div>

            <div
              v-if="company.gallery && company.gallery.length > 6"
              class="mt-8 text-center"
            >
              <button
                @click="toggleGallery"
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors inline-flex items-center gap-2"
              >
                {{ isGalleryExpanded ? "Show Less" : "View All Photos" }}
                <ChevronUp v-if="isGalleryExpanded" class="h-4 w-4" />
                <ChevronDown v-else class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
