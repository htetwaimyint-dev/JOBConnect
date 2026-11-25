<script setup lang="ts">
import { Search, X } from "lucide-vue-next";
const search = defineModel<string>("search", { required: true });
const status = defineModel<string>("status", { required: true });
</script>
<template>
  <div
    class="flex flex-col items-stretch justify-between gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:p-5"
  >
    <div class="relative w-full sm:max-w-md">
      <Search
        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
      /><input
        v-model="search"
        type="search"
        :placeholder="'Search by name or email and company'"
        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-10 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
      /><button
        v-if="search"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700"
        aria-label="Clear search"
        @click="search = ''"
      ></button>
    </div>
    <select
      v-model="status"
      class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-3 pr-8 text-sm outline-none focus:border-indigo-500 sm:w-auto"
    >
      <option>All Statuses</option>
      <option>Active</option>
      <option>Suspended</option>
    </select>
  </div>
</template>
