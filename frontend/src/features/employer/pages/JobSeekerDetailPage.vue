<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  ArrowLeft,
  UserRound,
  LoaderCircle,
  AlertCircle,
  MapPin,
  BriefcaseBusiness,
  Wallet,
  UserCheck,
} from "lucide-vue-next";

import ProfileSection from "@/features/job-seeker/components/ProfileSection.vue";
import EducationSection from "@/features/job-seeker/components/EducationSection.vue";
import ExperienceSection from "@/features/job-seeker/components/ExperienceSection.vue";
import SkillSection from "@/features/job-seeker/components/SkillSection.vue";
import ResumeSection from "@/features/job-seeker/components/ResumeSection.vue";

import { profileRepository } from "@/features/job-seeker/repositories/profileRepository";
import type { JobSeekerProfile } from "@/features/job-seeker/types/profile.types";

const route = useRoute();
const router = useRouter();

const jobSeekerId = ref(Number(route.params.id));

const profile = ref<JobSeekerProfile | null>(null);
const isLoading = ref(false);
const errorMessage = ref("");

const loadProfile = async () => {
  if (!Number.isInteger(jobSeekerId.value) || jobSeekerId.value <= 0) {
    profile.value = null;
    errorMessage.value = "Invalid job seeker ID.";
    return;
  }

  isLoading.value = true;
  errorMessage.value = "";
  profile.value = null;

  try {
    profile.value = await profileRepository.getProfileById(jobSeekerId.value);
  } catch (error) {
    console.error("Failed to load job seeker profile:", error);
    errorMessage.value = "Unable to load job seeker profile.";
  } finally {
    isLoading.value = false;
  }
};

const goBack = () => {
  router.push({ name: "EmployerJobSeekerProfiles" });
};

watch(
  () => route.params.id,
  (newId) => {
    jobSeekerId.value = Number(newId);
    loadProfile();
  },
);

onMounted(loadProfile);
</script>

