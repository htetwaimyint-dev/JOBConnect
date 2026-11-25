<script setup lang="ts">
import { computed } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";

import { useUiStore } from "@/stores/ui.store";
import { useAuthStore } from "@/stores/auth.store";

import {
  BriefcaseBusiness,
  UserRound,
  Search,
  Bookmark,
  FileText,
  CalendarDays,
  LayoutDashboard,
  Gift,
  Settings,
  LogOut,
  X,
} from "lucide-vue-next";

const route = useRoute();
const router = useRouter();

const uiStore = useUiStore();
const authStore = useAuthStore();

// Workspace navigation
const workspaceItems = [
  {
    label: "Dashboard",
    path: "/jobseeker/dashboard",
    icon: LayoutDashboard,
  },
  {
    label: "Find Jobs",
    path: "/jobseeker/jobs",
    icon: Search,
  },
  {
    label: "Saved Jobs",
    path: "/jobseeker/saved-jobs",
    icon: Bookmark,
  },
  {
    label: "My Applications",
    path: "/jobseeker/applications",
    icon: FileText,
  },
  {
    label: "Interviews",
    path: "/jobseeker/interviews",
    icon: CalendarDays,
  },
  {
    label: "Job Offers",
    path: "/jobseeker/job-offers",
    icon: Gift,
  },
];

// Account navigation
const accountItems = [
  {
    label: "My Profile",
    path: "/jobseeker/profile",
    icon: UserRound,
  },
];

// User information
const userName = computed(() => authStore.user?.name || "Job Seeker");

const userInitial = computed(
  () => userName.value.trim().charAt(0).toUpperCase() || "U",
);

// Active route
const isActive = (path: string) => {
  if (path === "/jobseeker/dashboard") {
    return route.path === path;
  }

  return route.path === path || route.path.startsWith(`${path}/`);
};

// Navigation
const handleNavigation = () => {
  uiStore.closeSidebar();
};

// Logout
const handleLogout = async () => {
  try {
    uiStore.closeSidebar();

    await authStore.logout();

    await router.replace("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};

// Shared navigation styles
const menuClass =
  "flex min-h-[44px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200";

const activeMenuClass = "bg-[#102D59] font-semibold text-white shadow-sm";
</script>

<template>
  <!-- Background Overlay -->
  <Transition name="sidebar-overlay">
    <div
      v-if="uiStore.isSidebarOpen"
      class="fixed inset-0 z-40 bg-slate-900/25 backdrop-blur-[2px]"
      @click="uiStore.closeSidebar()"
    ></div>
  </Transition>

  <!-- Sidebar -->
  <Transition name="sidebar">
    <aside
      v-if="uiStore.isSidebarOpen"
      class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col border-r border-slate-200 bg-white shadow-2xl"
      aria-label="Job Seeker navigation"
    >
      <!-- Header -->
      <div
        class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-5"
      >
        <RouterLink
          to="/jobseeker/dashboard"
          class="flex items-center gap-3 no-underline"
          @click="handleNavigation"
        >
          <div
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#102D59] text-white"
          >
            <BriefcaseBusiness :size="20" />
          </div>

          <h2 class="text-lg font-bold tracking-tight text-[#102D59]">
            JobConnect
            <span class="text-blue-600">AI</span>
          </h2>
        </RouterLink>

        <button
          type="button"
          class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-[#102D59]"
          aria-label="Close sidebar"
          @click="uiStore.closeSidebar()"
        >
          <X :size="20" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto px-4 py-6">
        <!-- Workspace -->
        <div>
          <p
            class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400"
          >
            Workspace
          </p>

          <div class="space-y-1">
            <RouterLink
              v-for="item in workspaceItems"
              :key="item.path"
              :to="item.path"
              :class="[
                menuClass,
                isActive(item.path)
                  ? activeMenuClass
                  : 'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
              ]"
              @click="handleNavigation"
            >
              <component :is="item.icon" :size="19" class="shrink-0" />

              <span class="flex-1">
                {{ item.label }}
              </span>
            </RouterLink>
          </div>
        </div>

        <!-- Account -->
        <div class="mt-8">
          <p
            class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400"
          >
            Account
          </p>

          <div class="space-y-1">
            <RouterLink
              v-for="item in accountItems"
              :key="item.path"
              :to="item.path"
              :class="[
                menuClass,
                isActive(item.path)
                  ? activeMenuClass
                  : 'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
              ]"
              @click="handleNavigation"
            >
              <component :is="item.icon" :size="19" class="shrink-0" />

              <span class="flex-1">
                {{ item.label }}
              </span>
            </RouterLink>
          </div>
        </div>
      </nav>

      <!-- User Account Footer -->
      <div class="shrink-0 border-t border-slate-100 p-4">
        <!-- User Information -->
        <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#102D59] text-sm font-bold text-white"
          >
            {{ userInitial }}
          </div>

          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-slate-800">
              {{ userName }}
            </p>

            <p class="text-xs font-medium text-slate-500">Job Seeker</p>
          </div>
        </div>

        <!-- Settings -->
        <RouterLink
          to="/jobseeker/settings"
          :class="[
            menuClass,
            isActive('/jobseeker/settings')
              ? activeMenuClass
              : 'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
          ]"
          @click="handleNavigation"
        >
          <Settings :size="19" class="shrink-0" />

          <span>Settings</span>
        </RouterLink>

        <!-- Logout -->
        <button
          type="button"
          class="mt-1 flex min-h-[44px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
          @click="handleLogout"
        >
          <LogOut :size="19" class="shrink-0" />

          <span>Logout</span>
        </button>
      </div>
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

.sidebar-overlay-enter-active,
.sidebar-overlay-leave-active {
  transition: opacity 0.3s ease;
}

.sidebar-overlay-enter-from,
.sidebar-overlay-leave-to {
  opacity: 0;
}
</style>
