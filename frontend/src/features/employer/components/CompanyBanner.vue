<script setup lang="ts">
import type { Company } from "../types/company.types";
import { useRouter } from "vue-router";

import { Pencil, Eye, MapPin, BriefcaseBusiness, Globe } from "lucide-vue-next";

defineProps<{
  company: Company;
}>();

const emit = defineEmits<{
  preview: [];
  viewJobs: [];
}>();

const router = useRouter();

const handleEdit = () => {
  router.push({ name: "employer.company-profile.edit" });
};

const website = (url: string | null) => {
  if (!url) return "N/A";

  return url.replace(/^https?:\/\//, "").replace(/\/$/, "");
};
</script>

<template>
  <div class="w-full bg-[#F8FAFC] pb-6">
    <!-- Cover Image -->
    <div
      class="relative -mt-8 h-40 w-full overflow-hidden bg-slate-800 sm:h-52"
    >
      <div
        v-if="company.cover_image"
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: `url(${company.cover_image})` }"
      ></div>

      <div class="absolute inset-0 bg-black/20"></div>
    </div>

    <!-- Company Header Card -->
    <div
      class="relative mx-auto -mt-14 w-full max-w-[1160px] px-4 sm:-mt-20 sm:px-6 lg:px-8"
    >
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-7"
      >
        <!-- Main Information + Actions -->
        <div
          class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
        >
          <!-- Logo and Company Information -->
          <div class="flex min-w-0 items-start gap-4">
            <div
              class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white sm:h-20 sm:w-20"
            >
              <img
                v-if="company.logo"
                :src="company.logo"
                :alt="`${company.name} logo`"
                class="h-full w-full object-cover"
              />

              <BriefcaseBusiness v-else class="h-8 w-8 text-blue-600" />
            </div>

            <div class="min-w-0 flex-1 pt-1">
              <div class="flex flex-wrap items-center gap-2">
                <h1
                  class="break-words text-lg font-bold text-slate-900 sm:text-2xl"
                >
                  {{ company.name }}
                </h1>

                <!-- Rejected -->
                <span
                  v-if="company.verification_status === 'rejected'"
                  class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600"
                >
                  <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                  Rejected
                </span>

                <!-- Verified -->
                <span
                  v-else-if="company.verification_status === 'verified'"
                  class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600"
                >
                  Verified
                </span>

                <!-- Pending -->
                <span
                  v-else
                  class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600"
                >
                  Pending
                </span>
              </div>

              <p class="mt-1 text-sm text-slate-500">
                {{ company.tagline || company.industry || "Company Profile" }}
              </p>

              <div
                class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500"
              >
                <span class="inline-flex items-center gap-1.5">
                  <MapPin class="h-4 w-4 shrink-0 text-slate-400" />
                  {{
                    company.city || company.address || "Location not specified"
                  }}
                </span>

                <span class="inline-flex items-center gap-1.5">
                  <BriefcaseBusiness class="h-4 w-4 shrink-0 text-slate-400" />
                  {{ company.industry || "Industry not specified" }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div
            class="flex shrink-0 flex-wrap items-center gap-2 border-t border-slate-100 pt-4 lg:border-0 lg:pt-0"
          >
            <!-- Edit -->
            <button
              type="button"
              @click="handleEdit"
              class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 sm:flex-none"
            >
              <Pencil class="h-4 w-4" />
              Edit
            </button>
          </div>
        </div>

        <!-- Company Information -->
        <div
          class="mt-6 grid grid-cols-1 gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2 lg:grid-cols-4"
        >
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Website</p>

            <a
              v-if="company.website"
              :href="company.website"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-1 inline-flex max-w-full items-center gap-1 truncate text-sm font-semibold text-blue-600 hover:underline"
            >
              <Globe class="h-4 w-4 shrink-0" />
              {{ website(company.website) }}
            </a>

            <p v-else class="mt-1 text-sm font-semibold text-slate-700">N/A</p>
          </div>

          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Address</p>
            <p class="mt-1 truncate text-sm font-semibold text-slate-700">
              {{ company.address || "N/A" }}
            </p>
          </div>

          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Company Size</p>
            <p class="mt-1 text-sm font-semibold text-slate-700">
              {{
                company.employee_count
                  ? `${company.employee_count} Employees`
                  : "N/A"
              }}
            </p>
          </div>

          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Company Type</p>
            <p class="mt-1 truncate text-sm font-semibold text-slate-700">
              {{ company.industry || "N/A" }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
