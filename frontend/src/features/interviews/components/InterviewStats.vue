<script setup lang="ts">
import { computed } from "vue";
import type { Interview } from "../types/interview.types";

const props = defineProps<{
    interviews: Interview[];
}>();

const stats = computed(() => {
    const today = new Date().toDateString();

    return [
        {
            label: "Total Interviews",
            value: props.interviews.length,
            icon: "📅",
            description: "All scheduled interviews",
        },
        {
            label: "Today",
            value: props.interviews.filter(
                (item) =>
                    new Date(item.scheduled_at).toDateString() === today &&
                    item.status === "scheduled",
            ).length,
            icon: "☀️",
            description: "Scheduled for today",
        },
        {
            label: "Completed",
            value: props.interviews.filter(
                (item) => item.status === "completed",
            ).length,
            icon: "✓",
            description: "Finished interviews",
        },
        {
            label: "Cancelled",
            value: props.interviews.filter(
                (item) => item.status === "cancelled",
            ).length,
            icon: "↗",
            description: "Cancelled interviews",
        },
    ];
});
</script>

<template>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
        <div
            v-for="stat in stats"
            :key="stat.label"
            class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm"
        >
            <div class="flex items-center justify-between">
                <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500">
                    {{ stat.label }}
                </p>
                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-sm text-indigo-600"
                >
                    {{ stat.icon }}
                </span>
            </div>

            <p class="mt-0.5 text-lg font-bold text-slate-900">
                {{ stat.value }}
            </p>

            <p class="hidden text-[9px] text-slate-400 sm:block">
                {{ stat.description }}
            </p>
        </div>
    </div>
</template>
