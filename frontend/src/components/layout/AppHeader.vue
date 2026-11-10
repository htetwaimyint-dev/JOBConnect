<script setup lang="ts">
import { ref } from "vue";
import { RouterLink } from "vue-router";

const isMenuOpen = ref(false);
const searchQuery = ref("");

const closeMenu = () => {
  isMenuOpen.value = false;
};
</script>

<template>
  <header class="site-header sticky top-0 z-50 w-full bg-white">
    <div
      class="flex w-full items-center justify-between gap-5 border-b border-blue-100 px-5 py-3 sm:px-8 lg:px-10"
    >
      <!-- Logo -->
      <RouterLink
        to="/"
        class="flex shrink-0 items-center gap-3 no-underline"
        @click="closeMenu"
      >
        <span
          class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-7 w-7"
          >
            <rect x="3" y="7" width="18" height="14" rx="2" />
            <path
              d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2"
            />
          </svg>
        </span>

        <h2
          class="m-0 whitespace-nowrap text-xl font-bold text-blue-700 sm:text-2xl"
        >
          JobConnect <span class="text-slate-900">AI</span>
        </h2>
      </RouterLink>

      <!-- Desktop Navigation -->
      <nav
        class="hidden flex-1 items-center justify-center gap-8 lg:flex xl:gap-10"
      >
        <RouterLink to="/" class="nav-link"> Home </RouterLink>

        <RouterLink to="/jobs" class="nav-link"> Find Jobs </RouterLink>

        <RouterLink to="/about" class="nav-link"> About </RouterLink>

        <RouterLink to="/contact" class="nav-link"> Contact </RouterLink>
      </nav>

      <!-- Desktop Actions -->
      <div class="hidden shrink-0 items-center gap-3 lg:flex">
        <!-- Search Input -->
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search jobs..."
          class="h-10 w-40 rounded-full border border-slate-200 px-4 text-sm outline-none transition focus:border-blue-500 xl:w-52"
          @keyup.enter="
            $router.push({
              path: '/jobs',
              query: { search: searchQuery },
            })
          "
        />

        <!-- Search Button -->
        <RouterLink
          :to="{
            path: '/jobs',
            query: { search: searchQuery },
          }"
          class="flex h-10 w-10 items-center justify-center text-blue-600 no-underline hover:text-blue-800"
          aria-label="Search jobs"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            class="h-6 w-6"
          >
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>
        </RouterLink>

        <!-- Login -->
        <RouterLink to="/login" class="auth-button login-button">
          Login
        </RouterLink>

        <!-- Register -->
        <RouterLink to="/register" class="auth-button register-button">
          Register
        </RouterLink>
      </div>

      <!-- Mobile Menu Button -->
      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 lg:hidden"
        aria-label="Toggle navigation"
        :aria-expanded="isMenuOpen"
        @click="isMenuOpen = !isMenuOpen"
      >
        <svg
          v-if="!isMenuOpen"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          class="h-6 w-6"
        >
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          class="h-6 w-6"
        >
          <path d="m18 6-12 12M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Mobile Navigation -->
    <div
      v-if="isMenuOpen"
      class="border-b border-blue-100 bg-white px-5 py-5 lg:hidden"
    >
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
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search jobs..."
          class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 px-4 text-sm outline-none focus:border-blue-500"
          @keyup.enter="
            closeMenu();
            $router.push({
              path: '/jobs',
              query: { search: searchQuery },
            });
          "
        />

        <RouterLink
          :to="{
            path: '/jobs',
            query: { search: searchQuery },
          }"
          class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white no-underline"
          @click="closeMenu"
          aria-label="Search jobs"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            class="h-5 w-5"
          >
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-4-4" />
          </svg>
        </RouterLink>
      </div>

      <!-- Mobile Login / Register -->
      <div class="mt-5 flex gap-3">
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
    </div>
  </header>
</template>

<style scoped>
/* Remove underline from every header link */
.site-header :deep(a),
.site-header :deep(a:hover),
.site-header :deep(a:focus),
.site-header :deep(a:active),
.site-header :deep(a:visited) {
  text-decoration: none !important;
}

/* Navigation */
.site-header :deep(.nav-link) {
  display: inline-block;
  padding: 4px 0;
  color: #334155;
  font-size: 15px;
  font-weight: 500;
  white-space: nowrap;
  text-decoration: none !important;
  border-bottom: none !important;
  box-shadow: none !important;
  transition: color 0.2s ease;
}

.site-header :deep(.nav-link:hover) {
  color: #2563eb;
  text-decoration: none !important;
}

/* Login and Register */
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
  text-decoration: none !important;
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
