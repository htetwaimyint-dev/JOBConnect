<script setup lang="ts">
import { computed } from "vue";
import { RouterLink } from "vue-router";
import {
  LayoutDashboard,
  Search,
  FileText,
  Bookmark,
  CalendarDays,
  Gift,
  UserRound,
  Settings,
  BriefcaseBusiness,
  Building2,
  Users,
  X,
} from "lucide-vue-next";

import { useAuthStore } from "@/stores/auth.store";
import { useUiStore } from "@/stores/ui.store";

const authStore = useAuthStore();
const uiStore = useUiStore();

const navigationItems = computed(() => {
  const role = authStore.user?.role;

  // Job Seeker
  if (role === "job_seeker") {
    return [
      {
        title: "Dashboard",
        path: "/jobseeker/dashboard",
        icon: LayoutDashboard,
      },
      { title: "Find Jobs", path: "/jobseeker/jobs", icon: Search },
      {
        title: "My Applications",
        path: "/jobseeker/applications",
        icon: FileText,
      },
      { title: "Saved Jobs", path: "/jobseeker/saved-jobs", icon: Bookmark },
      {
        title: "Interviews",
        path: "/jobseeker/interviews",
        icon: CalendarDays,
      },
      { title: "Job Offers", path: "/jobseeker/offers", icon: Gift },
    ];
  }

  // Employer
  if (role === "employer") {
    return [
      {
        title: "Dashboard",
        path: "/employer/dashboard",
        icon: LayoutDashboard,
      },
      { title: "Company Profile", path: "/employer/profile", icon: Building2 },
      {
        title: "Edit Company Profile",
        path: "/employer/profile/edit",
        icon: UserRound,
      },
      { title: "Settings", path: "/employer/settings", icon: Settings },
    ];
  }

  // Admin
  if (role === "admin") {
    return [
      { title: "Dashboard", path: "/admin/dashboard", icon: LayoutDashboard },
      { title: "Settings", path: "/admin/settings", icon: Settings },
    ];
  }

  return [];
});

const accountItems = computed(() => {
  const role = authStore.user?.role;

  if (role === "job_seeker") {
    return [
      { title: "My Profile", path: "/jobseeker/profile", icon: UserRound },
      { title: "Settings", path: "/jobseeker/settings", icon: Settings },
    ];
  }

  return [];
});
</script>

<template>
  <Transition name="sidebar">
    <aside
      v-if="uiStore.isSidebarOpen"
      class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col border-r border-slate-200 bg-white shadow-xl"
    >
      <!-- Sidebar Header -->
      <div
        class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-100 px-5"
      >
        <div
          class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white"
        >
          <BriefcaseBusiness :size="21" />
        </div>

        <h2 class="text-lg font-bold text-blue-700">
          JobConnect <span class="text-slate-900">AI</span>
        </h2>

        <button
          type="button"
          class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100"
          aria-label="Close sidebar"
          @click="uiStore.closeSidebar()"
        >
          <X :size="20" />
        </button>
      </div>

      <!-- Main Navigation -->
      <nav class="flex-1 overflow-y-auto px-4 py-6">
        <p
          class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400"
        >
          Main Menu
        </p>

        <div class="space-y-1">
          <RouterLink
            v-for="item in navigationItems"
            :key="item.path"
            :to="item.path"
            class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-600"
            active-class="!bg-blue-50 !text-blue-600 font-semibold"
            @click="uiStore.closeSidebar()"
          >
            <component :is="item.icon" :size="19" />
            <span>{{ item.title }}</span>
          </RouterLink>
        </div>

        <!-- Account Navigation -->
        <template v-if="accountItems.length">
          <p
            class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400"
          >
            Account
          </p>

          <div class="space-y-1">
            <RouterLink
              v-for="item in accountItems"
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-600"
              active-class="!bg-blue-50 !text-blue-600 font-semibold"
              @click="uiStore.closeSidebar()"
            >
              <component :is="item.icon" :size="19" />
              <span>{{ item.title }}</span>
            </RouterLink>
          </div>
        </template>
      </nav>
    </aside>
  </Transition>
</template>

<style scoped>
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}
</style>
