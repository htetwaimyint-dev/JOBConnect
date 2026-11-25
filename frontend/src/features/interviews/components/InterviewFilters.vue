<script setup lang="ts">
import type { InterviewFilters } from "../types/interview.types";

const props = defineProps<{
    filters: InterviewFilters;
}>();

const emit = defineEmits<{
    "update:filters": [filters: InterviewFilters];
    reset: [];
}>();

function update<K extends keyof InterviewFilters>(
    key: K,
    value: InterviewFilters[K],
) {
    emit("update:filters", { ...props.filters, [key]: value });
}

const statuses = [
    { value: "all", label: "All interviews" },
    { value: "scheduled", label: "Scheduled" },
    { value: "rescheduled", label: "Rescheduled" },
    { value: "completed", label: "Completed" },
    { value: "cancelled", label: "Cancelled" },
] as const;

const interviewTypes = [
    { value: "all", label: "All types" },
    { value: "online", label: "Online" },
    { value: "onsite", label: "On-site" },
    { value: "phone", label: "Phone" },
] as const;
</script>

<template>
    <aside
        class="flex h-full w-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
    >
        <!-- Fixed Header (Does not scroll) -->
        <div
            class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-100 p-4"
        >
            <div>
                <h2 class="text-sm font-bold text-slate-900">Filters</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Narrow down your interviews
                </p>
            </div>
            <button
                type="button"
                class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-700"
                @click="emit('reset')"
            >
                Clear all
            </button>
        </div>

        <!-- Scrollable Filter Controls -->
        <div
            class="filter-scrollbar min-h-0 flex-1 space-y-4 overflow-y-auto p-4"
        >
            <section>
                <label
                    for="interview-search"
                    class="mb-2 block text-xs font-semibold text-slate-700"
                    >Search</label
                >
                <div class="relative">
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <circle cx="10.8" cy="10.8" r="6.8" />
                        <path d="m16 16 4.5 4.5" />
                    </svg>
                    <input
                        id="interview-search"
                        :value="filters.search"
                        type="search"
                        placeholder="Applicant or job title"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                        @input="
                            update(
                                'search',
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                    />
                </div>
            </section>

            <section>
                <h3 class="mb-3 text-xs font-semibold text-slate-700">
                    Interview status
                </h3>
                <div class="space-y-2.5">
                    <label
                        v-for="status in statuses"
                        :key="status.value"
                        class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900"
                    >
                        <input
                            :checked="filters.status === status.value"
                            :value="status.value"
                            type="radio"
                            name="interview-status"
                            class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @change="update('status', status.value)"
                        />
                        <span>{{ status.label }}</span>
                    </label>
                </div>
            </section>

            <section>
                <label
                    for="interview-type"
                    class="mb-2 block text-xs font-semibold text-slate-700"
                    >Interview type</label
                >
                <select
                    id="interview-type"
                    :value="filters.interview_type"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                    @change="
                        update(
                            'interview_type',
                            ($event.target as HTMLSelectElement)
                                .value as InterviewFilters['interview_type'],
                        )
                    "
                >
                    <option
                        v-for="type in interviewTypes"
                        :key="type.value"
                        :value="type.value"
                    >
                        {{ type.label }}
                    </option>
                </select>
            </section>

            <section>
                <h3 class="mb-2 text-xs font-semibold text-slate-700">
                    Scheduled date
                </h3>
                <label
                    for="interview-from"
                    class="mb-1.5 block text-xs text-slate-500"
                    >From</label
                >
                <input
                    id="interview-from"
                    :value="filters.from"
                    type="date"
                    class="mb-3 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                    @input="
                        update(
                            'from',
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                />
                <label
                    for="interview-to"
                    class="mb-1.5 block text-xs text-slate-500"
                    >To</label
                >
                <input
                    id="interview-to"
                    :value="filters.to"
                    type="date"
                    :min="filters.from || undefined"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                    @input="
                        update('to', ($event.target as HTMLInputElement).value)
                    "
                />
            </section>
        </div>
    </aside>
</template>

<style scoped>
.filter-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.filter-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.filter-scrollbar::-webkit-scrollbar-track {
    background: transparent;
    margin-block: 8px;
}

.filter-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 9999px;
}

.filter-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}
</style>
