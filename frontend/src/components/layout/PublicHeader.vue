<script setup lang="ts">
import { computed, ref, watch } from "vue";
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
} from "lucide-vue-next";

import { useAuthStore } from "@/stores/auth.store";
import { useUserStore } from "@/stores/user.store";

const authStore = useAuthStore();
const userStore = useUserStore();

const router = useRouter();
const route = useRoute();

// Local State
const isMenuOpen = ref(false);
const isProfileMenuOpen = ref(false);
const searchQuery = ref("");
const imageFailed = ref(false);

// Authentication
const isAuthenticated = computed(() => authStore.isAuthenticated);

const userRole = computed(() => authStore.user?.role);

const isJobSeeker = computed(
  () => isAuthenticated.value && userRole.value === "job_seeker",
);

const isEmployer = computed(
  () => isAuthenticated.value && userRole.value === "employer",
);

const isAdmin = computed(
  () => isAuthenticated.value && userRole.value === "admin",
);

// User Information
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

// Role-based Routes
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

// Navigation Links
const navigationLinks = computed(() => {
  if (isJobSeeker.value) {
    return [
      { label: "Dashboard", path: "/jobseeker/dashboard" },
      { label: "Find Jobs", path: "/jobseeker/jobs" },
      { label: "Saved Jobs", path: "/jobseeker/saved-jobs" },
      { label: "Applications", path: "/jobseeker/applications" },
      { label: "Interviews", path: "/jobseeker/interviews" },
      { label: "Job Offers", path: "/jobseeker/offers" },
    ];
  }

  if (isEmployer.value) {
    return [
      { label: "Company Profile", path: "/employer/profile" },
      { label: "Edit Company Profile", path: "/employer/profile/edit" },
      { label: "Settings", path: "/employer/settings" },
    ];
  }

  if (isAdmin.value) {
    return [
      { label: "Dashboard", path: "/admin/dashboard" },
      { label: "Settings", path: "/admin/settings" },
    ];
  }

  return [
    { label: "Find Jobs", path: "/jobs" },
    { label: "About", path: "/about" },
    { label: "Contact", path: "/contact" },
  ];
});

// Menu Controls
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
};

const closeMenus = () => {
  closeMenu();
  closeProfileMenu();
};

// Close menus when route changes
watch(
  () => route.fullPath,
  () => {
    closeMenus();
  },
);

// Job Search
const searchJobs = async () => {
  const keyword = searchQuery.value.trim();

  const path = isJobSeeker.value ? "/jobseeker/jobs" : "/jobs";

  closeMenus();

  await router.push({
    path,
    query: keyword ? { search: keyword } : {},
  });
};

