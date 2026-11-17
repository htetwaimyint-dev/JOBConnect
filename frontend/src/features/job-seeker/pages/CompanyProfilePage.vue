<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import {
  MapPin,
  Globe,
  Briefcase,
  CheckCircle2,
  ChevronRight,
  Building2,
  Users,
} from "lucide-vue-next";

import Search from "@/components/layout/Search.vue";
import { companyRepository } from "../../employer/repositories/companyRepository";
import type { Company } from "../../employer/types/company.types";

const router = useRouter();

// Data State
const companies = ref<Company[]>([]);
const loading = ref(false);
const searchQuery = ref("");
const currentPage = ref(1);

const fetchCompanies = async () => {
  loading.value = true;
  try {
    const data = await companyRepository.getCompanies(searchQuery.value);
    companies.value = data;
  } catch (error) {
    console.error("Failed to fetch companies:", error);
  } finally {
    loading.value = false;
  }
};

watch(searchQuery, () => {
  currentPage.value = 1;
  fetchCompanies();
});

const goToProfile = (id: number) => {
  router.push(`/companies/${id}`);
};

onMounted(() => {
  fetchCompanies();
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-7xl">
      <!-- Common Layout (Header + Search) -->
      <Search
        title="Explore Companies"
        subtitle="Discover great places to work and find your next opportunity."
        search-placeholder="Search by company name or industry..."
        v-model:searchQuery="searchQuery"
      />

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <div
          class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"
        ></div>
      </div>

      <!-- Cards Grid -->
      <div
        v-else-if="companies.length > 0"
        class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 sm:gap-6"
      >
        <div
          v-for="company in companies"
          :key="company.id"
          class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl cursor-pointer"
          @click="goToProfile(company.id)"
        >
          <!-- Cover Image -->
          <div class="relative h-28 w-full bg-slate-200 sm:h-32">
            <img
              v-if="company.cover_image"
              :src="company.cover_image"
              alt="Cover"
              class="h-full w-full object-cover opacity-90 group-hover:opacity-100 transition-opacity"
            />
            <div
              v-else
              class="h-full w-full bg-gradient-to-r from-blue-900 to-slate-900"
            ></div>
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"
            ></div>
          </div>

          <!-- Card Body -->
          <div class="relative flex flex-1 flex-col px-4 pb-5 pt-0 sm:px-5">
            <!-- Logo -->
            <div class="-mt-9 mb-3 flex items-end justify-between">
              <div
                class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border-[3px] border-white bg-white shadow-md"
              >
                <img
                  v-if="company.logo"
                  :src="company.logo"
                  alt="Logo"
                  class="h-full w-full object-contain p-1"
                />
                <Building2 v-else class="h-8 w-8 text-gray-400" />
              </div>
            </div>

            <!-- Company Info -->
            <div>
              <h2
                class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors sm:text-xl"
              >
                {{ company.name }}
              </h2>
              <p class="mt-1 text-xs text-gray-500 line-clamp-2 sm:text-sm">
                {{ company.tagline || "No tagline provided" }}
              </p>
            </div>

            <!-- Meta Info -->
            <div class="mt-4 space-y-2.5 text-xs text-gray-600 sm:text-sm">
              <div class="flex items-center gap-2">
                <Briefcase class="h-4 w-4 shrink-0 text-gray-400" />
                <span class="truncate">{{ company.industry || "N/A" }}</span>
              </div>
              <div class="flex items-center gap-2">
                <MapPin class="h-4 w-4 shrink-0 text-gray-400" />
                <span class="truncate">{{ company.city || "N/A" }}</span>
              </div>
              <div class="flex items-center gap-2">
                <Globe class="h-4 w-4 shrink-0 text-gray-400" />
                <a
                  :href="company.website"
                  target="_blank"
                  class="text-blue-600 hover:underline truncate"
                  @click.stop
                >
                  {{ company.website?.replace("https://", "") || "N/A" }}
                </a>
              </div>
            </div>

            <!-- Footer -->
            <div
              class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4 mt-5"
            >
              <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <Users class="h-3.5 w-3.5" />
                <span class="font-semibold text-gray-700">{{
                  company.employee_count || "N/A"
                }}</span>
                <span class="hidden sm:inline">employees</span>
              </div>
              <!-- <span
                class="flex items-center gap-0.5 text-sm font-semibold text-blue-600 group-hover:translate-x-1 transition-transform"
              >
                View
                <ChevronRight class="h-4 w-4" />
              </span> -->
              <RouterLink
                :to="{ name: 'CompanyDetail', params: { id: company.id } }"
                class="flex items-center gap-0.5 text-sm font-semibold text-blue-600 group-hover:translate-x-1 transition-transform"
              >
                View
                <ChevronRight class="h-4 w-4" />
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="mt-16 flex flex-col items-center justify-center text-center"
      >
        <div
          class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100"
        >
          <Building2 class="h-10 w-10 text-gray-400" />
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">
          No companies found
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          Try adjusting your search to find what you're looking for.
        </p>
      </div>
    </div>
  </div>
</template>
