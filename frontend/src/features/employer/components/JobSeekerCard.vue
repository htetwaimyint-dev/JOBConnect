<script setup lang="ts">
import { MapPin, Eye, Send } from "lucide-vue-next";

import type { JobSeeker } from "../types/job-seeker.types";

defineProps<{
  jobSeeker: JobSeeker;
}>();

defineEmits<{
  viewProfile: [id: number];
  directOffer: [id: number];
}>();
</script>

<template>
  <div
    class="rounded-xl border border-slate-200 bg-white p-4 transition hover:border-blue-200 hover:shadow-sm sm:p-5"
  >
    <div
      class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between"
    >
      <!-- Profile Information -->
      <div class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
        <!-- Profile Photo -->
        <img
          :src="
            jobSeeker.profile_photo ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(jobSeeker.name)}&background=DBEAFE&color=1D4ED8`
          "
          :alt="`${jobSeeker.name}'s profile photo`"
          class="h-12 w-12 shrink-0 rounded-full object-cover sm:h-16 sm:w-16"
        />

        <!-- Candidate Details -->
        <div
          class="grid min-w-0 flex-1 grid-cols-3 items-center gap-2 sm:gap-4"
        >
          <!-- Name -->
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Candidate Name</p>
            <h3
              class="truncate text-sm font-semibold text-slate-900 sm:text-base"
            >
              {{ jobSeeker.name }}
            </h3>
          </div>

          <!-- Professional Title -->
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Professional Title</p>
            <p class="truncate text-sm text-slate-600">
              {{ jobSeeker.professional_title || "Not specified" }}
            </p>
          </div>

          <!-- Location -->
          <div class="min-w-0">
            <p class="text-xs font-medium text-slate-400">Location</p>
            <p class="flex items-center gap-1 truncate text-sm text-slate-600">
              <MapPin class="h-4 w-4 shrink-0 text-slate-400" />
              <span class="truncate">
                {{ jobSeeker.city || "Not specified" }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div
        class="flex w-full shrink-0 items-center justify-start gap-2 border-t border-slate-100 pt-3 sm:pt-6 xl:w-auto xl:border-0 xl:pt-0"
      >
        <!-- View Profile -->
        <button
          type="button"
          @click="$emit('viewProfile', jobSeeker.id)"
          class="inline-flex w-28 shrink-0 items-center justify-center gap-1 rounded-md border border-slate-200 bg-white p-2 text-[12px] font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 sm:w-32 sm:gap-1.5 sm:py-2 sm:text-xs xl:w-auto xl:rounded-lg xl:px-4 xl:py-2.5 xl:text-sm"
        >
          <Eye class="h-3.5 w-3.5 shrink-0 sm:h-4 sm:w-4" />
          <span>View Profile</span>
        </button>

        <!-- Direct Offer -->
        <button
          type="button"
          @click="$emit('directOffer', jobSeeker.id)"
          class="inline-flex w-28 shrink-0 items-center justify-center gap-1 rounded-md border border-slate-200 bg-blue-600 p-2 text-[12px] font-semibold text-white transition hover:border-blue-200 hover:bg-blue-50 hover:text-slate-700 sm:w-32 sm:gap-1.5 sm:py-2 sm:text-xs xl:w-auto xl:rounded-lg xl:px-4 xl:py-2.5 xl:text-sm"
        >
          <Send class="h-3.5 w-3.5 shrink-0 sm:h-4 sm:w-4" />
          <span>Direct Offer</span>
        </button>
      </div>
    </div>
  </div>
</template>
