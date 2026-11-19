<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  ArrowLeft,
  MapPin,
  Globe,
  Briefcase,
  Mail,
  Phone,
  Calendar,
  Users,
  CheckCircle2,
} from "lucide-vue-next";
import { companyRepository } from "../../employer/repositories/companyRepository";
import type { Company } from "../../employer/types/company.types";

const route = useRoute();
const router = useRouter();

// State for the company detail
const company = ref<Company | null>(null);
const loading = ref(true);
const error = ref("");

const goToProfile = (id: number) => {
  router.push({ name: "CompanyDetail", params: { id } });
};

// Fetch the company detail by ID when the component is mounted
const fetchCompanyDetail = async () => {
  loading.value = true;
  try {
    const id = Number(route.params.id); // Get the ID from the URL
    const data = await companyRepository.getCompanyById(id);
    company.value = data;
  } catch (err: any) {
    error.value = "Failed to load company details.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Go back to the previous page
const goBack = () => {
  router.back();
};

onMounted(() => {
  fetchCompanyDetail();
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
      <!-- Back Button -->
      <button @click="goBack"
        class="mb-6 flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">
        <ArrowLeft class="h-4 w-4" />
        Back to Companies
      </button>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="rounded-xl bg-red-50 p-6 text-center text-red-600">
        {{ error }}
      </div>

      <!-- Company Detail Content -->
      <div v-else-if="company" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <!-- Cover Image -->
        <div class="relative h-48 w-full bg-slate-200 sm:h-64">
          <img v-if="company.cover_image" :src="company.cover_image" class="h-full w-full object-cover" alt="Cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        </div>

        <!-- Profile Header (Logo & Name) -->
        <div class="relative px-6 pb-6 pt-0 sm:px-10">
          <div class="-mt-12 mb-4 flex items-end justify-between">
            <div
              class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-xl border-4 border-white bg-white shadow-md">
              <img v-if="company.logo" :src="company.logo" alt="Logo" class="h-full w-full object-contain p-2" />
            </div>
          </div>

          <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            {{ company.name }}
          </h1>
          <p class="mt-1 text-gray-500">
            {{ company.tagline || "No tagline provided" }}
          </p>
        </div>

        <!-- Detail Content -->
        <div class="border-t border-gray-100 px-6 py-8 sm:px-10">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <!-- Left Column: Description & Gallery -->
            <div class="md:col-span-2 space-y-6">
              <div>
                <h2 class="text-lg font-bold text-gray-900 mb-2">
                  About Company
                </h2>
                <p class="text-sm leading-relaxed text-gray-600">
                  {{
                    company.description && company.description !== "[]"
                      ? company.description
                      : "No description available."
                  }}
                </p>
              </div>

              <!-- Gallery (If any) -->
              <div v-if="company.gallery && company.gallery.length > 0">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Gallery</h2>
                <div class="grid grid-cols-2 gap-4">
                  <img v-for="(img, i) in company.gallery" :key="i" :src="img"
                    class="rounded-lg object-cover h-40 w-full" alt="Gallery Image" />
                </div>
              </div>
            </div>

            <!-- Right Column: Quick Info -->
            <div class="space-y-4 rounded-xl border border-gray-100 bg-gray-50 p-5">
              <h3 class="text-base font-bold text-gray-900">Company Details</h3>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Briefcase class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Industry</p>
                  <p>{{ company.industry || "N/A" }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <MapPin class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Location</p>
                  <p>{{ company.city || "N/A" }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Users class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Employees</p>
                  <p>{{ company.employee_count || "N/A" }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Calendar class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Founded</p>
                  <p>{{ company.founded_year || "N/A" }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Globe class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Website</p>
                  <a :href="company.website ?? undefined" target="_blank"
                    class="text-blue-600 hover:underline truncate block">
                    {{
                      company.website
                        ? company.website.replace("https://", "")
                        : "N/A"
                    }}
                  </a>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Mail class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Email</p>
                  <p>{{ company.company_email || "N/A" }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 text-sm text-gray-600">
                <Phone class="h-5 w-5 shrink-0 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900">Phone</p>
                  <p>{{ company.phone || "N/A" }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
