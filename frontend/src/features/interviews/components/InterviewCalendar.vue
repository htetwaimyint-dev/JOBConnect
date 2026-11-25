<script setup lang="ts">
import { computed } from "vue";
import type { Interview } from "../types/interview.types";
import InterviewCard from "./InterviewCard.vue";

const props = defineProps<{
    interviews: Interview[];
    currentDate: Date;
    loading: boolean;
}>();
const emit = defineEmits<{
    select: [interview: Interview];
    addSchedule: [slot: { date: Date; hour: number }];
}>();

const weekDays = computed(() => {
    const start = new Date(props.currentDate);
    start.setHours(0, 0, 0, 0);
    start.setDate(start.getDate() - start.getDay());
    return Array.from({ length: 7 }, (_, index) => {
        const date = new Date(start);
        date.setDate(start.getDate() + index);
        return date;
    });
});
const hours = Array.from({ length: 11 }, (_, i) => i + 8);
function dateKey(date: Date) {
    return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
}
function interviewsForHour(date: Date, hour: number) {
    return props.interviews.filter((interview) => {
        const value = interview.scheduled_at.includes("T")
            ? interview.scheduled_at
            : interview.scheduled_at.replace(" ", "T");
        const scheduled = new Date(value);
        return (
            !Number.isNaN(scheduled.getTime()) &&
            dateKey(scheduled) === dateKey(date) &&
            scheduled.getHours() === hour
        );
    });
}
</script>

<template>
    <div v-if="loading" class="p-10 text-center text-sm text-slate-500">
        Loading…
    </div>
    <div v-else class="flex h-full min-h-0 flex-col overflow-hidden">
        <div
            class="calendar-scrollbar shrink-0 overflow-x-auto border-b border-slate-200 bg-slate-50/80"
        >
            <div
                class="grid min-w-225 grid-cols-[54px_repeat(7,minmax(110px,1fr))]"
            >
                <div class="border-r border-slate-100" />
                <div
                    v-for="day in weekDays"
                    :key="dateKey(day)"
                    class="border-r border-slate-100 px-2 py-2 text-center last:border-r-0"
                >
                    <p
                        class="text-[9px] font-semibold uppercase tracking-wider text-slate-400"
                    >
                        {{
                            day.toLocaleDateString(undefined, {
                                weekday: "short",
                            })
                        }}
                    </p>
                    <p
                        class="mx-auto mt-1 flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold"
                        :class="
                            dateKey(day) === dateKey(new Date())
                                ? 'bg-indigo-600 text-white'
                                : 'text-slate-700'
                        "
                    >
                        {{ day.getDate() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- This is the only vertically scrollable region of the page. -->
        <div
            class="calendar-scrollbar min-h-0 flex-1 overflow-auto"
            data-calendar-scroll
        >
            <div
                class="grid min-w-225 grid-cols-[54px_repeat(7,minmax(110px,1fr))]"
            >
                <template v-for="hour in hours" :key="hour">
                    <div
                        class="h-17 border-b border-r border-slate-100 bg-slate-50/50 pr-2 pt-1.5 text-right text-[9px] font-medium text-slate-400"
                    >
                        {{
                            new Date(2000, 0, 1, hour).toLocaleTimeString([], {
                                hour: "numeric",
                            })
                        }}
                    </div>
                    <div
                        v-for="day in weekDays"
                        :key="`${dateKey(day)}-${hour}`"
                        class="group relative min-h-17 border-b border-r border-slate-100 p-1 transition-colors hover:bg-indigo-50/30 last:border-r-0"
                    >
                        <template v-if="interviewsForHour(day, hour).length">
                            <InterviewCard
                                v-for="interview in interviewsForHour(
                                    day,
                                    hour,
                                )"
                                :key="interview.id"
                                :interview="interview"
                                @select="emit('select', $event)"
                            />
                        </template>

                        <div
                            v-else
                            class="flex h-full min-h-14.5 w-full cursor-pointer items-center justify-center rounded-lg opacity-0 hover:bg-indigo-50/40 transition-colors duration-150 group-hover:opacity-80 focus:opacity-100"
                            @click="emit('addSchedule', { date: day, hour })"
                        >
                            <span
                                class="rounded-md border border-indigo-200 bg-white/95 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 shadow-2xs transition hover:border-indigo-300 hover:bg-indigo-50"
                            >
                                + Schedule
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
.calendar-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.calendar-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.calendar-scrollbar::-webkit-scrollbar-track {
    background: transparent;
    margin-block: 8px;
}

.calendar-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 9999px;
}

.calendar-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}
</style>
