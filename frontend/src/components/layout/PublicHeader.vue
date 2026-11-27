<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";

import {
  Menu,
  X,
  Search,
  BriefcaseBusiness,
  UserRound,
  LogOut,
  Settings,
  ChevronDown,
  Bell,
} from "lucide-vue-next";

import { useAuthStore } from "@/stores/auth.store";
import { useUserStore } from "@/stores/user.store";

const authStore = useAuthStore();
const userStore = useUserStore();

const router = useRouter();
const route = useRoute();

const actionsRef = ref<HTMLElement | null>(null);

const isMenuOpen = ref(false);
const isProfileMenuOpen = ref(false);
const isNotificationOpen = ref(false);

const searchQuery = ref("");
const imageFailed = ref(false);

const isAuthenticated = computed(() => authStore.isAuthenticated);
const userRole = computed(() => authStore.user?.role);
const isJobSeeker = computed(() => userRole.value === "job_seeker");

const userName = computed(() => authStore.user?.name || "User");

const userInitial = computed(
  () => userName.value.trim().charAt(0).toUpperCase() || "U",
);

const userRoleLabel = computed(
  () => userRole.value?.replace(/_/g, " ") || "User",
);

const profilePhoto = computed(() => userStore.profilePhoto);

const showProfileImage = computed(
  () => Boolean(profilePhoto.value) && !imageFailed.value,
);

watch(profilePhoto, () => {
  imageFailed.value = false;
});

const profilePath = computed(() => {
  switch (userRole.value) {
    case "job_seeker":
      return "/jobseeker/profile";
    case "employer":
      return "/employer/profile";
    case "admin":
      return "/admin/dashboard";
    default:
      return "/";
  }
});

const settingsPath = computed(() => {
  switch (userRole.value) {
    case "job_seeker":
      return "/jobseeker/settings";
    case "employer":
      return "/employer/settings";
    case "admin":
      return "/admin/settings";
    default:
      return "/";
  }
});

const navigationLinks = computed(() => {
  switch (userRole.value) {
    case "job_seeker":
      return [
        { label: "Find Jobs", path: "/jobseeker/jobs" },
        { label: "Saved Jobs", path: "/jobseeker/saved-jobs" },
        { label: "Applications", path: "/jobseeker/applications" },
        { label: "Interviews", path: "/jobseeker/interviews" },
        { label: "Company Profiles", path: "/jobseeker/companyProfile" },
        { label: "Job Offers", path: "/jobseeker/job-offers" },
      ];

    case "employer":
      return [
        { label: "Company Profile", path: "/employer/profile" },
        { label: "Manage Jobs", path: "/employer/jobs" },
        { label: "Applicants", path: "/employer/applicants" },
        { label: "Job Seeker Profiles", path: "/employer/job-seekers" },
      ];

    case "admin":
      return [
        { label: "Dashboard", path: "/admin/dashboard" },
        { label: "Companies", path: "/admin/companies" },
        { label: "Job Seekers", path: "/admin/job-seekers" },
        { label: "Company Verify", path: "/admin/company-approvals" },
        { label: "Job Post Approval", path: "/admin/job-approvals" },
        { label: "Categories", path: "/admin/categories" },
        { label: "Reports", path: "/admin/reports" },
      ];

    default:
      return [
        { label: "Find Jobs", path: "/jobs" },
        { label: "About", path: "/about" },
        { label: "Contact", path: "/contact" },
      ];
  }
});

const closeMenu = () => {
  isMenuOpen.value = false;
};

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const closeProfileMenu = () => {
  isProfileMenuOpen.value = false;
};

const toggleProfileMenu = () => {
  isProfileMenuOpen.value = !isProfileMenuOpen.value;
  closeNotification();
};

const closeNotification = () => {
  isNotificationOpen.value = false;
};

const toggleNotification = () => {
  isNotificationOpen.value = !isNotificationOpen.value;
  closeProfileMenu();
};

const closeMenus = () => {
  closeMenu();
  closeProfileMenu();
  closeNotification();
};

