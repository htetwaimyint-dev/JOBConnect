<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
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

const route = useRoute();
const router = useRouter();

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

// Fetch company detail & jobs
const fetchCompanyDetail = async () => {
  loading.value = true;
  error.value = "";
  try {
    const id = Number(route.params.id);
    const data = await companyRepository.getCompanyJobs(id);

    company.value = data.company;
    companyJobs.value = data.jobs?.data || [];
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

// Description JSON string ကို စာသားအဖြစ် ပြောင်းရန် (Error ကင်းစေရန် ပြင်ထားပါသည်)
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

const latestJobs = computed(() => companyJobs.value.slice(0, 3));

const latestPhotos = computed(() => {
  if (!company.value?.gallery) return [];
  return company.value.gallery.slice(0, 5).map((img) => getImageUrl(img));
});

// Filter States
const isSearchingJobs = ref(false);

// Filter States
const searchKeyword = ref("");
const selectedJobType = ref("");
const selectedWorkplaceType = ref(""); // <-- ထပ်ဖြည့်ပါ
const filterLocation = ref("");

// စာသားထဲက အထူးသင်္ကေတ (/ , - , space) များကို ဖယ်ရှားပြီး ရှာဖွေနိုင်ရန် Helper
const normalizeText = (text: string) => {
  return text.toLowerCase().replace(/[^a-z0-9]/g, "");
};

// Real-time Instant Filter
const filteredJobs = computed(() => {
  return companyJobs.value.filter((job) => {
    // 1. Keyword Search (Smart Match: "UIUX" -> "UI/UX")
    const keyword = searchKeyword.value.trim();
    const matchesKeyword =
      !keyword ||
      job.title.toLowerCase().includes(keyword.toLowerCase()) ||
      normalizeText(job.title).includes(normalizeText(keyword));

    // 2. Location Search (City သို့မဟုတ် Country)
    const loc = filterLocation.value.trim().toLowerCase();
    const matchesLocation =
      !loc ||
      (job.city && job.city.toLowerCase().includes(loc)) ||
      (job.country && job.country.toLowerCase().includes(loc));

    // 3. Employment Type Filter
    const matchesJobType =
      !selectedJobType.value || job.employment_type === selectedJobType.value;

    // 4. Workplace Type Filter (Onsite, Remote, Hybrid)
    const matchesWorkplaceType =
      !selectedWorkplaceType.value ||
      job.workplace_type === selectedWorkplaceType.value;

    return (
      matchesKeyword &&
      matchesLocation &&
      matchesJobType &&
      matchesWorkplaceType
    );
  });
});

// Filter အားလုံးကို ရှင်းလင်းရန် (Clear Button)
const clearFilters = () => {
  searchKeyword.value = "";
  selectedJobType.value = "";
  selectedWorkplaceType.value = ""; // <-- ထပ်ဖြည့်ပါ
  filterLocation.value = "";
};

// Filter တစ်ခုခု ရွေးထားခြင်း ရှိ/မရှိ စစ်ဆေးရန်
const hasActiveFilters = computed(() => {
  return Boolean(
    searchKeyword.value.trim() ||
    selectedJobType.value ||
    selectedWorkplaceType.value || // <-- ထပ်ဖြည့်ပါ
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
        &larr; Back to Companies
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
              <div class="mb-3">
                <h1
                  class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white line-clamp-2"
                >
                  {{ company.name }}
                </h1>
                <p
                  class="text-xs sm:text-sm md:text-base font-medium text-gray-200 mt-1 line-clamp-2"
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
                      :href="company.website"
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

              <div v-else class="space-y-3">
                <div
                  v-for="job in latestJobs"
                  :key="job.id"
                  class="group flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition-all bg-white cursor-pointer"
                >
                  <div>
                    <h4
                      class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors"
                    >
                      {{ job.title }}
                    </h4>
                    <div
                      class="flex items-center gap-3 mt-1.5 text-xs text-gray-500"
                    >
                      <span class="flex items-center gap-1">
                        <MapPin class="h-3.5 w-3.5" /> {{ job.city }},
                        {{ job.country }}
                      </span>
                      <span class="flex items-center gap-1 capitalize">
                        <Briefcase class="h-3.5 w-3.5" />
                        {{ job.employment_type.replace("_", " ") }}
                      </span>
                    </div>
                  </div>
                  <div class="mt-3 sm:mt-0 flex items-center gap-4">
                    <p class="text-sm font-semibold text-indigo-600 capitalize">
                      {{
                        job.salary_min && job.salary_max
                          ? `${Number(job.salary_min).toLocaleString()} - ${Number(job.salary_max).toLocaleString()}`
                          : job.salary_type
                      }}
                    </p>
                    <button
                      class="p-1.5 rounded-full bg-indigo-50 text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <ArrowRight class="h-4 w-4" />
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

            <!-- ၃။ Filter Bar ၏ အောက်တွင် Empty State နှင့် Job Cards Grid ရှိရပါမည် -->

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
                class="relative bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all cursor-pointer flex flex-col justify-between"
              >
                <!-- Top-Right Index Badge -->
                <span
                  class="absolute -top-2.5 -right-2.5 h-7 w-7 rounded-full bg-sky-500 text-white text-xs font-bold flex items-center justify-center shadow"
                >
                  {{ String(index + 1).padStart(2, "0") }}
                </span>

                <div>
                  <!-- Card Header -->
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3.5 min-w-0">
                      <div
                        class="h-12 w-12 rounded-full border border-gray-200 bg-gray-50 overflow-hidden shrink-0 flex items-center justify-center"
                      >
                        <img
                          v-if="company.logo"
                          :src="getImageUrl(company.logo)"
                          :alt="company.name"
                          class="h-full w-full object-contain p-1.5"
                        />
                        <Briefcase v-else class="h-5 w-5 text-gray-400" />
                      </div>
                      <div class="min-w-0">
                        <h3
                          class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition-colors truncate"
                        >
                          {{ job.title }}
                        </h3>
                        <p class="text-sm text-gray-500 font-medium truncate">
                          {{ company.name }}
                        </p>
                      </div>
                    </div>

                    <button
                      @click.stop
                      class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors shrink-0"
                    >
                      <Bookmark class="h-3.5 w-3.5" />
                      Save
                    </button>
                  </div>

                  <!-- Badges -->
                  <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span
                      class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 capitalize"
                    >
                      {{ job.employment_type.replace("_", " ") }}
                    </span>
                    <span
                      class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 capitalize"
                    >
                      {{ job.workplace_type }}
                    </span>
                    <span
                      v-if="job.vacancies"
                      class="px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-600"
                    >
                      {{ job.vacancies }}
                      {{ job.vacancies > 1 ? "Positions" : "Position" }}
                    </span>
                  </div>

                  <!-- Location & Salary -->
                  <div class="mt-5 space-y-2.5 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                      <MapPin class="h-4 w-4 text-sky-500 shrink-0" />
                      <span>{{ job.city }}, {{ job.country }}</span>
                    </div>
                    <div
                      class="flex items-center gap-2 font-semibold text-gray-800 capitalize"
                    >
                      <Banknote class="h-4 w-4 text-emerald-500 shrink-0" />
                      <span>
                        {{
                          job.salary_min && job.salary_max
                            ? `${Number(job.salary_min).toLocaleString()} - ${Number(job.salary_max).toLocaleString()}`
                            : job.salary_type
                        }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Card Footer -->
                <div
                  class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between"
                >
                  <div>
                    <p class="text-[11px] text-gray-400">Posted on</p>
                    <p class="text-xs font-semibold text-gray-700">
                      {{ formatPostedDate(job.created_at) }}
                    </p>
                  </div>

                  <button
                    @click.stop="goToJobDetail(job.id)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#1e293b] hover:bg-indigo-600 text-white text-xs font-semibold transition-colors shrink-0"
                  >
                    View Details
                    <ArrowRight class="h-3.5 w-3.5" />
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
