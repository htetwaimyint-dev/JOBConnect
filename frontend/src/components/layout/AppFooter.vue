<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
  <footer class="w-full bg-[#102D59] text-white">
    <div
      class="mx-auto flex w-full max-w-[1600px] flex-col justify-between gap-6 px-6 py-5 sm:px-8 lg:flex-row lg:items-center lg:gap-5 lg:px-12"
    >
      <!-- Brand -->
      <div class="flex shrink-0 items-center gap-3">
        <span
          class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-6 w-6"
          >
            <rect x="3" y="7" width="18" height="14" rx="2" />
            <path
              d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2"
            />
          </svg>
        </span>

        <div class="flex shrink-0 flex-col gap-1">
          <h2 class="m-0 text-base font-bold text-white">JobConnect AI</h2>

          <p class="m-0 text-[10px] text-blue-100">
            Connecting talented people with the right opportunities.
          </p>
        </div>
      </div>

      <!-- Divider -->
      <div class="hidden h-10 border-l border-white/30 lg:block"></div>

      <!-- Contact Us -->
      <div class="flex flex-col gap-2">
        <h3 class="m-0 text-[11px] font-semibold text-white">Contact Us</h3>

        <div
          class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-blue-100"
        >
          <a
            href="mailto:hello@jobconnect.ai"
            class="inline-flex items-center gap-1 text-blue-100 no-underline transition hover:text-white"
          >
            ✉ hello@jobconnect.ai
          </a>

          <a
            href="tel:+959123456789"
            class="inline-flex items-center gap-1 text-blue-100 no-underline transition hover:text-white"
          >
            ☎ +95 9 123 456 789
          </a>

          <span class="flex items-center gap-1"> 📍 Yangon, Myanmar </span>
        </div>
      </div>

      <!-- Divider -->
      <div class="hidden h-10 border-l border-white/30 lg:block"></div>

      <!-- Follow Us -->
      <div class="flex shrink-0 flex-col gap-2">
        <h3 class="m-0 text-[11px] font-semibold text-white">Follow Us</h3>

        <div class="flex items-center gap-3">
          <a
            href="#"
            aria-label="Facebook"
            class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-bold text-[#102D59] no-underline transition hover:bg-blue-200"
          >
            f
          </a>

          <a
            href="#"
            aria-label="LinkedIn"
            class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-[10px] font-bold text-[#102D59] no-underline transition hover:bg-blue-200"
          >
            in
          </a>

          <a
            href="#"
            aria-label="GitHub"
            class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-bold text-[#102D59] no-underline transition hover:bg-blue-200"
          >
            ●
          </a>

          <a
            href="#"
            aria-label="YouTube"
            class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs text-[#102D59] no-underline transition hover:bg-blue-200"
          >
            ▶
          </a>
        </div>
      </div>
    </div>
    <!-- Copyright -->
    <div
      class="w-full border-t border-white/15 px-5 py-3 text-center text-sm font-medium text-blue-100 md:text-base"
    >
      © {{ currentYear }} JobConnect AI. All rights reserved.
    </div>
  </footer>
</template>
