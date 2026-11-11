<script setup lang="ts">
import { ref, computed } from "vue";
import { useRouter, RouterLink } from "vue-router";

import { useAuthStore } from "@/stores/auth.store";
import { useUserStore } from "@/stores/user.store";
import { useUiStore } from "@/stores/ui.store";

import {
  Bell,
  ChevronDown,
  UserRound,
  LogOut,
  Menu,
  X,
  Sparkles,
} from "lucide-vue-next";

const router = useRouter();

const authStore = useAuthStore();
const userStore = useUserStore();
const uiStore = useUiStore();

const showProfileMenu = ref(false);
const showNotifications = ref(false);

const props = withDefaults(
  defineProps<{
    title?: string;
  }>(),
  {
    title: "Job Seeker Portal",
  },
);

const userName = computed(() => {
  return authStore.user?.name || "User";
});

const userRole = computed(() => {
  const roles: Record<string, string> = {
    job_seeker: "Job Seeker",
    employer: "Employer",
    admin: "Administrator",
  };

  const role = authStore.user?.role;

  return role ? roles[role] || role : "User";
});

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase();
});

const profilePhoto = computed(() => {
  return userStore.profilePhoto;
});

const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
  showNotifications.value = false;
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showProfileMenu.value = false;
};

const closeMenus = () => {
  showProfileMenu.value = false;
  showNotifications.value = false;
};

const handleLogout = async () => {
  closeMenus();

  try {
    await authStore.logout();
  } catch (error) {
    console.error("Logout failed:", error);
  } finally {
    userStore.clearProfilePhoto();
    uiStore.closeSidebar();
    await router.replace("/login");
  }
};
</script>

<template>
  <header class="sticky top-0 z-[60] flex h-20 w-full items-center justify-between
           border-b border-slate-200 bg-white/95 px-4 shadow-sm
           backdrop-blur-md sm:px-6 lg:px-8">
    <!-- Left Section -->
    <div class="flex min-w-0 items-center gap-3">

      <!-- Mobile Sidebar Toggle -->
      <button type="button" class="rounded-xl p-2.5 text-slate-600 transition-all
               hover:bg-blue-50 hover:text-blue-600" aria-label="Toggle sidebar" @click="uiStore.toggleSidebar">
        <X v-if="uiStore.isSidebarOpen" :size="22" />
        <Menu v-else :size="22" />
      </button>

      <!-- Page Title -->
      <div class="min-w-0">
        <div class="flex items-center gap-2">
          <Sparkles :size="17" class="hidden shrink-0 text-blue-500 sm:block" />

          <h2 class="truncate text-lg font-bold tracking-tight text-slate-800 sm:text-xl">
            {{ props.title }}
          </h2>
        </div>

        <p class="hidden text-xs text-slate-400 sm:block">
          Welcome back! Manage your career journey.
        </p>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex shrink-0 items-center gap-2 sm:gap-4">

      <!-- Notifications -->
      <div class="relative">
        <button type="button" aria-label="Notifications" :aria-expanded="showNotifications" class="relative flex h-11 w-11 items-center justify-center
                 rounded-xl border border-slate-200 bg-white
                 text-slate-600 transition-all
                 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600" @click="toggleNotifications">
          <Bell :size="20" />

          <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full
                   border-2 border-white bg-red-500" />
        </button>

        <!-- Notification Dropdown -->
        <Transition name="dropdown">
          <div v-if="showNotifications" class="absolute right-0 top-14 z-50 w-72
                   rounded-2xl border border-slate-200 bg-white
                   p-4 shadow-xl shadow-slate-900/10 sm:w-80">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
              <div>
                <h3 class="font-bold text-slate-800">
                  Notifications
                </h3>
                <p class="mt-0.5 text-xs text-slate-400">
                  Stay updated with your activities
                </p>
              </div>

              <button type="button" class="rounded-lg px-2 py-1 text-xs font-medium
                       text-slate-400 transition hover:bg-slate-100
                       hover:text-slate-700" @click="showNotifications = false">
                Close
              </button>
            </div>

            <div class="py-8 text-center">
              <div class="mx-auto mb-3 flex h-14 w-14 items-center
                       justify-center rounded-full bg-blue-50">
                <Bell :size="25" class="text-blue-400" />
              </div>

              <p class="text-sm font-semibold text-slate-700">
                No notifications yet
              </p>

              <p class="mt-1 text-xs text-slate-400">
                Your notifications will appear here.
              </p>
            </div>
          </div>
        </Transition>
      </div>

      <!-- Divider -->
      <div class="hidden h-9 w-px bg-slate-200 sm:block" />

      <!-- User Profile -->
      <div class="relative">

        <!-- Profile Button -->
        <button type="button" :aria-expanded="showProfileMenu" aria-label="User profile menu" class="flex items-center gap-2 rounded-xl border border-transparent
                 p-1.5 transition-all hover:border-slate-200
                 hover:bg-slate-50 sm:gap-3" @click="toggleProfileMenu">
          <!-- Avatar -->
          <div class="flex h-10 w-10 shrink-0 items-center justify-center
                   overflow-hidden rounded-xl bg-gradient-to-br
                   from-blue-500 to-indigo-600 text-sm font-bold text-white
                   shadow-md shadow-blue-500/20">
            <img v-if="profilePhoto" :src="profilePhoto" alt="Profile Photo" class="h-full w-full object-cover" />

            <span v-else>
              {{ userInitial }}
            </span>
          </div>

          <!-- User Information -->
          <div class="hidden text-left sm:block">
            <p class="max-w-32 truncate text-sm font-bold text-slate-800">
              {{ userName }}
            </p>

            <p class="text-xs font-medium text-slate-400">
              {{ userRole }}
            </p>
          </div>

          <ChevronDown :size="17" class="hidden text-slate-400 transition-transform sm:block"
            :class="{ 'rotate-180': showProfileMenu }" />
        </button>

        <!-- Profile Dropdown -->
        <Transition name="dropdown">
          <div v-if="showProfileMenu" class="absolute right-0 top-14 z-50 w-60
                   rounded-2xl border border-slate-200 bg-white
                   p-2 shadow-xl shadow-slate-900/10">
            <!-- User Details -->
            <div class="border-b border-slate-100 px-3 py-3">
              <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center
                         overflow-hidden rounded-xl bg-gradient-to-br
                         from-blue-500 to-indigo-600 text-sm font-bold text-white">
                  <img v-if="profilePhoto" :src="profilePhoto" alt="Profile Photo" class="h-full w-full object-cover" />

                  <span v-else>
                    {{ userInitial }}
                  </span>
                </div>

                <div class="min-w-0">
                  <p class="truncate text-sm font-bold text-slate-800">
                    {{ userName }}
                  </p>

                  <p class="text-xs text-slate-400">
                    {{ userRole }}
                  </p>
                </div>
              </div>
            </div>

            <!-- My Profile -->
            <RouterLink to="/jobseeker/profile" class="mt-2 flex items-center gap-3 rounded-xl px-3 py-3
                     text-sm font-medium text-slate-600 no-underline
                     transition-all hover:bg-blue-50 hover:text-blue-600" @click="closeMenus">
              <UserRound :size="18" />
              My Profile
            </RouterLink>

            <!-- Divider -->
            <div class="my-1 border-t border-slate-100" />

            <!-- Logout -->
            <button type="button" class="flex w-full items-center gap-3 rounded-xl px-3 py-3
                     text-sm font-medium text-red-500 transition-all
                     hover:bg-red-50" @click="handleLogout">
              <LogOut :size="18" />
              Logout
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition:
    opacity 0.15s ease,
    transform 0.15s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>