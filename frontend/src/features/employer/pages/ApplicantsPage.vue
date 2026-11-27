<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";

import ApplicantsFilters from "@/features/employer/components/applicants/ApplicantsFilters.vue";
import ApplicantsTable from "@/features/employer/components/applicants/ApplicantsTable.vue";
import ApplicantDetailsDrawer from "@/features/employer/components/applicants/ApplicantDetailsDrawer.vue";
import ApplicantResumePreview from "@/features/employer/components/applicants/ApplicantResumePreview.vue";

import { resumeRepository } from "@/features/job-seeker/repositories/resumeRepository";
import { getImageUrl } from "@/utils/image.utils";

import { useEmployerApplications } from "@/features/applications/composables/useEmployerApplications";

import type {
    ApplicationStatus,
    UpdateEmployerApplicationStatusData,
} from "@/features/applications/types/employer-application.types";

// Employer Applications
const {
    applications,
    selectedApplication,
    loading,
    detailLoading,
    updatingStatus,
    errorMessage,
    searchKeyword,
    selectedStatus,
    pagination,
    evaluatingApplicant,
    evaluationError,
    evaluateSingleApplicant,
    fetchApplications,
    fetchApplicationDetail,
    updateApplicationStatus,
    applyFilters,
    resetFilters,
    changePage,
    formatDate,
} = useEmployerApplications();

// Drawer State
const drawerOpen = ref(false);
const statusNote = ref("");

// Resume Preview State
const previewOpen = ref(false);
const previewLoading = ref(false);
const previewError = ref("");
const previewUrl = ref<string | null>(null);

// Drawer Date Formatter
const formatHistoryDate = (value: string | null | undefined): string => {
    return value ? formatDate(value) : "—";
};

// Close Resume Preview and release its object URL.
const closeResumePreview = () => {
    previewOpen.value = false;
    previewError.value = "";

    if (previewUrl.value) {
        window.URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }
};

// Preview Applicant Resume.
const previewResume = async () => {
    const resumeId = selectedApplication.value?.resume?.id;

    if (!resumeId) return;

    closeResumePreview();

    previewOpen.value = true;
    previewLoading.value = true;

    try {
        const blob = await resumeRepository.previewForEmployer(resumeId);

        if (!blob.type.includes("pdf")) {
            throw new Error("The server did not return a valid PDF file.");
        }

        previewUrl.value = window.URL.createObjectURL(blob);
    } catch (error) {
        console.error("Failed to preview resume:", error);

        previewError.value =
            "Unable to preview this resume. Please try downloading it instead.";
    } finally {
        previewLoading.value = false;
    }
};

// Release object URL when page is destroyed.
onUnmounted(() => {
    if (previewUrl.value) {
        window.URL.revokeObjectURL(previewUrl.value);
    }
});

// Application Status Options
const statusOptions: ApplicationStatus[] = [
    "pending",
    "reviewed",
    "shortlisted",
    "interview",
    "accepted",
    "rejected",
    "withdrawn",
];

// Available Next Status Options
const nextStatusOptions = computed<
    UpdateEmployerApplicationStatusData["new_status"][]
>(() => {
    switch (selectedApplication.value?.status) {
        case "reviewed":
            return ["shortlisted", "rejected"];

        case "shortlisted":
        case "interview":
            return ["accepted", "rejected"];

        default:
            return [];
    }
});

// Open Applicant Details.
const openApplicant = async (id: number) => {
    drawerOpen.value = true;
    statusNote.value = "";
    selectedApplication.value = null;

    await fetchApplicationDetail(id);
};

// Close Applicant Details.
const closeDrawer = () => {
    closeResumePreview();

    drawerOpen.value = false;
    selectedApplication.value = null;
    statusNote.value = "";
};

// Update Application Status.
const changeApplicationStatus = async (
    newStatus: UpdateEmployerApplicationStatusData["new_status"],
) => {
    if (!selectedApplication.value) return;

    const id = selectedApplication.value.id;

    const success = await updateApplicationStatus(id, {
        new_status: newStatus,
        note: statusNote.value.trim() || undefined,
    });

    if (success) {
        closeResumePreview();
        statusNote.value = "";

        await fetchApplicationDetail(id);
    }
};

// Applicant Profile Photo
const profilePhotoUrl = computed(() => {
    const path = selectedApplication.value?.job_seeker?.profile?.profile_photo;

    return typeof path === "string" && path
        ? getImageUrl(path)
        : null;
});

// Applicant Profile
const profile = computed(() => {
    return selectedApplication.value?.job_seeker?.profile;
});

// Professional Title
const professionalTitle = computed(() => {
    const value = profile.value?.professional_title;

    return typeof value === "string" ? value : null;
});

// AI Evaluation is only available for pending/reviewed applications.
const canEvaluateApplicant = computed(() => {
    return ["pending", "reviewed"].includes(
        selectedApplication.value?.status ?? "",
    );
});

// Run AI Evaluation for the selected applicant.
const runApplicantEvaluation = async () => {
    if (!selectedApplication.value || !canEvaluateApplicant.value) {
        return;
    }

    await evaluateSingleApplicant(selectedApplication.value.id);
};

// Initial Applications Fetch
onMounted(() => {
    fetchApplications();
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            <!-- Header -->
            <div>
                <p class="text-sm font-semibold text-blue-700">
                    EMPLOYER WORKSPACE
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Applicants
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Review candidates and manage applications for your jobs.
                </p>
            </div>

            <!-- Search and Filters -->
            <ApplicantsFilters v-model:search-keyword="searchKeyword" v-model:selected-status="selectedStatus"
                :status-options="statusOptions" :loading="loading" @search="applyFilters" @reset="resetFilters" />

            <!-- Error -->
            <div v-if="errorMessage && !drawerOpen"
                class="flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <span>{{ errorMessage }}</span>

                <button class="font-semibold underline" @click="fetchApplications()">
                    Retry
                </button>
            </div>

            <!-- Applicants Table -->
            <ApplicantsTable :applications="applications" :loading="loading" :pagination="pagination"
                :format-date="formatDate" @open-applicant="openApplicant" @change-page="changePage" />
        </div>

        <!-- Applicant Details Drawer -->
        <ApplicantDetailsDrawer v-model:status-note="statusNote" :open="drawerOpen" :detail-loading="detailLoading"
            :selected-application="selectedApplication" :error-message="errorMessage"
            :profile-photo-url="profilePhotoUrl" :professional-title="professionalTitle"
            :preview-loading="previewLoading" :evaluating-applicant="evaluatingApplicant"
            :can-evaluate-applicant="canEvaluateApplicant" :evaluation-error="evaluationError"
            :next-status-options="nextStatusOptions" :updating-status="updatingStatus" :format-date="formatHistoryDate"
            @close="closeDrawer" @preview-resume="previewResume" @run-evaluation="runApplicantEvaluation"
            @change-status="changeApplicationStatus" />

        <!-- Resume Preview Modal -->
        <ApplicantResumePreview :open="previewOpen" :loading="previewLoading" :error="previewError"
            :preview-url="previewUrl" :resume-title="selectedApplication?.resume?.title || 'Applicant Resume'"
            @close="closeResumePreview" />
    </div>
</template>

<style>
.drawer-enter-active,
.drawer-leave-active {
    transition: opacity 0.2s ease;
}

.drawer-enter-active aside,
.drawer-leave-active aside {
    transition: transform 0.25s ease;
}

.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}

.drawer-enter-from aside,
.drawer-leave-to aside {
    transform: translateX(100%);
}
</style>
