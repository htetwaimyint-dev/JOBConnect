<script setup lang="ts">
import { ref } from "vue";
import { RouterLink, useRoute } from "vue-router";

import BaseButton from "@/components/common/BaseButton.vue";

import {
  LayoutDashboard,
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
    <!-- Sidebar Header -->
    <div class="flex h-20 items-center border-b border-slate-200 px-6">
      <div>
        <p class="m-1 text-xl text-slate-600">JobConnect AI</p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-2 overflow-hidden px-4 py-6">
      <!-- Personal Information -->
      <p class="mb-2 px-3 text-sm font-medium text-slate-500">
        Personal Information
      </p>

      <!-- Dashboard -->
      <RouterLink
        to="/jobseeker/dashboard"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/dashboard')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <LayoutDashboard :size="20" :stroke-width="2" />
        <span>Dashboard</span>
      </RouterLink>

      <!-- Profile Dropdown -->
      <div>
        <button
          type="button"
          @click="togglePersonalInfo"
          class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-medium transition-colors"
          :class="
            isPersonalInfoActive()
              ? 'bg-blue-50 text-blue-600'
              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
          "
          :aria-expanded="isPersonalInfoOpen"
          aria-controls="personal-info-submenu"
        >
          <span class="flex items-center gap-3">
            <UserCircle :size="20" :stroke-width="2" />
            Profile
          </span>

          <ChevronDown
            :size="16"
            class="transition-transform"
            :class="{ 'rotate-180': isPersonalInfoOpen }"
          />
        </button>

        <div
          v-if="isPersonalInfoOpen"
          id="personal-info-submenu"
          class="ml-5 mt-1 space-y-1 border-l border-slate-200 pl-4"
        >
          <RouterLink
            to="/jobseeker/personal-information"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium no-underline transition-colors"
            :class="
              isActive('/jobseeker/personal-information')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
            "
          >
            <UserRound :size="18" />
            Personal Details
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/education"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium no-underline transition-colors"
            :class="
              isActive('/jobseeker/personal-information/education')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
            "
          >
            <GraduationCap :size="18" />
            Education
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/experience"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium no-underline transition-colors"
            :class="
              isActive('/jobseeker/personal-information/experience')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
            "
          >
            <Briefcase :size="18" />
            Work Experience
          </RouterLink>

          <RouterLink
            to="/jobseeker/personal-information/skills"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium no-underline transition-colors"
            :class="
              isActive('/jobseeker/personal-information/skills')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
            "
          >
            <Code :size="18" />
            Skills
          </RouterLink>
        </div>
      </div>

      <!-- My Jobs -->
      <RouterLink
        to="/jobseeker/jobs"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/jobs')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <BriefcaseBusiness :size="20" :stroke-width="2" />
        <span>My Jobs</span>
      </RouterLink>

      <!-- Applications -->
      <RouterLink
        to="/jobseeker/applications"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/applications')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <Send :size="20" :stroke-width="2" />
        <span>Applications</span>
      </RouterLink>

      <!-- Interviews -->
      <RouterLink
        to="/jobseeker/interviews"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/interviews')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <CalendarDays :size="20" :stroke-width="2" />
        <span>Interviews</span>
      </RouterLink>

      <!-- Job Offers -->
      <RouterLink
        to="/jobseeker/offers"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/offers')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <Award :size="20" :stroke-width="2" />
        <span>Job Offers</span>
      </RouterLink>

      <div class="my-2 border-t border-slate-200"></div>

      <!-- Account -->
      <p class="mb-2 px-3 text-sm font-medium text-slate-500">Account</p>

      <!-- My Account -->
      <RouterLink
        to="/jobseeker/profile"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors no-underline"
        :class="
          isActive('/jobseeker/profile')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <UserRound :size="20" :stroke-width="2" />
        <span>My Account</span>
      </RouterLink>
    </nav>

    <!-- Logout -->
    <div class="border-t border-slate-200 p-4">
      <BaseButton
        color="danger"
        variant="ghost"
        size="36"
        class="!w-full !justify-start !rounded-lg !px-3 !py-3 !text-sm !font-medium !text-red-500 hover:!bg-red-50"
      >
        <template #leading>
          <LogOut :size="20" :stroke-width="2" />
        </template>
        Logout
      </BaseButton>
    </div>
  </aside>
</template>
