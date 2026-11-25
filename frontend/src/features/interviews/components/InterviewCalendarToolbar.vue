<script setup lang="ts">
defineProps<{
    currentDate: Date;
}>();

const emit = defineEmits<{
    (event: "previous"): void;
    (event: "next"): void;
    (event: "today"): void;
    (event: "refresh"): void;
}>();

function getWeekLabel(date: Date) {
    const start = new Date(date);
    const day = start.getDay();
    start.setDate(start.getDate() - day);

    const end = new Date(start);
    end.setDate(start.getDate() + 6);

    const options: Intl.DateTimeFormatOptions = {
        month: "short",
        day: "numeric",
    };

    return `${start.toLocaleDateString(undefined, options)} – ${end.toLocaleDateString(
        undefined,
        {
            ...options,
            year: "numeric",
        },
    )}`;
}
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="mt-1 text-sm text-gray-500">
                {{ getWeekLabel(currentDate) }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50"
                @click="emit('today')"
            >
                Today
            </button>

            <button
                type="button"
                aria-label="Refresh calendar"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50"
                @click="emit('refresh')"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.582m0 0a8.003 8.003 0 01-15.356-2m15.356 2H15"
                    />
                </svg>
            </button>

            <div class="flex overflow-hidden rounded-lg border border-gray-200">
                <button
                    type="button"
                    aria-label="Previous week"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-50"
                    @click="emit('previous')"
                >
                    ←
                </button>
                <div class="w-px bg-gray-200" />
                <button
                    type="button"
                    aria-label="Next week"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-50"
                    @click="emit('next')"
                >
                    →
                </button>
            </div>
        </div>
    </div>
</template>
