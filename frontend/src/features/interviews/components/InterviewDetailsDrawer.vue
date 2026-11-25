<script setup lang="ts">
import type { Interview } from "../types/interview.types";

defineProps<{
    interview: Interview | null;
}>();

defineEmits<{
    (event: "close"): void;
    (event: "status", id: number, status: Interview["status"]): void;
}>();

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
                                {{
                                    interview.application?.applicant_name ??
                                    "Applicant"
                                }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                {{
                                    interview.application?.job?.title ??
                                    "Job title unavailable"
                                }}
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

                        <div class="space-y-4">
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Date and time
                                </p>
                                <p
                                    class="mt-1 text-sm font-medium text-gray-800"
                                >
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
                                    class="mt-1 text-sm font-medium capitalize text-gray-800"
                                >
                                    {{ interview.interview_type }}
                                </p>
                            </div>
                            <div v-if="interview.location">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Location
                                </p>
                                <p class="mt-1 text-sm text-gray-800">
                                    {{ interview.location }}
                                </p>
                            </div>
                            <div v-if="interview.meeting_link">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                                >
                                    Meeting link
                                </p>
                                <a
                                    :href="interview.meeting_link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-1 block break-all text-sm font-medium text-indigo-600 hover:underline"
                                >
                                    {{ interview.meeting_link }}
                                </a>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
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

                        <div
                            v-if="interview.notes"
                            class="border-t border-gray-100 pt-5"
                        >
                            <h3 class="text-sm font-semibold text-gray-900">
                                Notes
                            </h3>
                            <p
                                class="mt-2 whitespace-pre-wrap text-sm leading-6 text-gray-600"
                            >
                                {{ interview.notes }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-gray-100 p-5">
                        <template
                            v-if="
                                ['scheduled', 'rescheduled'].includes(
                                    interview.status,
                                )
                            "
                        >
                            <button
                                type="button"
                                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                                @click="
                                    $emit('status', interview.id, 'completed')
                                "
                            >
                                Mark as completed
                            </button>
                            <button
                                type="button"
                                class="w-full rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50"
                                @click="
                                    $emit('status', interview.id, 'cancelled')
                                "
                            >
                                Cancel interview
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
