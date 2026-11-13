<script setup lang="ts">
import { computed } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";
import { useUiStore } from "@/stores/ui.store";
import { useAuthStore } from "@/stores/auth.store";

import {
  LayoutDashboard,
  UserRound,
  Search,
  Bookmark,
  FileText,
  CalendarDays,
  Gift,
  Settings,
  LogOut,
  X,
  Sparkles,
  ChevronRight,
} from "lucide-vue-next";

const route = useRoute();
const router = useRouter();

const uiStore = useUiStore();
const authStore = useAuthStore();

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
    path: "/jobseeker/offers",
    icon: Gift,
  },
];

const accountItems = [
  {
    label: "My Profile",
    path: "/jobseeker/profile",
    icon: UserRound,
  },
  {
    label: "Settings",
    path: "/jobseeker/settings",
    icon: Settings,
  },
];

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(`${path}/`);
};

const userName = computed(() => {
  return authStore.user?.name || "Job Seeker";
});

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase();
});

const handleNavigation = () => {
  uiStore.closeSidebar();
};

const handleLogout = async () => {
  try {
    await authStore.logout();
    uiStore.closeSidebar();
    await router.replace("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};
</script>

<template>

  <aside class="fixed left-0 top-0 z-50 flex h-screen w-72 flex-col
         bg-[#101B35] text-white shadow-2xl
         transition-transform duration-300" :class="uiStore.isSidebarOpen
          ? 'translate-x-0'
          : '-translate-x-full'">

    <!-- Brand -->
    <div class="flex h-20 shrink-0 items-center justify-between border-b border-white/10 px-6">
      <RouterLink to="/jobseeker/dashboard" class="flex items-center gap-3 no-underline" @click="handleNavigation">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl
                 bg-gradient-to-br from-blue-400 to-indigo-600 shadow-lg shadow-blue-900/40">
          <Sparkles :size="23" class="text-white" />
        </div>

        <div>
          <h2 class="text-xl font-bold tracking-wide text-white">
            JobConnect
          </h2>
          <p class="text-[11px] font-medium tracking-wider text-blue-300">
            JOB SEEKER PORTAL
          </p>
        </div>
      </RouterLink>

      <button type="button" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white"
        aria-label="Close sidebar" @click="uiStore.closeSidebar">
        <X :size="20" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-6">

      <!-- Workspace -->
      <div class="mb-7">
        <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.15em] text-slate-500">
          Workspace
        </p>

        <div class="space-y-1.5">
          <RouterLink v-for="item in workspaceItems" :key="item.path" :to="item.path" class="group relative flex items-center gap-3 rounded-xl
                   px-3 py-3 text-sm font-medium no-underline
                   transition-all duration-200" :class="isActive(item.path)
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/40'
                    : 'text-slate-400 hover:bg-white/10 hover:text-white'
                    " @click="handleNavigation">
            <component :is="item.icon" :size="19" :stroke-width="isActive(item.path) ? 2.5 : 2" />

            <span class="flex-1">{{ item.label }}</span>

            <ChevronRight v-if="isActive(item.path)" :size="16" class="text-blue-100" />
          </RouterLink>
        </div>
      </div>

      <!-- Account -->
      <div>
        <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.15em] text-slate-500">
          Account
        </p>

        <div class="space-y-1.5">
          <RouterLink v-for="item in accountItems" :key="item.path" :to="item.path" class="group flex items-center gap-3 rounded-xl
                   px-3 py-3 text-sm font-medium no-underline
                   transition-all duration-200" :class="isActive(item.path)
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/40'
                    : 'text-slate-400 hover:bg-white/10 hover:text-white'
                    " @click="handleNavigation">
            <component :is="item.icon" :size="19" />
            <span class="flex-1">{{ item.label }}</span>
            <ChevronRight v-if="isActive(item.path)" :size="16" class="text-blue-100" />
          </RouterLink>
        </div>
      </div>
    </nav>

    <!-- User Info -->
    <div class="shrink-0 border-t border-white/10 p-4">
      <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/5 p-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                 bg-gradient-to-br from-blue-400 to-indigo-500
                 text-sm font-bold text-white">
          {{ userInitial }}
        </div>

        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-white">
            {{ userName }}
          </p>
          <p class="text-xs text-slate-400">Job Seeker</p>
        </div>
      </div>

      <!-- Logout -->
      <button type="button" class="flex w-full items-center gap-3 rounded-xl px-3 py-3
               text-sm font-medium text-red-300 transition-colors
               hover:bg-red-500/10 hover:text-red-200" @click="handleLogout">
        <LogOut :size="19" />
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>
