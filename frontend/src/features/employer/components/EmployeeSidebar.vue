<script setup lang="ts">
import { useRoute, RouterLink } from "vue-router";

import {
  LayoutDashboard,
  BriefcaseBusiness,
  Users,
  UserRound,
  UserSearch,
  Send,
  LogOut,
} from "lucide-vue-next";

const route = useRoute();

const menuItems = [
  {
    name: "Dashboard",
    path: "/employee/dashboard",
    icon: LayoutDashboard,
  },
  {
    name: "Job Create Post",
    path: "/employee/jobs/create",
    icon: BriefcaseBusiness,
  },
  {
    name: "Applicants",
    path: "/employee/applicants",
    icon: Users,
  },
  {
    name: "My Account",
    path: "/employee/account",
    icon: UserRound,
  },
  {
    name: "JobSeeker Profile",
    path: "/employee/job-seekers",
    icon: UserSearch,
  },
  {
    name: "Direct Offer",
    path: "/employee/direct-offers",
    icon: Send,
  },
];

const isActive = (path: string) => {
  return route.path === path;
};

const handleLogout = () => {
  console.log("Employee Logout");
};
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
    <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6">
      <RouterLink
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive(item.path)
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
        "
      >
        <component :is="item.icon" :size="20" :stroke-width="2" />

        <span>{{ item.name }}</span>
      </RouterLink>
    </nav>

    <!-- Logout -->
    <div class="border-t border-slate-200 p-4">
      <button
        type="button"
        @click="handleLogout"
        class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-red-500 transition-colors hover:bg-red-50"
      >
        <LogOut :size="20" />
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>
