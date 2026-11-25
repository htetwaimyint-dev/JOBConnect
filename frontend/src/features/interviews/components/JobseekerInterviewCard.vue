<script setup lang="ts">
import { computed } from "vue";
import { Calendar, Clock2, MapPin, Link2 } from "lucide-vue-next";

import type { Interview } from "../types/interview.types";

const props = defineProps<{
    interview: Interview;
}>();

const emit = defineEmits<{
    (event: "select", interview: Interview): void;
}>();

const jobTitle = computed(
    () => props.interview.application?.job?.title ?? "Job title unavailable",
);
const companyName = computed(
    () => props.interview.application?.job?.company?.name ?? "Company",
);
const companyLogo = computed(
    () => props.interview.application?.job?.company?.logo ?? null,
);

const interview = computed(() => props.interview);

function timeLabel(value: string) {
    return new Date(value).toLocaleTimeString([], {
        hour: "numeric",
        minute: "2-digit",
    });
}

function dateLabel(value: string) {
    return new Date(value).toLocaleDateString(undefined, {
        weekday: "short",
        month: "short",
        day: "numeric",
    });
}
</script>

<template>
    <button
        type="button"
        class="mb-3 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md"
        @click="emit('select', interview)"
    >
        <div class="flex flex-col gap-3 p-4">
            <!-- Header row: time + status -->
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm font-semibold text-indigo-700">
                    <Calendar :size="15" />
                    <span>{{ dateLabel(interview.scheduled_at) }} · {{ timeLabel(interview.scheduled_at) }}</span>
                </div>
                <span
                    class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-medium capitalize"
                    :class="{
                        'bg-green-100 text-green-700': interview.status === 'completed',
                        'bg-red-100 text-red-700': interview.status === 'cancelled',
                        'bg-indigo-100 text-indigo-700': interview.status === 'scheduled',
                        'bg-amber-100 text-amber-700': interview.status === 'rescheduled',
                    }"
                >
                    {{ interview.status }}
                </span>
            </div>

            <!-- Company + Job title -->
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden"
                >
                    <img
                        v-if="companyLogo"
                        :src="companyLogo"
                        :alt="companyName"
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-sm font-medium text-slate-500">
                        {{ companyName.charAt(0).toUpperCase() }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-900 truncate">{{ companyName }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-500 truncate">{{ jobTitle }}</p>
                </div>
            </div>

            <!-- Interview details chips -->
            <div class="flex flex-wrap gap-2 text-[10px]">
                <span
                    class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-slate-600"
                >
                    <MapPin :size="12" />
                    {{ interview.interview_type }}
                </span>
                <span v-if="interview.location" class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-slate-600">
                    {{ interview.location }}
                </span>
                <span v-if="interview.meeting_link" class="inline-flex items-center gap-1 rounded-md border border-indigo-200 bg-indigo-50 px-2 py-1 text-indigo-700">
                    <Link2 :size="12" />
                    Online meeting
                </span>
            </div>

            <!-- Interviewers -->
            <div v-if="interview.interviewers.length" class="mt-2 space-y-1">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    Interviewers
                </p>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="person in interview.interviewers"
                        :key="person.id"
                        class="max-w-[140px] truncate rounded-full bg-slate-50 px-2 py-1 text-[9px] text-gray-600 border border-slate-200"
                    >
                        {{ person.name }}
                    </span>
                </div>
            </div>
        </div>
    </button>
</template>