// Logout
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
  <header class="site-header sticky top-0 z-30 w-full bg-white">
    <!-- Main Header -->
    <div
      class="flex w-full flex-nowrap items-center gap-2 border-b border-slate-100 px-3 py-3 sm:gap-4 sm:px-6 lg:justify-between lg:gap-5 lg:px-10"
    >
      <!-- Mobile Hamburger -->
      <button
        type="button"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 lg:hidden"
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

      <!-- Desktop Navigation -->
      <nav
        class="hidden flex-1 items-center justify-center gap-5 lg:flex xl:gap-7"
      >
        <RouterLink
          to="/"
          class="nav-link"
          exact-active-class="router-link-active"
        >
          Home
        </RouterLink>

        <RouterLink
          v-for="link in navigationLinks"
          :key="link.path"
          :to="link.path"
          class="nav-link"
        >
          {{ link.label }}
        </RouterLink>
      </nav>

      <!-- Desktop Search & Actions -->
      <div class="hidden shrink-0 items-center gap-2 lg:flex">
        <!-- Search -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search jobs..."
            aria-label="Search jobs"
            class="h-10 w-36 rounded-full border border-slate-200 bg-slate-50 px-4 pr-10 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white xl:w-48"
            @keyup.enter="searchJobs"
          />

          <button
            type="button"
            class="absolute right-1 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-blue-600 transition hover:bg-blue-50"
            aria-label="Search jobs"
            @click="searchJobs"
          >
            <Search :size="18" />
          </button>
        </div>

        <!-- Guest Actions -->
        <template v-if="!isAuthenticated">
          <RouterLink to="/login" class="auth-button login-button">
            Login
          </RouterLink>

          <RouterLink to="/register" class="auth-button register-button">
            Register
          </RouterLink>
        </template>

        <!-- Authenticated Profile Dropdown -->
        <div v-else class="relative">
          <button
            type="button"
            class="flex items-center gap-2 rounded-full p-1 transition hover:bg-slate-100"
            aria-label="User profile menu"
            :aria-expanded="isProfileMenuOpen"
            @click="toggleProfileMenu"
          >
            <img
              v-if="showProfileImage"
              :src="profilePhoto!"
              alt="Profile"
              class="h-9 w-9 rounded-full border border-slate-200 object-cover"
              @error="imageFailed = true"
            />

            <span
              v-else
              class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white"
            >
              {{ userInitial }}
            </span>

            <ChevronDown :size="16" class="text-slate-500" />
          </button>

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
                @click="closeProfileMenu"
              >
                <UserRound :size="17" />
                My Profile
              </RouterLink>

              <RouterLink
                :to="settingsPath"
                class="dropdown-link"
                @click="closeProfileMenu"
              >
                <Settings :size="17" />
                Settings
              </RouterLink>

              <button
                type="button"
                class="dropdown-link w-full text-red-600 hover:bg-red-50"
                @click="handleLogout"
              >
                <LogOut :size="17" />
                Logout
              </button>
            </div>
          </Transition>
        </div>
      </div>

      <!-- Mobile Search -->
      <div class="flex min-w-0 flex-1 items-center gap-1.5 lg:hidden">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search jobs..."
          aria-label="Search jobs"
          class="h-9 min-w-0 flex-1 rounded-full border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white sm:text-sm"
          @keyup.enter="searchJobs"
        />

        <button
          type="button"
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-blue-600 transition hover:bg-blue-50"
          aria-label="Search jobs"
          @click="searchJobs"
        >
          <Search :size="21" />
        </button>
      </div>

      <!-- Mobile Profile Dropdown -->
      <div v-if="isAuthenticated" class="relative lg:hidden">
        <button
          type="button"
          class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-600 text-sm font-bold text-white transition hover:bg-blue-700 sm:h-10 sm:w-10"
          :title="userName"
          aria-label="User profile menu"
          :aria-expanded="isProfileMenuOpen"
          @click="toggleProfileMenu"
        >
          <img
            v-if="showProfileImage"
            :src="profilePhoto!"
            alt="Profile"
            class="h-full w-full object-cover"
            @error="imageFailed = true"
          />

          <span v-else>
            {{ userInitial }}
          </span>
        </button>

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
              @click="closeProfileMenu"
            >
              <UserRound :size="17" />
              My Profile
            </RouterLink>

            <RouterLink
              :to="settingsPath"
              class="dropdown-link"
              @click="closeProfileMenu"
            >
              <Settings :size="17" />
              Settings
            </RouterLink>

            <button
              type="button"
              class="dropdown-link w-full text-red-600 hover:bg-red-50"
              @click="handleLogout"
            >
              <LogOut :size="17" />
              Logout
            </button>
          </div>
        </Transition>
      </div>

      <!-- Mobile Login -->
      <RouterLink
        v-else
        to="/login"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 lg:hidden"
        aria-label="Login"
      >
        <UserRound :size="19" />
      </RouterLink>
    </div>

    <!-- Mobile Navigation Drawer -->
    <Transition name="mobile-menu">
      <div
        v-if="isMenuOpen"
        class="absolute left-0 top-full z-50 w-full border-b border-slate-100 bg-white px-5 py-5 shadow-lg lg:hidden"
      >
        <nav class="flex flex-col gap-1">
          <RouterLink to="/" class="mobile-nav-link" @click="closeMenu">
            Home
          </RouterLink>

          <RouterLink
            v-for="link in navigationLinks"
            :key="link.path"
            :to="link.path"
            class="mobile-nav-link"
            @click="closeMenu"
          >
            {{ link.label }}
          </RouterLink>

          <RouterLink to="/about" class="mobile-nav-link" @click="closeMenu">
            About
          </RouterLink>

          <RouterLink to="/contact" class="mobile-nav-link" @click="closeMenu">
            Contact
          </RouterLink>
        </nav>

        <!-- Guest Actions -->
        <div v-if="!isAuthenticated" class="mt-5 flex gap-3">
          <RouterLink
            to="/login"
            class="auth-button login-button flex-1"
            @click="closeMenu"
          >
            Login
          </RouterLink>

          <RouterLink
            to="/register"
            class="auth-button register-button flex-1"
            @click="closeMenu"
          >
            Register
          </RouterLink>
        </div>

        <!-- Authenticated User -->
        <div v-else class="mt-5 border-t border-slate-200 pt-4">
          <RouterLink
            :to="profilePath"
            class="flex items-center gap-3 rounded-xl bg-blue-50 p-3 no-underline"
            @click="closeMenu"
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
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 font-bold text-white"
            >
              {{ userInitial }}
            </span>

            <span class="min-w-0 flex-1">
              <span class="block truncate text-sm font-semibold text-slate-800">
                {{ userName }}
              </span>

              <span class="block text-xs capitalize text-blue-600">
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
      </div>
    </Transition>
  </header>
</template>

<style scoped>
.site-header :deep(a) {
  text-decoration: none;
}

/* Desktop Navigation */
.nav-link {
  padding: 6px 0;
  color: #334155;
  font-size: 14px;
  font-weight: 500;
  white-space: nowrap;
  transition: color 0.2s ease;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #2563eb;
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
  color: #2563eb;
  background-color: #eff6ff;
}

/* Authentication Buttons */
.auth-button {
  display: inline-flex;
  min-width: 88px;
  height: 40px;
  align-items: center;
  justify-content: center;
  padding: 0 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  transition:
    background-color 0.2s ease,
    border-color 0.2s ease;
}

.login-button {
  border: 1px solid #bfdbfe;
  color: #1d4ed8;
  background-color: white;
}

.login-button:hover {
  background-color: #eff6ff;
}

.register-button {
  border: 1px solid #2563eb;
  color: white;
  background-color: #2563eb;
}

.register-button:hover {
  border-color: #1d4ed8;
  background-color: #1d4ed8;
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

/* Transitions */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

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
