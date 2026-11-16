<script setup lang="ts">
import {
  UserRound,
  GraduationCap,
  BriefcaseBusiness,
  Lightbulb,
  FileText,
} from "lucide-vue-next";

import ProfileSection from "../components/ProfileSection.vue";
import EducationSection from "../components/EducationSection.vue";
import ExperienceSection from "../components/ExperienceSection.vue";
import SkillSection from "../components/SkillSection.vue";
import ResumeSection from "../components/ResumeSection.vue";

const profileSections = [
  {
    title: "Personal Information",
    description: "Your basic personal and contact information.",
    icon: UserRound,
    color: "text-blue-600",
    bg: "bg-blue-50",
  },
  {
    title: "Education",
    description: "Your academic background and qualifications.",
    icon: GraduationCap,
    color: "text-violet-600",
    bg: "bg-violet-50",
  },
  {
    title: "Work Experience",
    description: "Your previous and current professional experience.",
    icon: BriefcaseBusiness,
    color: "text-orange-600",
    bg: "bg-orange-50",
  },
  {
    title: "Skills",
    description: "Highlight your professional and technical skills.",
    icon: Lightbulb,
    color: "text-emerald-600",
    bg: "bg-emerald-50",
  },
  {
    title: "Resume",
    description: "Upload and manage your resumes.",
    icon: FileText,
    color: "text-rose-600",
    bg: "bg-rose-50",
  },
];
</script>

<template>
  <div class="mx-auto max-w-5xl space-y-8 pb-10">
    <!-- Page Header -->
    <div
      class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div>
        <div class="flex items-center gap-3">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
          >
            <UserRound :size="25" />
          </div>

          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">
              My Profile
            </h1>

            <p class="mt-1 text-sm text-slate-500">
              Manage your professional profile.
            </p>
          </div>
        </div>

        <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-500">
          Keep your profile up to date to help employers understand your
          background, qualifications, and career interests.
        </p>
      </div>
    </div>

    <!-- Profile Sections Overview -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
      <div
        v-for="section in profileSections"
        :key="section.title"
        class="rounded-xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:shadow-sm"
      >
        <div
          class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl"
          :class="[section.bg, section.color]"
        >
          <component :is="section.icon" :size="20" />
        </div>

        <h3 class="text-sm font-semibold text-slate-800">
          {{ section.title }}
        </h3>

        <p class="mt-1 text-xs leading-5 text-slate-500">
          {{ section.description }}
        </p>
      </div>
    </div>

    <!-- Personal Information -->
    <section class="space-y-3">
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
        >
          <UserRound :size="19" />
        </div>
        <div>
          <h2 class="font-semibold text-slate-800">Personal Information</h2>
          <p class="text-xs text-slate-500">
            Your personal details and profile information
          </p>
        </div>
      </div>

      <ProfileSection />
    </section>

    <!-- Education -->
    <section class="space-y-3">
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600"
        >
          <GraduationCap :size="19" />
        </div>

        <div>
          <h2 class="font-semibold text-slate-800">Education</h2>
          <p class="text-xs text-slate-500">
            Your academic background and qualifications
          </p>
        </div>
      </div>

      <EducationSection />
    </section>

    <!-- Work Experience -->
    <section class="space-y-3">
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 text-orange-600"
        >
          <BriefcaseBusiness :size="19" />
        </div>

        <div>
          <h2 class="font-semibold text-slate-800">Work Experience</h2>
          <p class="text-xs text-slate-500">
            Your previous and current professional experience
          </p>
        </div>
      </div>

      <ExperienceSection />
    </section>

    <!-- Skills -->
    <section class="space-y-3">
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"
        >
          <Lightbulb :size="19" />
        </div>

        <div>
          <h2 class="font-semibold text-slate-800">Skills</h2>
          <p class="text-xs text-slate-500">
            Your professional and technical skills
          </p>
        </div>
      </div>

      <SkillSection />
    </section>

    <!-- Resume -->
    <section class="space-y-3">
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600"
        >
          <FileText :size="19" />
        </div>

        <div>
          <h2 class="font-semibold text-slate-800">Resume Management</h2>
          <p class="text-xs text-slate-500">Upload and manage your resumes</p>
        </div>
      </div>

      <ResumeSection />
    </section>
  </div>
</template>
