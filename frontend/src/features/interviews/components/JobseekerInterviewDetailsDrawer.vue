<script setup lang="ts">
import { computed } from "vue";
import { toast } from "vue3-toastify";
import {
    Link2,
    Calendar,
    Clock2,
    MapPin,
    RefreshCw,
    AlertCircle,
} from "lucide-vue-next";

import type { Interview } from "../types/interview.types";

const props = defineProps<{
    interview: Interview | null;
}>();

const emit = defineEmits<{
    (event: "close"): void;
    (event: "attend", id: number): void;
    (event: "request-reschedule", id: number): void;
    (event: "refresh"): void;
}>();

const companyName = computed(
    () => props.interview?.application?.job?.company?.name ?? "Company",
);
const companyLogo = computed(
    () => props.interview?.application?.job?.company?.logo ?? null,
);
const jobTitle = computed(
    () => props.interview?.application?.job?.title ?? "Job title unavailable",
);
const jobSeekerName = computed(
    () => props.interview?.application?.job_seeker?.name ?? "You",
);

function formatDate(value: string) {
    return new Date(value).toLocaleString([], {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
    });
}

function handleJoinMeeting() {
    if (props.interview?.meeting_link) {
        window.open(
            props.interview.meeting_link,
            "_blank",
            "noopener,noreferrer",
        );
    }
}

function handleAttend() {
    if (!props.interview) return;
    emit("attend", props.interview.id);
}

function handleRequestReschedule() {
    if (!props.interview) return;
    emit("request-reschedule", props.interview.id);
}

