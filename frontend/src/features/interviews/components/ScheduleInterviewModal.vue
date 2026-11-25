<script setup lang="ts">
import { computed, ref, watch } from "vue";
import type {
    InterviewApplication,
    InterviewFormData,
    Interviewer,
    InterviewType,
} from "../types/interview.types";
import InterviewerMultiSelect from "./InterviewerMultiSelect.vue";

const props = defineProps<{
    open: boolean;
    applications: InterviewApplication[];
    interviewers: Interviewer[];
    saving?: boolean;
    selectedSlot?: {
        date: Date;
        hour: number;
    } | null;
}>();

const emit = defineEmits<{
    (event: "close"): void;
    (event: "submit", data: InterviewFormData): void;
    (event: "manage-interviewers"): void;
}>();

const applicationId = ref("");
const interviewType = ref<InterviewType>("online");
const scheduledAt = ref("");
const location = ref("");
const meetingLink = ref("");
const notes = ref("");
const interviewerIds = ref<number[]>([]);

const requiresLocation = computed(() => interviewType.value === "onsite");

const requiresLink = computed(() => interviewType.value === "online");

function resetForm() {
    applicationId.value = "";
    interviewType.value = "online";
    location.value = "";
    meetingLink.value = "";
    notes.value = "";
    interviewerIds.value = [];
}

watch(
    () => [props.open, props.selectedSlot] as const,
    ([isOpen, slot]) => {
        if (!isOpen) return;

        resetForm();

        if (slot) {
            scheduledAt.value = formatDateTimeLocal(slot.date, slot.hour);
        } else {
            scheduledAt.value = "";
        }
    },
    { immediate: true },
);

function submit() {
    if (!applicationId.value || !scheduledAt.value) return;

    emit("submit", {
        application_id: Number(applicationId.value),
        interview_type: interviewType.value,
        scheduled_at: new Date(scheduledAt.value).toISOString(),
        location: requiresLocation.value ? location.value.trim() || null : null,
        meeting_link: requiresLink.value
            ? meetingLink.value.trim() || null
            : null,
        notes: notes.value.trim() || null,
        interviewer_ids: interviewerIds.value,
    });
}

function formatDateTimeLocal(date: Date, hour?: number): string {
    const selectedDate = new Date(date);

    if (hour !== undefined) {
        selectedDate.setHours(hour, 0, 0, 0);
    }

    const year = selectedDate.getFullYear();
    const month = String(selectedDate.getMonth() + 1).padStart(2, "0");
    const day = String(selectedDate.getDate()).padStart(2, "0");
    const hours = String(selectedDate.getHours()).padStart(2, "0");
    const minutes = String(selectedDate.getMinutes()).padStart(2, "0");

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

const shortlistedApplications = computed(() =>
    props.applications.filter(
        (application) => application.status === "shortlisted",
    ),
);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/40 p-4"
            @click.self="emit('close')"
        >
            <section
                class="my-auto w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Schedule Interview
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Create an interview for an applicant.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        ✕
                    </button>
                </div>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Applicant / Application
                        </label>
                        <select
                            v-model="applicationId"
                            required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        >
                            <option value="" disabled>
                                Select an application
                            </option>
                            <option
                                v-for="application in shortlistedApplications"
                                :key="application.id"
                                :value="String(application.id)"
                            >
                                {{ application.applicant_name }} —
                                {{ application.job.title }}
                            </option>
                        </select>
                        <p
                            v-if="!applications.length"
                            class="mt-1 text-xs text-amber-600"
                        >
                            No eligible applications available for scheduling.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Interview Type
                            </label>
                            <select
                                v-model="interviewType"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                            >
                                <option value="online">Online</option>
                                <option value="onsite">Onsite</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Date and Time
                            </label>
                            <input
                                v-model="scheduledAt"
                                type="datetime-local"
                                required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                            />
                        </div>
                    </div>

                    <div v-if="requiresLocation">
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Location
                        </label>
                        <input
                            v-model="location"
                            required
                            placeholder="Enter interview location"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div v-if="requiresLink">
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Meeting Link
                        </label>
                        <input
                            v-model="meetingLink"
                            type="url"
                            required
                            placeholder="https://..."
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        />
                    </div>

                    <InterviewerMultiSelect
                        v-model="interviewerIds"
                        :interviewers="interviewers"
                        @manage="emit('manage-interviewers')"
                    />

                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="notes"
                            rows="3"
                            placeholder="Add interview instructions or notes..."
                            class="w-full resize-y rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div
                        class="flex justify-end gap-2 border-t border-gray-100 pt-4"
                    >
                        <button
                            type="button"
                            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
                            @click="emit('close')"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="saving || !applications.length"
                            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{
                                saving ? "Scheduling..." : "Schedule Interview"
                            }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </Teleport>
</template>
