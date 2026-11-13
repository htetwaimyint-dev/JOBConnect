<script setup lang="ts">
import { computed, ref } from "vue";
import { RouterLink, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.store";

const authStore = useAuthStore();
const router = useRouter();

const isMenuOpen = ref(false);
const searchQuery = ref("");

const closeMenu = () => {
  isMenuOpen.value = false;
};

const userInitial = computed(() => {
  return authStore.user?.name?.charAt(0).toUpperCase() || "U";
});

const profilePath = computed(() => {
  switch (authStore.user?.role) {
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

const searchJobs = () => {
  closeMenu();

  router.push({
    path: "/jobs",
    query: searchQuery.value.trim()
      ? { search: searchQuery.value.trim() }
      : {},
  });
};
</script>

<template>
  <header class="site-header sticky top-0 z-50 w-full bg-white">
    <div class="flex w-full items-center justify-between gap-5 border-b border-blue-100 px-5 py-3 sm:px-8 lg:px-10">
      <!-- Logo -->
      <RouterLink to="/" class="flex shrink-0 items-center gap-3 no-underline" @click="closeMenu">
        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.8" class="h-7 w-7">
            <rect x="3" y="7" width="18" height="14" rx="2" />
            <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2" />
          </svg>
        </span>

        <h2 class="m-0 whitespace-nowrap text-xl font-bold text-blue-700 sm:text-2xl">
          JobConnect <span class="text-slate-900">AI</span>
        </h2>
      </RouterLink>

      <!-- Desktop Navigation -->
      <nav class="hidden flex-1 items-center justify-center gap-8 lg:flex xl:gap-10">
        <RouterLink to="/" class="nav-link">Home</RouterLink>
        <RouterLink to="/jobs" class="nav-link">Find Jobs</RouterLink>
        <RouterLink to="/about" class="nav-link">About</RouterLink>
        <RouterLink to="/contact" class="nav-link">Contact</RouterLink>
      </nav>

      <!-- Desktop Actions -->
      <div class="hidden shrink-0 items-center gap-3 lg:flex">
        <input v-model="searchQuery" type="text" placeholder="Search jobs..."
          class="h-10 w-40 rounded-full border border-slate-200 px-4 text-sm outline-none transition focus:border-blue-500 xl:w-52"
          @keyup.enter="searchJobs" />

        <button type="button"
          class="flex h-10 w-10 items-center justify-center text-blue-600 transition hover:text-blue-800"
          aria-label="Search jobs" @click="searchJobs">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            class="h-6 w-6">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>
        </button>

        <!-- Guest: Login / Register -->
        <template v-if="!authStore.isAuthenticated">
          <RouterLink to="/login" class="auth-button login-button">
            Login
          </RouterLink>

          <RouterLink to="/register" class="auth-button register-button">
            Register
          </RouterLink>
        </template>

        <!-- Authenticated: Profile Avatar -->
        <RouterLink v-else :to="profilePath"
          class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-base font-bold text-white no-underline shadow-sm transition hover:bg-blue-700"
          :title="authStore.user?.name || 'My Profile'" aria-label="My Profile">
          {{ userInitial }}
        </RouterLink>
      </div>

      <!-- Mobile Menu Button -->
      <button type="button"
        class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 lg:hidden"
        aria-label="Toggle navigation" :aria-expanded="isMenuOpen" @click="isMenuOpen = !isMenuOpen">
        <svg v-if="!isMenuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" class="h-6 w-6">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" class="h-6 w-6">
          <path d="m18 6-12 12M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Mobile Navigation -->
    <div v-if="isMenuOpen" class="border-b border-blue-100 bg-white px-5 py-5 lg:hidden">
      <nav class="flex flex-col gap-4">
        <RouterLink to="/" class="nav-link" @click="closeMenu">
          Home
        </RouterLink>

        <RouterLink to="/jobs" class="nav-link" @click="closeMenu">
          Find Jobs
        </RouterLink>

        <RouterLink to="/about" class="nav-link" @click="closeMenu">
          About
        </RouterLink>

        <RouterLink to="/contact" class="nav-link" @click="closeMenu">
          Contact
        </RouterLink>
      </nav>

      <!-- Mobile Search -->
      <div class="mt-5 flex items-center gap-2">
        <input v-model="searchQuery" type="text" placeholder="Search jobs..."
          class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 px-4 text-sm outline-none focus:border-blue-500"
          @keyup.enter="searchJobs" />

        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white"
          aria-label="Search jobs" @click="searchJobs">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            class="h-5 w-5">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>
        </button>
      </div>

      <!-- Mobile Guest Actions -->
      <div v-if="!authStore.isAuthenticated" class="mt-5 flex gap-3">
        <RouterLink to="/login" class="auth-button login-button flex-1" @click="closeMenu">
          Login
        </RouterLink>

        <RouterLink to="/register" class="auth-button register-button flex-1" @click="closeMenu">
          Register
        </RouterLink>
      </div>

      <!-- Mobile Authenticated Profile -->
      <RouterLink v-else :to="profilePath" class="mt-5 flex items-center gap-3 rounded-xl bg-blue-50 p-3 no-underline"
        @click="closeMenu">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 font-bold text-white">
          {{ userInitial }}
        </span>

        <span class="min-w-0">
          <span class="block truncate text-sm font-semibold text-slate-800">
            {{ authStore.user?.name }}
          </span>
          <span class="block text-xs text-blue-600">My Profile</span>
        </span>
      </RouterLink>
    </div>
  </header>
</template>

<style scoped>
.site-header :deep(a),
.site-header :deep(a:hover),
.site-header :deep(a:focus),
.site-header :deep(a:active),
.site-header :deep(a:visited) {
  text-decoration: none !important;
}

.site-header :deep(.nav-link) {
  display: inline-block;
  padding: 4px 0;
  color: #334155;
  font-size: 15px;
  font-weight: 500;
  white-space: nowrap;
  transition: color 0.2s ease;
}

.site-header :deep(.nav-link:hover) {
  color: #2563eb;
}

.auth-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 88px;
  height: 40px;
  padding: 0 14px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  transition: background-color 0.2s ease;
}

.login-button {
  border: 1px solid #bfdbfe;
  color: #1d4ed8;
  background: white;
}

.login-button:hover {
  background: #eff6ff;
}

.register-button {
  border: 1px solid #2563eb;
  color: white;
  background: #2563eb;
}

.register-button:hover {
  background: #1d4ed8;
}
</style>
