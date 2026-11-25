<script setup lang="ts">
import { computed, ref } from "vue";
import { useRouter } from "vue-router";

import { useInterviews } from "../composables/useInterviews";
import type { InterviewFormData } from "../types/interview.types";
import ScheduleInterviewModal from "../components/ScheduleInterviewModal.vue";
import InterviewerManagementModal from "../components/InterviewerManagementModal.vue";

const router = useRouter();

const {
    getInterviewers,
    getAvailableApplications,
    createInterview,
    createInterviewer,
    updateInterviewer,
    deleteInterviewer,
} = useInterviews();

const interviewersQuery = getInterviewers();
const applicationsQuery = getAvailableApplications();

const interviewers = computed(() => interviewersQuery.data.value ?? []);
const applications = computed(() => applicationsQuery.data.value ?? []);

const showInterviewerModal = ref(false);
const errorMessage = ref("");

async function submitInterview(data: InterviewFormData) {
    errorMessage.value = "";

    try {
        await createInterview.mutateAsync(data);
        router.push({ name: "employer.interviews" });
    } catch (error) {
        errorMessage.value =
            error instanceof Error
                ? error.message
                : "Unable to schedule interview.";
    }
}
</script>

<template>
    <main class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-3xl">
            <div class="mb-6">
                <button
                    type="button"
                    class="mb-4 text-sm font-medium text-gray-500 hover:text-indigo-600"
                    @click="router.back()"
                >
                    ← Back to interviews
                </button>

                <h1 class="text-2xl font-bold text-gray-900">
                    Schedule Interview
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Choose an applicant, interview time, and interviewers.
                </p>
            </div>

            <p
                v-if="errorMessage"
                role="alert"
                class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ errorMessage }}
            </p>

            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6"
            >
                <ScheduleInterviewModal
                    :open="true"
                    :applications="applications"
                    :interviewers="interviewers"
                    :saving="createInterview.isPending.value"
                    @close="router.back()"
                    @submit="submitInterview"
                    @manage-interviewers="showInterviewerModal = true"
                />
            </div>
        </div>

        <InterviewerManagementModal
            :open="showInterviewerModal"
            :interviewers="interviewers"
            @close="showInterviewerModal = false"
            @create="(data) => createInterviewer.mutate(data)"
            @update="(id, data) => updateInterviewer.mutate({ id, data })"
            @delete="(id) => deleteInterviewer.mutate(id)"
        />
    </main>
</template>