const prepTips = computed(() => [
    "Research the company and the role before the interview.",
    "Prepare answers to common interview questions.",
    "Test your internet, camera, and microphone in advance.",
    "Choose a quiet, well-lit space for the interview.",
    "Have a copy of your resume and the job description nearby.",
]);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="interview"
            class="fixed inset-0 z-40 bg-gray-900/30"
            @click.self="$emit('close')"
        >
            <Transition name="slide">
                <aside
                    class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-white shadow-2xl"
                >
                    <div
                        class="flex items-start justify-between border-b border-gray-100 p-6"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-indigo-600"
                            >
                                Interview details
                            </p>
                            <h2
                                class="mt-1 text-xl font-semibold text-gray-900"
                            >
                                {{ jobTitle }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ companyName }}
                            </p>
                        </div>
                        <button
                            type="button"
                            aria-label="Close details"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100"
                            @click="$emit('close')"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="flex-1 space-y-6 overflow-y-auto p-6">
                        <!-- Status -->
                        <span
                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                            :class="{
                                'bg-indigo-50 text-indigo-700':
                                    interview.status === 'scheduled',
                                'bg-green-50 text-green-700':
                                    interview.status === 'completed',
                                'bg-red-50 text-red-700':
                                    interview.status === 'cancelled',
                                'bg-amber-50 text-amber-700':
                                    interview.status === 'rescheduled',
                            }"
                        >
                            {{ interview.status }}
                        </span>

                        <!-- Details -->
                        <div class="space-y-4">
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Date and time
                                </p>
                                <p
                                    class="mt-1 flex items-center gap-2 text-sm font-medium text-gray-800"
                                >
                                    <Calendar
                                        :size="16"
                                        class="text-slate-500"
                                    />
                                    {{ formatDate(interview.scheduled_at) }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Interview type
                                </p>
                                <p
                                    class="mt-1 flex items-center gap-2 text-sm font-medium capitalize text-gray-800"
                                >
                                    <Clock2 :size="16" class="text-slate-500" />
                                    {{ interview.interview_type }}
                                </p>
                            </div>
                            <div v-if="interview.location">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Location
                                </p>
                                <p
                                    class="mt-1 flex items-center gap-2 text-sm text-gray-800"
                                >
                                    <MapPin :size="16" class="text-slate-500" />
                                    {{ interview.location }}
                                </p>
                            </div>
                            <div v-if="interview.meeting_link">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Meeting link
                                </p>
                                <button
                                    type="button"
                                    class="mt-1 flex items-center gap-2 w-fit rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-100 transition"
                                    @click="handleJoinMeeting"
                                >
                                    <Link2 :size="16" />
                                    Join online meeting
                                </button>
                            </div>
                        </div>

                        <!-- Company -->
                        <div class="border-t border-gray-100 pt-5 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Company
                            </h3>
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
                                    <span
                                        v-else
                                        class="text-sm font-medium text-slate-500"
                                    >
                                        {{
                                            companyName.charAt(0).toUpperCase()
                                        }}
                                    </span>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
                                        {{ companyName }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ jobSeekerName }}'s interview
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Interviewers -->
                        <div class="border-t border-gray-100 pt-5 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Interviewers
                            </h3>
                            <div class="mt-3 space-y-3">
                                <div
                                    v-for="person in interview.interviewers"
                                    :key="person.id"
                                    class="flex items-center gap-3"
                                >
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700"
                                    >
                                        {{
                                            person.name.charAt(0).toUpperCase()
                                        }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-800"
                                        >
                                            {{ person.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ person.position }}
                                        </p>
                                    </div>
                                </div>
                                <p
                                    v-if="!interview.interviewers.length"
                                    class="text-sm text-gray-500"
                                >
                                    No interviewers assigned.
                                </p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div
                            v-if="interview.notes"
                            class="border-t border-gray-100 pt-5 space-y-2"
                        >
                            <h3 class="text-sm font-semibold text-gray-900">
                                Notes
                            </h3>
                            <p
                                class="mt-1 whitespace-pre-wrap text-sm leading-6 text-gray-600"
                            >
                                {{ interview.notes }}
                            </p>
                        </div>

                        <!-- Interview prep tips -->
                        <div class="border-t border-gray-100 pt-5 space-y-2">
                            <div class="flex items-center gap-2">
                                <AlertCircle
                                    :size="18"
                                    class="text-amber-600"
                                />
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Interview prep tips
                                </h3>
                            </div>
                            <ul
                                class="mt-2 space-y-1 text-sm text-gray-600 list-disc list-inside"
                            >
                                <li
                                    v-for="(tip, index) in prepTips"
                                    :key="index"
                                >
                                    {{ tip }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-gray-100 p-5">
                        <!-- Jobseeker actions for scheduled/rescheduled interviews -->
                        <template
                            v-if="
                                ['scheduled', 'rescheduled'].includes(
                                    interview.status,
                                )
                            "
                        >
                            <!-- <div class="space-y-2 pt-3 border-t border-gray-100">
                                <button
                                    type="button"
                                    class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition"
                                    @click="handleRequestReschedule()"
                                >
                                    <RefreshCw :size="16" />
                                    Request reschedule
                                </button>
                            </div> -->
                            <button
                                v-if="interview.meeting_link"
                                type="button"
                                class="w-full flex items-center justify-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-600 hover:bg-indigo-100 transition"
                                @click="handleJoinMeeting"
                            >
                                <Link2 :size="16" />
                                Join online meeting
                            </button>
                            <button
                                type="button"
                                class="w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition"
                                @click="handleAttend"
                            >
                                Mark as attended
                            </button>
                            <button
                                type="button"
                                class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition"
                                @click="handleRequestReschedule()"
                            >
                                <RefreshCw :size="16" />
                                Request reschedule
                            </button>
                        </template>
                        <template v-else-if="interview.status === 'completed'">
                            <button
                                type="button"
                                class="w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition disabled:opacity-50 disabled:pointer-events-none"
                                disabled
                            >
                                ✔ Attended
                            </button>
                        </template>
                        <template v-else-if="interview.status === 'cancelled'">
                            <button
                                type="button"
                                class="w-full rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition disabled:opacity-50 disabled:pointer-events-none"
                                disabled
                            >
                                <AlertCircle :size="16" />
                                Cancelled
                            </button>
                        </template>
                        <button
                            type="button"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
                            @click="$emit('close')"
                        >
                            Close
                        </button>
                    </div>
                </aside>
            </Transition>
        </div>
    </Teleport>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}
</style>
