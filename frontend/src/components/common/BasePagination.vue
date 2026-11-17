<script setup lang="ts">
import { computed } from "vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";

interface Props {
  currentPage: number;
  lastPage: number;
  total: number;
  perPage?: number;
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  perPage: 5,
  loading: false,
});

const emit = defineEmits<{
  change: [page: number];
  "update:perPage": [value: number];
}>();

type PageItem = number | "...";

const pages = computed<PageItem[]>(() => {
  const last = props.lastPage;

  if (last <= 0) return [];

  const current = props.currentPage;
  const visible = new Set<number>([1, last]);

  // Show nearby pages
  for (
    let page = Math.max(2, current - 1);
    page <= Math.min(last - 1, current + 1);
    page++
  ) {
    visible.add(page);
  }

  // Show more pages near the beginning
  if (current <= 3) {
    visible.add(2);
    visible.add(3);
  }

  // Show more pages near the end
  if (current >= last - 2) {
    visible.add(last - 1);
    visible.add(last - 2);
  }

  const sorted = [...visible]
    .filter((page) => page >= 1 && page <= last)
    .sort((a, b) => a - b);

  const result: PageItem[] = [];

  sorted.forEach((page, index) => {
    const previousPage = sorted[index - 1];

    if (previousPage !== undefined && page - previousPage > 1) {
      result.push("...");
    }

    result.push(page);
  });

  return result;
});

const from = computed(() =>
  props.total === 0 ? 0 : (props.currentPage - 1) * props.perPage + 1,
);

const to = computed(() =>
  Math.min(props.currentPage * props.perPage, props.total),
);

const goToPage = (page: number) => {
  if (
    props.loading ||
    page < 1 ||
    page > props.lastPage ||
    page === props.currentPage
  ) {
    return;
  }

  emit("change", page);
};
</script>

<template>
  <div
    v-if="total > 0"
    class="flex flex-col gap-4 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
  >
    <!-- Record Information -->
    <p class="text-center text-sm text-slate-500 sm:text-left">
      Showing
      <span class="font-semibold text-slate-800">{{ from }}–{{ to }}</span>
      of
      <span class="font-semibold text-slate-800">{{ total }}</span>
      results
    </p>

    <!-- Pagination Controls -->
    <nav
      aria-label="Pagination"
      class="flex flex-wrap items-center justify-center gap-1.5"
    >
      <!-- Items Per Page -->
      <div class="flex items-center justify-end gap-2">
        <label for="per-page" class="text-sm text-slate-500">
          Rows per page
        </label>

        <select
          id="per-page"
          :value="perPage"
          :disabled="loading"
          @change="
            emit(
              'update:perPage',
              Number(($event.target as HTMLSelectElement).value),
            )
          "
          class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:opacity-50"
        >
          <option :value="2">2</option>
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="15">15</option>
          <option :value="20">20</option>
        </select>
      </div>

      <!-- Previous -->
      <button
        type="button"
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage <= 1 || loading"
        aria-label="Previous page"
        class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
      >
        <ChevronLeft :size="17" />
      </button>

      <!-- Page Numbers -->
      <template v-for="(page, index) in pages" :key="`${page}-${index}`">
        <span
          v-if="page === '...'"
          class="flex h-9 min-w-8 items-center justify-center text-sm text-slate-400"
        >
          ...
        </span>

        <button
          v-else
          type="button"
          @click="goToPage(page)"
          :disabled="loading"
          :aria-label="`Go to page ${page}`"
          :aria-current="currentPage === page ? 'page' : undefined"
          class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2.5 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-50"
          :class="
            currentPage === page
              ? 'bg-blue-600 text-white shadow-sm shadow-blue-200'
              : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
          "
        >
          {{ page }}
        </button>
      </template>

      <!-- Next -->
      <button
        type="button"
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage >= lastPage || loading"
        aria-label="Next page"
        class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
      >
        <ChevronRight :size="17" />
      </button>
    </nav>
  </div>
</template>
