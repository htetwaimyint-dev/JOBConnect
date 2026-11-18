<script setup lang="ts">
import {
  MapPin,
  GraduationCap,
  BriefcaseBusiness,
  Eye,
  Send,
} from "lucide-vue-next";

import type { JobSeeker } from "../types/job-seeker.types";

defineProps<{
  jobSeeker: JobSeeker;
}>();

const emit = defineEmits<{
  viewProfile: [id: number];
  directOffer: [id: number];
}>();
</script>

<template>
  <div
    class="flex flex-col gap-5 rounded-2xl border border-blue-100 bg-gradient-to-r from-white to-blue-50 p-5 shadow-sm transition hover:shadow-md sm:flex-row sm:items-center sm:justify-between"
  >
    <!-- First Div: Candidate Information -->
    <div class="flex min-w-0 flex-1 flex-wrap items-center gap-5">
      <!-- Profile Photo -->
      <img
        :src="
          jobSeeker.profile_photo ||
          `https://ui-avatars.com/api/?name=${encodeURIComponent(jobSeeker.name)}&background=dbeafe&color=1d4ed8`
        "
        :alt="jobSeeker.name"
        class="h-16 w-16 shrink-0 rounded-full border-2 border-white object-cover shadow-sm"
      />

      <!-- Name -->
      <div class="min-w-[100px] flex-1">
        <p class="text-xs font-medium text-gray-500">Name</p>
        <h3 class="mt-1 truncate font-semibold text-gray-900">
          {{ jobSeeker.name }}
        </h3>
      </div>

      <!-- Role -->
      <div class="min-w-[100px] flex-1">
        <p class="text-xs font-medium text-gray-500">Role</p>
        <p class="mt-1 font-medium text-blue-700">
          {{ jobSeeker.professional_title || "Not specified" }}
        </p>
      </div>

      <!-- Skills -->
      <!-- <div class="min-w-[150px] flex-1">
        <p class="text-xs font-medium text-gray-500">Skills</p>

        <div class="mt-2 flex flex-wrap gap-1">
          <span
            v-for="skill in jobSeeker.skills.slice(0, 3)"
            :key="skill.id"
            class="rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700"
          >
            {{ skill.name }}
          </span>

          <span
            v-if="jobSeeker.skills.length === 0"
            class="text-sm text-gray-400"
          >
            No skills
          </span>

          <span
            v-else-if="jobSeeker.skills.length > 3"
            class="text-xs text-gray-500"
          >
            +{{ jobSeeker.skills.length - 3 }}
          </span>
        </div>
      </div> -->
      <!-- Education -->
      <!-- <div class="min-w-[100px] flex-1">
        <p class="text-xs font-medium text-gray-500">Education</p>

        <div v-if="jobSeeker.educations?.length" class="mt-1 space-y-2">
          <div
            v-for="(education, index) in jobSeeker.educations"
            :key="education.id ?? index"
            class="flex items-start gap-2 text-sm text-gray-700"
          >
            <GraduationCap :size="16" class="mt-0.5 shrink-0 text-blue-500" />

            <div class="min-w-0">
              <p class="line-clamp-2">
                {{ education.degree || "Not specified" }}
              </p>

              <p
                v-if="education.institution"
                class="mt-1 text-xs text-gray-500"
              >
                {{ education.institution }}
              </p>
            </div>
          </div>
        </div>

        <p v-else class="mt-1 text-sm text-gray-400">Not specified</p>
      </div> -->
      <!-- Education -->
      <!-- <div class="min-w-[100px] flex-1">
        <p class="text-xs font-medium text-gray-500">Education</p>

        <div class="mt-1 flex items-start gap-2 text-sm text-gray-700">
          <GraduationCap :size="16" class="mt-0.5 shrink-0 text-blue-500" />

          <div class="min-w-0">
            <p class="line-clamp-2">
              {{ jobSeeker.educations?.[0]?.degree || "Not specified" }}
            </p>

            <p
              v-if="jobSeeker.educations?.[0]?.institution"
              class="mt-1 truncate text-xs text-gray-500"
            >
              {{ jobSeeker.educations[0].institution }}
            </p>

            <p
              v-if="jobSeeker.educations?.length > 1"
              class="mt-1 text-xs font-medium text-blue-600"
            >
              +{{ jobSeeker.educations.length - 1 }} more
            </p>
          </div>
        </div>
      </div> -->
      <!-- Location -->
      <div class="min-w-[100px] flex-1">
        <p class="text-xs font-medium text-gray-500">Location</p>
        <div class="mt-1 flex items-center gap-1 text-sm text-gray-700">
          <MapPin :size="15" class="shrink-0 text-blue-500" />
          {{ jobSeeker.city || "Not specified" }}
        </div>
      </div>
    </div>

    <!-- Second Div: Actions -->
    <div class="flex shrink-0 items-center gap-2">
      <button
        type="button"
        @click="emit('viewProfile', jobSeeker.id)"
        class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
      >
        <Eye :size="16" />
        View Profile
      </button>

      <button
        type="button"
        @click="emit('directOffer', jobSeeker.id)"
        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
      >
        <Send :size="16" />
        Direct Offer
      </button>
    </div>
  </div>
</template>
