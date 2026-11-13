<script setup lang="ts">
import {
  UserRound,
  GraduationCap,
  BriefcaseBusiness,
  Lightbulb,
  FileText,
} from "lucide-vue-next";

import ProfileForm from "../components/ProfileForm.vue";
import EducationForm from "../components/EducationForm.vue";
import ExperienceForm from "../components/ExperienceForm.vue";
import SkillForm from "../components/SkillForm.vue";
import ResumeUpload from "../components/ResumeUpload.vue";

// Profile section configuration
const profileSections = [
  {
    id: "personal",
    title: "Personal Information",
    description: "Your basic personal and contact information.",
    icon: UserRound,
    color: "text-blue-600",
    bg: "bg-blue-50",
    component: ProfileSection,
  },
  {
    id: "education",
    title: "Education",
    description: "Your academic background and qualifications.",
    icon: GraduationCap,
    color: "text-violet-600",
    bg: "bg-violet-50",
    component: EducationSection,
  },
  {
    id: "experience",
    title: "Work Experience",
    description: "Your previous and current professional experience.",
    icon: BriefcaseBusiness,
    color: "text-orange-600",
    bg: "bg-orange-50",
    component: ExperienceSection,
  },
  {
    id: "skills",
    title: "Skills",
    description: "Highlight your professional and technical skills.",
    icon: Lightbulb,
    color: "text-emerald-600",
    bg: "bg-emerald-50",
    component: SkillSection,
  },
  {
    id: "resume",
    title: "Resume Management",
    description: "Upload and manage your resumes.",
    icon: FileText,
    color: "text-rose-600",
    bg: "bg-rose-50",
    component: ResumeSection,
  },
] as const;
</script>

<template>
  <div class="mx-auto w-full max-w-5xl space-y-8 pb-10">
    <!-- Page Header -->
    <header>
      <div class="flex items-center gap-3">
        <div
          class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
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
    </header>

    <!-- Profile Sections Overview -->
    <nav
      aria-label="Profile sections"
      class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5"
    >
      <a
        v-for="section in profileSections"
        :key="section.id"
        :href="`#${section.id}`"
        class="rounded-xl border border-slate-200 bg-white p-4 no-underline transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
      >
        <div
          class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl"
          :class="[section.bg, section.color]"
        >
          <component :is="section.icon" :size="20" />
        </div>

        <h2 class="text-sm font-semibold text-slate-800">
          {{ section.title }}
        </h2>

        <p class="mt-1 text-xs leading-5 text-slate-500">
          {{ section.description }}
        </p>
      </a>
    </nav>

<<<<<<< Updated upstream
            <ProfileForm />
        </section>
=======
    <!-- Profile Sections -->
    <section
      v-for="section in profileSections"
      :id="section.id"
      :key="section.id"
      class="scroll-mt-24 space-y-3"
    >
      <!-- Section Header -->
      <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
        <div
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
          :class="[section.bg, section.color]"
        >
          <component :is="section.icon" :size="19" />
        </div>
>>>>>>> Stashed changes

        <div>
          <h2 class="font-semibold text-slate-800">
            {{ section.title }}
          </h2>

          <p class="text-xs text-slate-500">
            {{ section.description }}
          </p>
        </div>
      </div>

<<<<<<< Updated upstream
            <EducationForm />
        </section>

        <!-- Work Experience -->
        <section class="space-y-3">
            <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                    <BriefcaseBusiness :size="19" />
                </div>

                <div>
                    <h2 class="font-semibold text-slate-800">
                        Work Experience
                    </h2>
                    <p class="text-xs text-slate-500">
                        Your previous and current professional experience
                    </p>
                </div>
            </div>

            <ExperienceForm />
        </section>

        <!-- Skills -->
        <section class="space-y-3">
            <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <Lightbulb :size="19" />
                </div>

                <div>
                    <h2 class="font-semibold text-slate-800">
                        Skills
                    </h2>
                    <p class="text-xs text-slate-500">
                        Your professional and technical skills
                    </p>
                </div>
            </div>

            <SkillForm />
        </section>

        <!-- Resume -->
        <section class="space-y-3">
            <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <FileText :size="19" />
                </div>

                <div>
                    <h2 class="font-semibold text-slate-800">
                        Resume Management
                    </h2>
                    <p class="text-xs text-slate-500">
                        Upload and manage your resumes
                    </p>
                </div>
            </div>

            <ResumeUpload />
        </section>

    </div>
=======
      <!-- Dynamic Section Component -->
      <component :is="section.component" />
    </section>
  </div>
>>>>>>> Stashed changes
</template>