<template>
  <div class="min-h-full space-y-6 bg-slate-50/70 p-4 pb-8 sm:p-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
          @click="goBack"
        >
          <ArrowLeft :size="19" />
        </button>

        <div>
          <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">
            Job Seeker Details
          </h1>
          <p class="mt-1 text-sm text-slate-500">
            Review professional information and qualifications.
          </p>
        </div>
      </div>

      <div
        class="inline-flex items-center gap-2 rounded-lg border border-blue-100 bg-white px-3 py-2 text-xs font-semibold text-blue-600"
      >
        <UserCheck :size="15" />
        Employer View
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="isLoading"
      class="flex min-h-[320px] flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white"
    >
      <LoaderCircle :size="32" class="animate-spin text-blue-600" />
      <p class="mt-4 text-sm font-medium text-slate-600">Loading profile...</p>
      <p class="mt-1 text-xs text-slate-400">
        Please wait while we retrieve the information.
      </p>
    </div>

    <!-- Error -->
    <div
      v-else-if="errorMessage"
      class="rounded-2xl border border-red-200 bg-white p-8 text-center"
    >
      <div
        class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500"
      >
        <AlertCircle :size="25" />
      </div>

      <h2 class="mt-4 font-bold text-slate-800">Unable to load profile</h2>

      <p class="mt-2 text-sm text-slate-500">
        {{ errorMessage }}
      </p>

      <div class="mt-5 flex justify-center gap-3">
        <button
          type="button"
          class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
          @click="goBack"
        >
          Go Back
        </button>

        <button
          type="button"
          class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
          @click="loadProfile"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Main Profile -->
    <template v-else-if="profile">
      <!-- Profile Summary Card -->
      <div
        :key="jobSeekerId"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
      >
        <div class="h-1.5 bg-blue-600"></div>

        <div class="flex flex-col gap-6 p-5 sm:flex-row sm:items-center sm:p-7">
          <!-- Avatar -->
          <div class="relative mx-auto shrink-0 sm:mx-0">
            <div
              class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-blue-500 sm:h-32 sm:w-32"
            >
              <img
                v-if="profile.profile_photo"
                :src="profile.profile_photo"
                alt="Profile photo"
                class="h-full w-full object-cover"
              />
              <UserRound v-else :size="48" />
            </div>

            <span
              class="absolute -bottom-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full border-4 border-white bg-blue-600 text-white"
            >
              <UserCheck :size="14" />
            </span>
          </div>

          <!-- Name and Details -->
          <div class="min-w-0 flex-1 text-center sm:text-left">
            <div
              class="flex flex-wrap items-center justify-center gap-2 sm:justify-start"
            >
              <h2 class="text-2xl font-bold text-slate-800">
                {{ profile.name || "Job Seeker" }}
              </h2>

              <span
                class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"
              >
                Job Seeker
              </span>
            </div>

            <p class="mt-2 text-base font-medium text-slate-500">
              {{
                profile.professional_title || "Professional Title Not Provided"
              }}
            </p>

            <div
              class="mt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-500 sm:justify-start"
            >
              <span class="inline-flex items-center gap-2">
                <MapPin :size="16" class="text-blue-500" />
                {{ profile.city || "Location not provided" }}
              </span>

              <span class="inline-flex items-center gap-2">
                <BriefcaseBusiness :size="16" class="text-blue-500" />
                {{ profile.experience_years ?? "—" }}
                {{
                  profile.experience_years !== null
                    ? "Years Experience"
                    : "Experience not provided"
                }}
              </span>
            </div>
          </div>
        </div>

        <!-- Summary Stats -->
        <div
          class="grid grid-cols-1 border-t border-slate-100 bg-slate-50/70 sm:grid-cols-2"
        >
          <div
            class="flex items-center gap-3 px-6 py-4 sm:border-r sm:border-slate-200"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
            >
              <BriefcaseBusiness :size="19" />
            </div>

            <div>
              <p class="text-xs text-slate-400">Work Experience</p>
              <p class="mt-0.5 font-bold text-slate-800">
                {{ profile.experience_years ?? "Not provided" }}
                <span
                  v-if="profile.experience_years !== null"
                  class="text-xs font-medium text-slate-500"
                >
                  Years
                </span>
              </p>
            </div>
          </div>

          <div class="flex items-center gap-3 px-6 py-4">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600"
            >
              <Wallet :size="19" />
            </div>

            <div>
              <p class="text-xs text-slate-400">Expected Salary</p>
              <p class="mt-0.5 font-bold text-slate-800">
                {{
                  profile.expected_salary !== null
                    ? profile.expected_salary.toLocaleString()
                    : "Not provided"
                }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Two Column Layout -->
      <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-12">
        <!-- Left Column -->
        <div class="space-y-6 xl:col-span-5">
          <!-- Personal Information -->
          <section class="space-y-3">
            <div class="flex items-center gap-2">
              <div class="h-5 w-1 rounded-full bg-blue-600"></div>
              <div>
                <h2 class="font-bold text-slate-800">Personal Information</h2>
                <p class="text-xs text-slate-400">
                  Personal details and profile summary
                </p>
              </div>
            </div>

            <ProfileSection
              :key="`profile-${jobSeekerId}`"
              :read-only="true"
              :job-seeker-id="jobSeekerId"
            />
          </section>
        </div>

        <!-- Right Column -->
        <div class="space-y-6 xl:col-span-7">
          <!-- Education -->
          <section class="space-y-3">
            <div class="flex items-center gap-2">
              <div class="h-5 w-1 rounded-full bg-blue-600"></div>
              <div>
                <h2 class="font-bold text-slate-800">Education</h2>
                <p class="text-xs text-slate-400">
                  Academic background and qualifications
                </p>
              </div>
            </div>

            <EducationSection
              :key="`education-${jobSeekerId}`"
              :read-only="true"
              :job-seeker-id="jobSeekerId"
            />
          </section>

          <!-- Experience -->
          <section class="space-y-3">
            <div class="flex items-center gap-2">
              <div class="h-5 w-1 rounded-full bg-blue-600"></div>
              <div>
                <h2 class="font-bold text-slate-800">Work Experience</h2>
                <p class="text-xs text-slate-400">
                  Previous employment and professional history
                </p>
              </div>
            </div>

            <ExperienceSection
              :key="`experience-${jobSeekerId}`"
              :read-only="true"
              :job-seeker-id="jobSeekerId"
            />
          </section>
        </div>
      </div>

      <!-- Skills & Public Resumes -->
      <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
        <!-- Skills -->
        <section class="min-w-0 space-y-3">
          <div class="flex items-center gap-2">
            <div class="h-5 w-1 rounded-full bg-blue-600"></div>
            <div>
              <h2 class="font-bold text-slate-800">Skills</h2>
              <p class="text-xs text-slate-400">
                Technical and professional abilities
              </p>
            </div>
          </div>

          <SkillSection
            :key="`skills-${jobSeekerId}`"
            :read-only="true"
            :job-seeker-id="jobSeekerId"
          />
        </section>

        <!-- Public Resumes -->
        <section class="min-w-0 space-y-3">
          <div class="flex items-center gap-2">
            <div class="h-5 w-1 rounded-full bg-blue-600"></div>
            <div>
              <h2 class="font-bold text-slate-800">Public Resumes</h2>
              <p class="text-xs text-slate-400">
                Available documents for download
              </p>
            </div>
          </div>

          <ResumeSection
            :key="`resumes-${jobSeekerId}`"
            :read-only="true"
            :job-seeker-id="jobSeekerId"
          />
        </section>
      </div>
    </template>
  </div>
</template>
