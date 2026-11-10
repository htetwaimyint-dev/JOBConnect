<script setup lang="ts">
import { ref } from "vue";
import { RouterLink, useRoute } from "vue-router";

import BaseButton from "@/components/common/BaseButton.vue";

import {
  LayoutDashboard,
  FileText,
  BriefcaseBusiness,
  Send,
  CalendarDays,
  Award,
  UserRound,
  ChevronDown,
  LogOut,
  UserCircle,
  GraduationCap,
  Briefcase,
  Code,
} from "lucide-vue-next";

const route = useRoute();

const isResumeOpen = ref(route.path.startsWith("/jobseeker/resumes"));
const isPersonalInfoOpen = ref(
  route.path.startsWith("/jobseeker/personal-information"),
);

const toggleResume = () => {
  isResumeOpen.value = !isResumeOpen.value;
};

const togglePersonalInfo = () => {
  isPersonalInfoOpen.value = !isPersonalInfoOpen.value;
};

const isActive = (path: string) => route.path === path;

const isResumeActive = () => route.path.startsWith("/jobseeker/resumes");

const isPersonalInfoActive = () =>
  route.path.startsWith("/jobseeker/personal-information");
</script>

<template>
  <aside
    class="fixed left-0 top-0 z-50 flex h-screen w-64 shrink-0 flex-col border-r border-slate-200 bg-white"
  >
    <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-4">
      <!-- Personal Information -->
      <p
        class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400"
      >
        Personal Information
      </p>

      <!-- Dashboard -->
      <RouterLink
        to="/jobseeker/dashboard"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/dashboard')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <LayoutDashboard
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/dashboard')
              ? 'text-blue-600'
              : 'text-slate-400'
          "
        />
        Dashboard
      </RouterLink>

      <!-- Profile & Personal Information -->
      <div>
        <button
          type="button"
          @click="togglePersonalInfo"
          class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm transition-all duration-200"
          :class="
            isPersonalInfoActive()
              ? 'bg-blue-50 font-semibold text-blue-700'
              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
          "
          :aria-expanded="isPersonalInfoOpen"
          aria-controls="personal-info-submenu"
        >
          <span class="flex items-center gap-3">
            <UserCircle
              class="h-5 w-5 shrink-0"
              :class="
                isPersonalInfoActive() ? 'text-blue-600' : 'text-slate-400'
              "
            />
            Profile
          </span>

          <ChevronDown
            class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
            :class="{ 'rotate-180': isPersonalInfoOpen }"
          />
        </button>

        <div
          v-if="isPersonalInfoOpen"
          id="personal-info-submenu"
          class="ml-6 mt-2 space-y-1 border-l-2 border-slate-200 pl-4"
        >
          <RouterLink
            to="/jobseeker/personal-information"
            class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/personal-information')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            <UserRound class="h-4 w-4" />
            Personal Details
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/education"
            class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/personal-information/education')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            <GraduationCap class="h-4 w-4" />
            Education
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/experience"
            class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/personal-information/experience')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            <Briefcase class="h-4 w-4" />
            Work Experience
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/skills"
            class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/personal-information/skills')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            <Code class="h-4 w-4" />
            Skills
          </RouterLink>
        </div>
      </div>
      <!-- <div>
        <button
          type="button"
          @click="toggleResume"
          class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm transition-all duration-200"
          :class="
            isResumeActive()
              ? 'bg-blue-50 font-semibold text-blue-700'
              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
          "
          :aria-expanded="isResumeOpen"
          aria-controls="resume-submenu"
        >
          <span class="flex items-center gap-3">
            <FileText
              class="h-5 w-5 shrink-0"
              :class="isResumeActive() ? 'text-blue-600' : 'text-slate-400'"
            />
            Resume Manager
          </span>

          <ChevronDown
            class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
            :class="{ 'rotate-180': isResumeOpen }"
          />
        </button>

        <div
          v-if="isResumeOpen"
          id="resume-submenu"
          class="ml-6 mt-2 space-y-1 border-l-2 border-slate-200 pl-4"
        >
          <RouterLink
            to="/jobseeker/resumes/create"
            class="block rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/resumes/create')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            Add Resume
          </RouterLink>

          <RouterLink
            to="/jobseeker/resumes"
            class="block rounded-md px-3 py-2.5 text-sm no-underline transition-colors duration-200"
            :class="
              isActive('/jobseeker/resumes')
                ? 'bg-blue-50 font-medium text-blue-700'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'
            "
          >
            My Resumes
          </RouterLink>
        </div>
      </div> -->

      <!-- My Jobs -->
      <RouterLink
        to="/jobseeker/jobs"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/jobs')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <BriefcaseBusiness
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/jobs') ? 'text-blue-600' : 'text-slate-400'
          "
        />
        My Jobs
      </RouterLink>

      <!-- Applications -->
      <RouterLink
        to="/jobseeker/applications"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/applications')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <Send
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/applications')
              ? 'text-blue-600'
              : 'text-slate-400'
          "
        />
        Applications
      </RouterLink>

      <!-- Interviews -->
      <RouterLink
        to="/jobseeker/interviews"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/interviews')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <CalendarDays
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/interviews')
              ? 'text-blue-600'
              : 'text-slate-400'
          "
        />
        Interviews
      </RouterLink>

      <!-- Job Offers -->
      <RouterLink
        to="/jobseeker/offers"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/offers')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <Award
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/offers') ? 'text-blue-600' : 'text-slate-400'
          "
        />
        Job Offers
      </RouterLink>

      <div class="my-3 border-t border-slate-100"></div>

      <!-- Account -->
      <p
        class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400"
      >
        Account
      </p>

      <!-- My Account -->
      <RouterLink
        to="/jobseeker/profile"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm no-underline transition-all duration-200"
        :class="
          isActive('/jobseeker/profile')
            ? 'bg-blue-50 font-semibold text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <UserRound
          class="h-5 w-5 shrink-0"
          :class="
            isActive('/jobseeker/profile') ? 'text-blue-600' : 'text-slate-400'
          "
        />
        My Account
      </RouterLink>

      <!-- Logout -->
      <BaseButton
        color="danger"
        variant="ghost"
        size="36"
        class="!w-full !justify-start !rounded-lg !px-4 !text-slate-600 hover:!bg-red-50 hover:!text-red-600"
      >
        <template #leading>
          <LogOut class="h-5 w-5 text-slate-400" />
        </template>
        Logout
      </BaseButton>
    </nav>
  </aside>
</template>
