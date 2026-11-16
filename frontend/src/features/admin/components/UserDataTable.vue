<script setup lang="ts">
import { ref, computed } from "vue";

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: "" },
  columns: { type: Array as () => any[], required: true },
  data: { type: Array as () => any[], default: () => [] },
  meta: { type: Object as () => Record<string, any> | null, default: null },
  loading: { type: Boolean, default: false },
});

defineEmits(["page-change"]);

const searchQuery = ref("");
const selectedRows = ref<number[]>([]);

const selectedCount = computed(() => selectedRows.value.length);

function clearSelection() {
  selectedRows.value = [];
}

// Simple client-side search filtering
const filteredData = computed(() => {
  if (!searchQuery.value) return props.data;
  const query = searchQuery.value.toLowerCase();
  return props.data.filter((row: any) =>
    Object.values(row).some(
      (val) => val && String(val).toLowerCase().includes(query),
    ),
  );
});
</script>

<template>
  <div class="mx-auto max-w-6xl p-6">
    <!-- Main Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
      <!-- Header -->
      <div class="border-b border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
      </div>

      <!-- Toolbar (Search & Action) -->
      <div
        class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="relative w-full max-w-md">
          <svg
            class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="`Search ${title.toLowerCase()}...`"
            class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          />
        </div>
        <div class="flex items-center gap-3">
          <button
            v-if="selectedCount > 0"
            class="rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100"
            @click="clearSelection"
          >
            Delete Selected ({{ selectedCount }})
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
          <thead
            class="border-y border-gray-200 bg-gray-50 text-xs uppercase text-gray-500"
          >
            <tr>
              <th class="px-6 py-4"></th>
              <th
                v-for="col in columns"
                :key="col.key"
                class="px-6 py-4 font-medium"
                :class="col.align"
              >
                {{ col.label }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="loading">
              <td
                :colspan="columns.length + 1"
                class="px-6 py-10 text-center text-gray-500"
              >
                Loading...
              </td>
            </tr>
            <tr v-else-if="filteredData.length === 0">
              <td
                :colspan="columns.length + 1"
                class="px-6 py-10 text-center text-gray-500"
              >
                No data found.
              </td>
            </tr>
            <tr
              v-for="(row, index) in filteredData"
              :key="row.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  v-model="selectedRows"
                  :value="row.id"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <!-- Dynamic slots per column -->
              <td
                v-for="col in columns"
                :key="col.key"
                class="px-6 py-4"
                :class="col.align"
              >
                <slot :name="`cell-${col.key}`" :row="row" :index="index">
                  {{ row[col.key] }}
                </slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer / Pagination -->
      <div
        class="flex flex-col items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row"
      >
        <div class="text-sm text-gray-500">
          Showing
          <span class="font-medium text-gray-900">{{ meta?.from ?? 0 }}</span> –
          <span class="font-medium text-gray-900">{{ meta?.to ?? 0 }}</span> of
          <span class="font-medium text-gray-900">{{ meta?.total ?? 0 }}</span>
          entries
        </div>
        <div class="mt-4 flex items-center gap-2 sm:mt-0">
          <select
            class="rounded-lg border border-gray-300 py-1.5 pl-3 pr-8 text-sm focus:border-blue-500 focus:outline-none"
          >
            <option>5</option>
            <option>10</option>
            <option>25</option>
            <option>50</option>
          </select>
          <span class="text-sm text-gray-500">entries</span>

          <div class="ml-4 flex items-center gap-1">
            <!-- Previous Button -->
            <button
              class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="!meta || meta.current_page === 1"
              @click="meta && $emit('page-change', meta.current_page - 1)"
            >
              Previous
            </button>

            <!-- Current Page Number -->
            <button
              class="rounded-lg border border-blue-600 bg-white px-3 py-1.5 text-sm font-medium text-blue-600"
            >
              {{ meta?.current_page ?? 1 }}
            </button>

            <!-- Next Button -->
            <button
              class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="!meta || meta.current_page === meta.last_page"
              @click="meta && $emit('page-change', meta.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
