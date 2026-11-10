<!-- <template>
  <main class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <section class="w-full max-w-md bg-white rounded-xl shadow p-6">
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600">JobConnect</h1>
        <p class="text-gray-500">Find your next opportunity</p>
      </div>

      <RouterView />
    </section>
  </main>
</template> -->

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div
      class="flex w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200"
    >
      <!-- LEFT: Branding panel (hidden on mobile) -->
      <aside
        class="relative hidden w-1/2 flex-col justify-between bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 p-10 lg:flex"
      >
        <!-- Logo -->
        <div class="flex items-center gap-2">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white"
          >
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M9 4h6a2 2 0 0 1 2 2v2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h3V6a2 2 0 0 1 2-2zm0 4h6V6H9v2z"
              />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-900">JobConnect</h1>
            <p class="text-xs text-slate-600">Find Your Dream Job</p>
          </div>
        </div>

        <!-- Headline -->
        <div class="space-y-6">
          <h2 class="text-4xl font-bold leading-tight text-slate-900">
            Your Career<br />
            <span class="text-blue-600">Starts Here</span>
          </h2>
          <p class="text-sm text-slate-700">
            Discover thousands of job opportunities and take the next step in
            your career.
          </p>

          <!-- Checklist -->
          <ul class="space-y-3 text-sm text-slate-700">
            <li class="flex items-center gap-2">
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white"
              >
                <svg
                  class="h-3 w-3"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="3"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </span>
              Find the right job
            </li>
            <li class="flex items-center gap-2">
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white"
              >
                <svg
                  class="h-3 w-3"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="3"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </span>
              Connect with top companies
            </li>
            <li class="flex items-center gap-2">
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white"
              >
                <svg
                  class="h-3 w-3"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="3"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </span>
              Grow your career
            </li>
          </ul>
        </div>

        <!-- Illustration placeholder -->
        <div class="relative h-40">
          <div class="absolute inset-0 rounded-2xl bg-blue-200/50"></div>
          <div class="relative flex h-full items-end justify-center pb-4">
            <div class="h-24 w-32 rounded-t-xl bg-white shadow-md"></div>
            <div class="ml-2 h-16 w-24 rounded-t-xl bg-white shadow-md"></div>
          </div>
        </div>
      </aside>

      <!-- RIGHT: Form panel -->
      <section class="flex w-full flex-col p-8 lg:w-1/2 lg:p-10">
        <!-- Language switch -->
        <div class="mb-6 flex justify-end">
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
          >
            <svg
              class="h-4 w-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle cx="12" cy="12" r="10" />
              <path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20" />
            </svg>
            English
            <svg
              class="h-3 w-3"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" d="m6 9 6 6 6-6" />
            </svg>
          </button>
        </div>

        <!-- Tabs (Login / Register) -->
        <nav class="mb-8 grid grid-cols-2 border-b border-slate-200">
          <RouterLink
            to="/login"
            class="pb-3 text-center text-sm font-medium transition-colors"
            :class="
              $route.path === '/login'
                ? 'border-b-2 border-blue-600 text-blue-600'
                : 'text-slate-500 hover:text-slate-700'
            "
          >
            Login
          </RouterLink>
          <RouterLink
            to="/register"
            class="pb-3 text-center text-sm font-medium transition-colors"
            :class="
              $route.path === '/register'
                ? 'border-b-2 border-blue-600 text-blue-600'
                : 'text-slate-500 hover:text-slate-700'
            "
          >
            Register
          </RouterLink>
        </nav>

        <!-- Page content slot -->
        <div class="flex-1">
          <slot />
        </div>
      </section>
    </div>
  </div>
</template>
