<script setup lang="ts">
import { ref } from "vue";
import { RouterLink, useRoute } from "vue-router";

import {
  LayoutDashboard,
  Users,
  Building2,
  UserRound,
  ChevronDown,
  ShieldCheck,
  ClipboardCheck,
  Tags,
  ChartNoAxesCombined,
  UserCircle,
  LogOut,
} from "lucide-vue-next";

const route = useRoute();

const isUsersOpen = ref(route.path.startsWith("/admin/users"));

const toggleUsers = () => {
  isUsersOpen.value = !isUsersOpen.value;
};

const isActive = (path: string) => {
  return route.path === path;
};

const handleLogout = () => {
  console.log("Admin Logout");
};
</script>

<template>
  <aside
    class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col border-r border-slate-200 bg-white"
  >
    <!-- Sidebar Header -->
    <div class="flex h-20 items-center border-b border-slate-200 px-6">
      <div>
        <p class="m-1 text-xl font-semibold text-slate-800">Admin Portal</p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6">
      <!-- Dashboard -->
      <RouterLink
        to="/admin/dashboard"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/dashboard')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <LayoutDashboard :size="20" />
        <span>Dashboard</span>
      </RouterLink>

      <!-- Users Dropdown -->
      <div>
        <button
          type="button"
          @click="toggleUsers"
          class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-medium text-slate-600 transition-colors hover:bg-blue-50 hover:text-blue-600"
        >
          <span class="flex items-center gap-3">
            <Users :size="20" />
            Users
          </span>

          <ChevronDown
            :size="16"
            class="transition-transform"
            :class="{ 'rotate-180': isUsersOpen }"
          />
        </button>

        <div
          v-if="isUsersOpen"
          class="ml-5 mt-1 space-y-1 border-l border-blue-100 pl-4"
        >
          <RouterLink
            to="/admin/users/companies"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors"
            :class="
              isActive('/admin/users/companies')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-blue-50 hover:text-blue-600'
            "
          >
            <Building2 :size="18" />
            Companies
          </RouterLink>

          <RouterLink
            to="/admin/users/job-seekers"
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors"
            :class="
              isActive('/admin/users/job-seekers')
                ? 'bg-blue-50 text-blue-600'
                : 'text-slate-500 hover:bg-blue-50 hover:text-blue-600'
            "
          >
            <UserRound :size="18" />
            Job Seekers
          </RouterLink>
        </div>
      </div>

      <!-- Verification -->
      <RouterLink
        to="/admin/verification"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/verification')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <ShieldCheck :size="20" />
        <span>Verification</span>
      </RouterLink>

      <!-- Job Approvals -->
      <RouterLink
        to="/admin/job-approvals"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/job-approvals')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <ClipboardCheck :size="20" />
        <span>Job Approvals</span>
      </RouterLink>

      <!-- Categories -->
      <RouterLink
        to="/admin/categories"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/categories')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <Tags :size="20" />
        <span>Categories</span>
      </RouterLink>

      <!-- Reports -->
      <RouterLink
        to="/admin/reports"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/reports')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <ChartNoAxesCombined :size="20" />
        <span>Reports</span>
      </RouterLink>

      <!-- My Account -->
      <RouterLink
        to="/admin/account"
        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors"
        :class="
          isActive('/admin/account')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
        "
      >
        <UserCircle :size="20" />
        <span>My Account</span>
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
