<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";
import {
  Menu,
  Bell,
  UserRound,
  BriefcaseBusiness,
  Settings,
  LogOut,
  ChevronDown,
  X,
} from "lucide-vue-next";

import { useAuthStore } from "@/stores/auth.store";
import { useUiStore } from "@/stores/ui.store";
import { useUserStore } from "@/stores/user.store";

const route = useRoute();
const router = useRouter();

const authStore = useAuthStore();
const uiStore = useUiStore();
const userStore = useUserStore();

const isProfileOpen = ref(false);
const isNotificationOpen = ref(false);

const pageTitle = computed(() => {
  return route.meta.title || "Dashboard";
});

const userName = computed(() => {
  return authStore.user?.name || "User";
});

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase();
});

const closeMenus = () => {
  isProfileOpen.value = false;
  isNotificationOpen.value = false;
};

const profilePhoto = computed(() => userStore.profilePhoto);

const profilePath = computed(() => {
  if (authStore.user?.role === "employer") {
    return "/employer/profile";
  }

  if (authStore.user?.role === "admin") {
    return "/admin/dashboard";
  }

  return "/jobseeker/profile";
});

const settingsPath = computed(() => {
  const role = authStore.user?.role;

  if (role === "employer") return "/employer/settings";
  if (role === "admin") return "/admin/settings";

  return "/jobseeker/settings";
});

const toggleProfile = () => {
  isProfileOpen.value = !isProfileOpen.value;
  isNotificationOpen.value = false;
};

const toggleNotifications = () => {
  isNotificationOpen.value = !isNotificationOpen.value;
  isProfileOpen.value = false;
};

const logout = async () => {
  try {
    await authStore.logout();
    userStore.clearProfilePhoto();
    uiStore.closeSidebar();

    isProfileOpen.value = false;
    isNotificationOpen.value = false;

    await router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};
</script>

<template>
  <header
    class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white/150 px-2 shadow-sm backdrop-blur-md sm:px-6 lg:px-8"
  >
    <!-- Left Section -->
    <div class="flex min-w-0 items-center gap-4">
      <!-- Mobile Sidebar Toggle -->
      <button
        type="button"
        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-blue-600 lg:hidden"
        aria-label="Toggle sidebar"
        @click="uiStore.toggleSidebar()"
      >
        <Menu v-if="!uiStore.isSidebarOpen" :size="23" />
        <X v-else :size="23" />
      </button>

      <!-- Logo -->
      <RouterLink
        to="/"
        class="flex shrink-0 items-center gap-2 no-underline"
        @click="closeMenus"
      >
        <span
          class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm sm:h-10 sm:w-10"
        >
          <BriefcaseBusiness :size="22" />
        </span>

        <h2
          class="m-0 whitespace-nowrap text-sm font-bold tracking-tight text-blue-700 sm:text-xl"
        >
          JobConnect <span class="text-slate-900">AI</span>
        </h2>
      </RouterLink>
    </div>

    <!-- Right Section -->
    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
      <!-- Notification -->
      <div class="relative">
        <button
          type="button"
          aria-label="Notifications"
          class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-blue-600"
          @click="toggleNotifications"
        >
          <Bell :size="21" />

          <span
            class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"
          ></span>
        </button>

        <!-- Notification Dropdown -->
        <div
          v-if="isNotificationOpen"
          class="absolute right-0 top-12 z-50 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-xl sm:w-80"
        >
          <div
            class="flex items-center justify-between border-b border-slate-100 pb-3"
          >
            <h3 class="font-semibold text-slate-800">Notifications</h3>

            <button
              type="button"
              aria-label="Close notifications"
              class="text-slate-400 hover:text-slate-700"
              @click="isNotificationOpen = false"
            >
              <X :size="18" />
            </button>
          </div>

          <div class="py-6 text-center">
            <Bell :size="28" class="mx-auto mb-2 text-slate-300" />

            <p class="text-sm text-slate-500">No notifications yet.</p>
          </div>
        </div>
      </div>

      <!-- Profile Dropdown -->
      <div class="relative">
        <button
          type="button"
          class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:px-2"
          @click="toggleProfile"
        >
          <img
            v-if="profilePhoto"
            :src="profilePhoto"
            alt="Profile"
            class="h-9 w-9 rounded-full object-cover"
          />

          <div
            v-else
            class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700"
          >
            {{ userInitial }}
          </div>

          <div class="hidden text-left sm:block">
            <p class="max-w-32 truncate text-sm font-semibold text-slate-800">
              {{ userName }}
            </p>

            <p class="text-xs text-slate-500">
              {{ authStore.user?.role || "User" }}
            </p>
          </div>

          <ChevronDown
            :size="16"
            class="hidden text-slate-500 transition sm:block"
            :class="{ 'rotate-180': isProfileOpen }"
          />
        </button>

        <!-- Profile Dropdown Menu -->
        <div
          v-if="isProfileOpen"
          class="absolute right-0 top-14 z-50 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-xl"
        >
          <RouterLink
            :to="profilePath"
            class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-blue-600"
            @click="isProfileOpen = false"
          >
            <UserRound :size="18" />
            My Profile
          </RouterLink>

          <RouterLink
            :to="settingsPath"
            class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-blue-600"
            @click="isProfileOpen = false"
          >
            <Settings :size="18" />
            Settings
          </RouterLink>

          <div class="my-1 border-t border-slate-100"></div>

          <button
            type="button"
            class="flex w-full items-center gap-3 px-4 py-3 text-sm text-red-600 transition hover:bg-red-50"
            @click="logout"
          >
            <LogOut :size="18" />
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
