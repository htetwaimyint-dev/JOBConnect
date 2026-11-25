<script setup lang="ts">
import { Search, X } from "lucide-vue-next";

withDefaults(
  defineProps<{
    loading?: boolean;
  }>(),
  {
    loading: false,
  },
);

const emit = defineEmits<{
  search: [];
  clear: [];
}>();
</script>

<template>
  <div class="flex items-center gap-2">
    <button
      type="button"
      :disabled="loading"
      @click="emit('search')"
      class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#2563EB] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
    >
      <Search class="h-4 w-4" />
      Search
    </button>

    <button
      type="button"
      :disabled="loading"
      @click="emit('clear')"
      class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
    >
      <X class="h-4 w-4" />
      Clear
    </button>
  </div>
</template>
