<script setup lang="ts">
import type { Interview } from "../types/interview.types";

defineProps<{
    interview: Interview;
}>();

defineEmits<{
    (event: "select", interview: Interview): void;
}>();

function timeLabel(value: string) {
    return new Date(value).toLocaleTimeString([], {
        hour: "numeric",
        minute: "2-digit",
    });
}
</script>

<template>
    <button
        type="button"
        class="mb-1 w-full overflow-hidden rounded-md border border-indigo-100 bg-indigo-50/90 p-1.5 text-left transition hover:border-indigo-300 hover:bg-indigo-50"
        @click="$emit('select', interview)"
    >
        <div class="flex items-start justify-between gap-2">
            <span class="text-[9px] font-semibold text-indigo-700">
                {{ timeLabel(interview.scheduled_at) }}
            </span>
            <span
                class="rounded-full px-1.5 py-0.5 text-[8px] font-medium capitalize"
                :class="{
                    'bg-green-100 text-green-700':
                        interview.status === 'completed',
                    'bg-red-100 text-red-700': interview.status === 'cancelled',
                    'bg-indigo-100 text-indigo-700':
                        interview.status === 'scheduled',
                    'bg-amber-100 text-amber-700':
                        interview.status === 'rescheduled',
                }"
            >
                {{ interview.status }}
            </span>
        </div>

        <p class="mt-1 truncate text-[10px] font-semibold text-gray-900">
            {{ interview.application?.applicant_name ?? "Applicant" }}
        </p>

        <p class="mt-0.5 truncate text-[9px] text-gray-600">
            {{ interview.application?.job?.title ?? "Job title unavailable" }}
        </p>

        <div class="mt-1 flex flex-wrap gap-1">
            <span
                v-for="person in interview.interviewers"
                :key="person.id"
                class="max-w-full truncate rounded-full bg-white px-1.5 py-0.5 text-[8px] text-gray-600"
            >
                {{ person.name }}
            </span>
        </div>
    </button>
</template>
