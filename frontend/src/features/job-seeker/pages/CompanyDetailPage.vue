<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  MapPin,
  Globe,
  Briefcase,
  Calendar,
  Users,
  CheckCircle2,
  ChevronDown,
  ChevronUp,
  Image as ImageIcon,
  ArrowRight,
  Mail,
  Phone,
} from "lucide-vue-next";
import { companyRepository } from "../../employer/repositories/companyRepository";
import type { Company } from "../../employer/types/company.types";

const route = useRoute();
const router = useRouter();

const company = ref<Company | null>(null);
const loading = ref(true);
const error = ref("");

// Tabs State
const activeTab = ref<"about" | "jobs" | "gallery">("about");

// Read More & Gallery States
const isDescriptionExpanded = ref(false);
const isGalleryExpanded = ref(false);

// Mock Jobs Data
const companyJobs = ref([
  {
    id: 1,
    title: "Senior AI Engineer",
    location: "San Francisco, CA",
    type: "Full-time",
    salary: "$120k - $150k",
    posted: "2 days ago",
  },
  {
    id: 2,
    title: "Frontend Developer (Vue.js)",
    location: "Remote",
    type: "Contract",
    salary: "$80k - $110k",
    posted: "5 days ago",
  },
  {
    id: 3,
    title: "Product Manager",
    location: "New York, NY",
    type: "Full-time",
    salary: "$100k - $130k",
    posted: "1 week ago",
  },
  {
    id: 4,
    title: "UX/UI Designer",
    location: "London, UK",
    type: "Full-time",
    salary: "$70k - $90k",
    posted: "2 weeks ago",
  },
]);

// Fetch company detail
const fetchCompanyDetail = async () => {
  loading.value = true;
  try {
    const id = Number(route.params.id);
    const data = await companyRepository.getCompanyById(id);
    company.value = data;
  } catch (err: any) {
    error.value = "Failed to load company details.";
  } finally {
    loading.value = false;
  }
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
  return isGalleryExpanded.value
    ? company.value.gallery
    : company.value.gallery.slice(0, 6);
});

const latestJobs = computed(() => companyJobs.value.slice(0, 3));

const latestPhotos = computed(() => {
  if (!company.value?.gallery) return [];
  return company.value.gallery.slice(0, 5);
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

      <!-- Company Profile Container -->
      <div
        v-else-if="company"
        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
      >
        <!-- 1. Cover Image & Header Section -->
        <div class="relative w-full">
          <!-- Cover Image: ပုံကို လင်းလက်စေဖို့ opacity မထည့်တော့ပါဘူး -->
          <div class="h-48 w-full bg-slate-800 sm:h-64 relative">
            <img
              v-if="company.cover_image"
              :src="company.cover_image"
              class="w-full h-full object-cover"
              alt="Cover"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-r from-indigo-900 to-slate-900"
            ></div>

            <!-- Gradient: အောက်ခြေမှာပဲ အနည်းငယ် မှောင်ပေးထားတယ် (စာသားဖတ်ရလွယ်ဖို့) -->
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"
            ></div>

            <!-- Verified Badge -->
            <div class="absolute top-4 right-4 flex items-center gap-2">
              <span
                v-if="company.verification_status === 'verified'"
                class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 shadow-md"
              >
                <CheckCircle2 class="h-4 w-4" /> Verified
              </span>
            </div>
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
                  :src="company.logo"
                  alt="Logo"
                  class="h-full w-full object-contain p-2"
                />
                <Briefcase v-else class="h-10 w-10 text-gray-300" />
              </div>
              <!-- Title & Tagline -->
              <div class="mb-5">
                <h1
                  class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white line-clamp-2"
                >
                  {{ company.name }}
                </h1>
                <p
                  class="text-xs sm:text-sm md:text-base font-medium text-black mt-1 line-clamp-2"
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
                    'text-gray-600 leading-relaxed text-base',
                  ]"
                >
                  {{
                    company.description && company.description !== "[]"
                      ? company.description
                      : "No description provided by the company."
                  }}
                </p>
                <button
                  v-if="company.description && company.description.length > 200"
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

              <div class="space-y-3">
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
                      <span class="flex items-center gap-1"
                        ><MapPin class="h-3.5 w-3.5" /> {{ job.location }}</span
                      >
                      <span class="flex items-center gap-1"
                        ><Briefcase class="h-3.5 w-3.5" /> {{ job.type }}</span
                      >
                    </div>
                  </div>
                  <div class="mt-3 sm:mt-0 flex items-center gap-4">
                    <p class="text-sm font-semibold text-gray-900">
                      {{ job.salary }}
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
          <div v-if="activeTab === 'jobs'" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold text-gray-900">
                All Open Positions
              </h2>
              <span class="text-sm text-gray-500"
                >Showing {{ companyJobs.length }} jobs</span
              >
            </div>

            <div
              v-for="job in companyJobs"
              :key="job.id"
              class="group border border-gray-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition-all cursor-pointer bg-white"
            >
              <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
              >
                <div>
                  <h3
                    class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors"
                  >
                    {{ job.title }}
                  </h3>
                  <div
                    class="flex items-center gap-4 mt-2 text-sm text-gray-500"
                  >
                    <span class="flex items-center gap-1"
                      ><MapPin class="h-4 w-4" /> {{ job.location }}</span
                    >
                    <span class="flex items-center gap-1"
                      ><Briefcase class="h-4 w-4" /> {{ job.type }}</span
                    >
                  </div>
                </div>
                <div class="flex items-center gap-4 sm:text-right">
                  <div>
                    <p class="text-sm font-bold text-gray-900">
                      {{ job.salary }}
                    </p>
                    <p class="text-xs text-gray-500">{{ job.posted }}</p>
                  </div>
                  <button
                    class="p-2 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors"
                  >
                    <ArrowRight class="h-5 w-5" />
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
n
