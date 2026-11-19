<!-- src/components/layout/PageHeader.vue -->
<script setup lang="ts">
import { Search } from "lucide-vue-next";

defineProps<{
  title: string;
  subtitle?: string;
  searchPlaceholder?: string;
  searchQuery?: string;
}>();

defineEmits<{
  (e: "update:searchQuery", value: string): void;
}>();
</script>

<template>
  <div
    class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between lg:mb-10"
  >
    <div class="text-center lg:text-left">
      <h1
        class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl"
      >
        {{ title }}
      </h1>
      <p v-if="subtitle" class="mt-1 text-sm text-gray-500 sm:text-base">
        {{ subtitle }}
      </p>
    </div>

    <!-- Search Bar -->
    <div class="relative w-full max-w-md mx-auto lg:mx-0">
      <Search
        class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
      />
      <input
        :value="searchQuery"
        @input="
          $emit('update:searchQuery', ($event.target as HTMLInputElement).value)
        "
        type="text"
        :placeholder="searchPlaceholder || 'Search...'"
        class="w-full rounded-xl border border-gray-300 py-3 pl-11 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-shadow"
      />
    </div>
  </div>
</template>
