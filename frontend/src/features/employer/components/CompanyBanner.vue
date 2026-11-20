<script setup lang="ts">
import type { Company } from "../types/company.types";
import { useRouter } from "vue-router";
import { Pencil } from "lucide-vue-next";
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
  company: Company;
}>();

const router = useRouter();

const handleEdit = () => {
  router.push({ name: "employer.company-profile.edit" });
};

const emit = defineEmits<{
  viewJobs: [];
}>();
</script>

<template>
  <div class="bg-[#f7f7fb] pb-6">
    <!-- Cover Image Banner -->
    <div
      class="w-screen relative left-1/2 right-1/2 -translate-x-1/2 h-48 sm:h-58 bg-gray-900 overflow-hidden -mt-8"
    >
      <div
        v-if="company.cover_image"
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: `url(${company.cover_image})` }"
      ></div>

      <div class="absolute inset-0 bg-black/20"></div>
    </div>

    <!-- Profile Card Header -->
    <div class="max-w-290 mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="relative -mt-16 sm:-mt-24 bg-white rounded-md border border-gray-100 p-6 sm:p-8"
      >
        <div
          class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6"
        >
          <!-- Logo & Info -->
          <div class="flex flex-col sm:flex-row items-start gap-5">
            <img
              :src="company.logo ?? ''"
              :alt="`${company.name} logo`"
              class="h-18 w-18 sm:h-18 sm:w-18 object-cover bg-white shrink-0"
            />

            <div class="space-y-1">
              <h1 class="mt-2 text-xl sm:text-xl font-bold text-gray-900">
                {{ company.name }}
              </h1>

              <p class="text-sm text-gray-500">
                {{ company.tagline || company.industry }}
              </p>
            </div>
          </div>

          <!-- Edit Button -->
          <div class="flex items-center gap-3 self-end sm:self-start">
            <BaseButton
              type="button"
              variant="outline"
              color="neutral"
              class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
              @click="handleEdit"
            >
              <Pencil class="w-4 h-4" />
              Edit Profile
            </BaseButton>
          </div>
        </div>

        <!-- Info Grid -->
        <div
          class="pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
        >
          <!-- Website -->
          <div>
            <p class="text-xs font-medium text-gray-400">Website</p>

            <a
              :href="company.website ?? '#'"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-1 block text-sm font-semibold text-blue-600 hover:underline truncate"
            >
              {{
                company.website
                  ? company.website.replace(/^https?:\/\/?/, "")
                  : "N/A"
              }}
            </a>
          </div>

          <!-- Location -->
          <div>
            <p class="text-xs font-medium text-gray-400">Location</p>

            <p class="mt-1 text-sm font-semibold text-gray-800 truncate">
              {{ company.address || "N/A" }}
            </p>
          </div>

          <!-- Company Size -->
          <div>
            <p class="text-xs font-medium text-gray-400">Company Size</p>

            <p class="mt-1 text-sm font-semibold text-gray-800">
              {{
                company.employee_count
                  ? `${company.employee_count} Employees`
                  : "N/A"
              }}
            </p>
          </div>

          <!-- Company Type -->
          <div>
            <p class="text-xs font-medium text-gray-400">Company Type</p>

            <p class="mt-1 text-sm font-semibold text-gray-800 truncate">
              {{ company.industry || "Design Services" }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