const searchJobs = async () => {
  const keyword = searchQuery.value.trim();
  const path = isAuthenticated.value ? "/jobseeker/jobs" : "/jobs";

  closeMenus();

  await router.push({
    path,
    query: keyword ? { search: keyword } : {},
  });
};

const handleOutsideClick = (event: PointerEvent) => {
  const target = event.target as Node;

  if (actionsRef.value && !actionsRef.value.contains(target)) {
    closeProfileMenu();
    closeNotification();
  }
};

onMounted(() => {
  document.addEventListener("pointerdown", handleOutsideClick);
});

onBeforeUnmount(() => {
  document.removeEventListener("pointerdown", handleOutsideClick);
});

watch(
  () => route.fullPath,
  () => {
    closeMenus();
  },
);

const handleLogout = async () => {
  try {
    closeMenus();

    await authStore.logout();
    userStore.clearProfilePhoto();

    await router.replace("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};
</script>

<template>
  <header
    class="site-header sticky top-0 z-[100] flex h-16 w-full items-center border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-md"
  >
    <div
      class="flex w-full min-w-0 items-center justify-between px-3 sm:px-5 xl:px-8"
    >
      <!-- Left Group: Hamburger + Logo -->
      <div class="flex shrink-0 items-center gap-3 xl:gap-4">
        <!-- Mobile Hamburger -->
        <button
          type="button"
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 xl:hidden"
          :aria-label="isMenuOpen ? 'Close navigation' : 'Open navigation'"
          :aria-expanded="isMenuOpen"
          @click="toggleMenu"
        >
          <X v-if="isMenuOpen" :size="22" />
          <Menu v-else :size="22" />
        </button>

        <!-- Logo -->
        <RouterLink
          to="/"
          class="flex shrink-0 items-center gap-1 no-underline sm:gap-2"
          @click="closeMenus"
        >
          <span
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm sm:h-10 sm:w-10"
          >
            <BriefcaseBusiness :size="20" />
          </span>

          <h2
            class="m-0 whitespace-nowrap text-lg font-bold tracking-tight text-blue-700 sm:text-xl"
          >
            JobConnect
          </h2>
        </RouterLink>
      </div>

      <!-- Desktop Navigation -->
      <nav
        class="mx-2 hidden min-w-0 flex-1 items-center justify-center gap-2 xl:flex 2xl:mx-3"
      >
        <RouterLink
          to="/"
          class="nav-link"
          exact-active-class="router-link-active"
          @click="closeMenus"
        >
          Home
        </RouterLink>

        <RouterLink
          v-for="link in navigationLinks"
          :key="link.path"
          :to="link.path"
          class="nav-link"
          @click="closeMenus"
        >
          {{ link.label }}
        </RouterLink>
      </nav>

      <!-- Desktop Guest Actions -->
      <div
        v-if="!isAuthenticated"
        class="hidden shrink-0 items-center gap-2 xl:flex"
      >
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search jobs..."
            class="w-36 rounded-lg border border-slate-200 bg-slate-50 py-2 pl-3 pr-9 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 2xl:w-48"
            @keyup.enter="searchJobs"
          />

          <button
            type="button"
            class="absolute right-1 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-md text-slate-500 transition hover:bg-blue-50 hover:text-blue-600"
            aria-label="Search jobs"
            @click="searchJobs"
          >
            <Search :size="17" />
          </button>
        </div>

        <RouterLink
          to="/login"
          class="auth-button login-button"
          @click="closeMenus"
        >
          Login
        </RouterLink>

        <RouterLink
          to="/register"
          class="auth-button register-button"
          @click="closeMenus"
        >
          Register
        </RouterLink>
      </div>

      <!-- Mobile Guest Actions -->
      <div
        v-if="!isAuthenticated"
        class="flex shrink-0 items-center gap-2 xl:hidden"
      >
        <RouterLink
          to="/login"
          class="mobile-auth-button login-button"
          @click="closeMenus"
        >
          Login
        </RouterLink>

        <RouterLink
          to="/register"
          class="mobile-auth-button register-button"
          @click="closeMenus"
        >
          Register
        </RouterLink>
      </div>

      <!-- Authenticated Actions -->
      <div
        v-else
        ref="actionsRef"
        class="flex shrink-0 items-center gap-2 sm:gap-4"
      >
        <!-- Job Seeker Notification -->
        <div v-if="isJobSeeker" class="relative">
          <button
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-[#102D59]"
            aria-label="Notifications"
            :aria-expanded="isNotificationOpen"
            aria-haspopup="true"
            @click="toggleNotification"
          >
            <Bell :size="21" />
          </button>

          <Transition name="dropdown">
            <div
              v-if="isNotificationOpen"
              class="absolute right-0 top-full z-50 mt-3 w-80 max-w-[90vw] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
            >
              <div
                class="flex items-center justify-between border-b border-slate-100 px-4 py-3"
              >
                <h3 class="text-sm font-semibold text-slate-800">
                  Notifications
                </h3>

                <button
                  type="button"
                  class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                  aria-label="Close notifications"
                  @click="closeNotification"
                >
                  <X :size="16" />
                </button>
              </div>

              <div class="flex flex-col items-center px-5 py-8 text-center">
                <div
                  class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500"
                >
                  <Bell :size="22" />
                </div>

                <p class="text-sm font-semibold text-slate-700">
                  No notifications yet
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                  You'll see updates about your applications and job
                  opportunities here.
                </p>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Profile -->
        <div class="relative">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:px-2"
            :aria-expanded="isProfileMenuOpen"
            aria-haspopup="true"
            aria-label="Open profile menu"
            @click="toggleProfileMenu"
          >
            <img
              v-if="showProfileImage"
              :src="profilePhoto!"
              alt="Profile"
              class="h-9 w-9 rounded-full object-cover"
              @error="imageFailed = true"
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

              <p class="text-xs capitalize text-slate-500">
                {{ userRoleLabel }}
              </p>
            </div>

            <ChevronDown
              :size="16"
              class="hidden text-slate-500 transition sm:block"
              :class="{ 'rotate-180': isProfileMenuOpen }"
            />
          </button>

          <!-- Profile Dropdown -->
          <Transition name="dropdown">
            <div
              v-if="isProfileMenuOpen"
              class="absolute right-0 top-full z-50 mt-3 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
            >
              <div class="border-b border-slate-100 px-3 py-3">
                <p class="truncate text-sm font-semibold text-slate-800">
                  {{ userName }}
                </p>

                <p class="mt-1 text-xs capitalize text-slate-500">
                  {{ userRoleLabel }}
                </p>
              </div>

              <RouterLink
                :to="profilePath"
                class="dropdown-link"
                @click="closeMenus"
              >
                <UserRound :size="17" />
                My Profile
              </RouterLink>

              <RouterLink
                :to="settingsPath"
                class="dropdown-link"
                @click="closeMenus"
              >
                <Settings :size="17" />
                Settings
              </RouterLink>

              <button
                type="button"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm text-red-600 transition hover:bg-red-50"
                @click="handleLogout"
              >
                <LogOut :size="17" />
                Logout
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <Transition name="sidebar-overlay">
      <button
        v-if="isMenuOpen"
        type="button"
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-[2px] xl:hidden"
        aria-label="Close navigation menu"
        @click="closeMenu"
      ></button>
    </Transition>

    <!-- Mobile Sidebar -->
    <Transition name="sidebar">
      <aside
        v-if="isMenuOpen"
        class="fixed inset-y-0 left-0 z-[100] flex h-screen max-h-dvh w-[min(20rem,85vw)] flex-col overflow-hidden border-r border-slate-200 bg-white shadow-2xl xl:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Mobile navigation"
      >
        <div
          class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-5"
        >
          <RouterLink
            to="/"
            class="flex items-center gap-2 no-underline"
            @click="closeMenus"
          >
            <span
              class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#102D59] text-white"
            >
              <BriefcaseBusiness :size="21" />
            </span>

            <span class="text-lg font-bold text-[#102D59]"> JobConnect </span>
          </RouterLink>

          <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
            aria-label="Close navigation"
            @click="closeMenu"
          >
            <X :size="21" />
          </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
          <p
            class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400"
          >
            Navigation
          </p>

          <RouterLink
            to="/"
            class="mobile-nav-link"
            active-class="!bg-[#102D59] !text-white !font-semibold shadow-sm"
            @click="closeMenu"
          >
            Home
          </RouterLink>

          <RouterLink
            v-for="link in navigationLinks"
            :key="link.path"
            :to="link.path"
            class="mobile-nav-link"
            active-class="router-link-active"
            @click="closeMenu"
          >
            {{ link.label }}
          </RouterLink>
        </nav>

        <!-- Guest Footer -->
        <div
          v-if="!isAuthenticated"
          class="shrink-0 border-t border-slate-100 p-4"
        >
          <p class="mb-3 px-1 text-sm text-slate-500">
            Welcome to JobConnect AI
          </p>

          <RouterLink
            to="/login"
            class="auth-button login-button w-full"
            @click="closeMenu"
          >
            Login
          </RouterLink>

          <RouterLink
            to="/register"
            class="auth-button register-button mt-2 w-full"
            @click="closeMenu"
          >
            Register
          </RouterLink>
        </div>

        <!-- Authenticated Footer -->
        <div v-else class="shrink-0 border-t border-slate-100 p-4">
          <RouterLink
            :to="profilePath"
            class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 no-underline transition hover:bg-slate-100"
            @click="closeMenus"
          >
            <img
              v-if="showProfileImage"
              :src="profilePhoto!"
              alt="Profile"
              class="h-10 w-10 rounded-full object-cover"
              @error="imageFailed = true"
            />

            <span
              v-else
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#102D59] font-bold text-white"
            >
              {{ userInitial }}
            </span>

            <span class="min-w-0 flex-1">
              <span class="block truncate text-sm font-semibold text-slate-800">
                {{ userName }}
              </span>

              <span class="block text-xs capitalize text-slate-500">
                {{ userRoleLabel }}
              </span>
            </span>
          </RouterLink>

          <RouterLink
            :to="settingsPath"
            class="dropdown-link mt-2"
            @click="closeMenus"
          >
            <Settings :size="17" />
            Settings
          </RouterLink>

          <button
            type="button"
            class="mt-2 flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50"
            @click="handleLogout"
          >
            <LogOut :size="18" />
            Logout
          </button>
        </div>
      </aside>
    </Transition>
  </header>
</template>

<style scoped>
.site-header :deep(a) {
  text-decoration: none;
}

/* Desktop Navigation */
.nav-link {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 12px;
  border-radius: 8px;
  color: #334155;
  font-size: 14px;
  font-weight: 500;
  white-space: nowrap;
  transition:
    background-color 0.2s ease,
    color 0.2s ease;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #1d4ed8;
  background-color: #f1f5f9;
}

/* Auth Buttons */
.auth-button {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9px;
  padding: 9px 16px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s ease;
}

/* Mobile Auth Buttons */
.mobile-auth-button {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  padding: 7px 10px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s ease;
}

/* Login */
.login-button {
  border: 1px solid #dbe3ed;
  color: #334155;
  background-color: white;
}

.login-button:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
}

/* Register */
.register-button {
  border: 1px solid #2563eb;
  color: white;
  background-color: #2563eb;
}

.register-button:hover {
  background-color: #1d4ed8;
  border-color: #1d4ed8;
}

/* Mobile Navigation */
.mobile-nav-link {
  display: block;
  padding: 11px 12px;
  border-radius: 8px;
  color: #334155;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition:
    background-color 0.2s ease,
    color 0.2s ease;
}

.mobile-nav-link:hover,
.mobile-nav-link.router-link-active {
  color: #102d59;
  background-color: #f1f5f9;
}

/* Dropdown Links */
.dropdown-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  color: #334155;
  font-size: 14px;
  text-decoration: none;
  transition: background-color 0.2s ease;
}

.dropdown-link:hover {
  background-color: #f1f5f9;
}

/* Sidebar Overlay */
.sidebar-overlay-enter-active,
.sidebar-overlay-leave-active {
  transition: opacity 0.25s ease;
}

.sidebar-overlay-enter-from,
.sidebar-overlay-leave-to {
  opacity: 0;
}

/* Sidebar Slide */
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}

/* Dropdown */
.dropdown-enter-active,
.dropdown-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
