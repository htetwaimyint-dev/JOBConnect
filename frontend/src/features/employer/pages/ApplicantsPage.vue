<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useRoute } from "vue-router";

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

const route = useRoute();

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
    selectedJobId,
    pagination,
    evaluatingApplicant,
    evaluationError,
    evaluateSingleApplicant,
    // Batch AI Evaluation
    evaluatingAllApplicants,
    batchEvaluationError,
    batchEvaluationSummary,
    topTenEvaluations,
    evaluateAllApplicants,

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

    const applicationId = selectedApplication.value.id;

    console.log("Before Evaluation:", selectedApplication.value);

    const success = await evaluateSingleApplicant(applicationId);

    console.log("Evaluation Success:", success);
    console.log("After Evaluation:", selectedApplication.value);
    console.log(
        "AI Evaluation:",
        selectedApplication.value?.ai_evaluation,
    );
};

// Run batch AI evaluation for the current job.
const runBatchEvaluation = async () => {
    if (!selectedJobId.value || evaluatingAllApplicants.value) {
        return;
    }

    await evaluateAllApplicants(selectedJobId.value);
};

// Initial Applications Fetch
onMounted(() => {
    const queryJobId = route.query.job_id;

    const jobId =
        typeof queryJobId === "string" ? Number(queryJobId) : NaN;

    if (Number.isInteger(jobId) && jobId > 0) {
        selectedJobId.value = jobId;
    }

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

            <!-- Batch AI Evaluation -->
            <section v-if="selectedJobId" class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900">
                                AI Applicant Ranking
                            </h2>

                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                Top 10
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            Evaluate eligible applicants and identify the strongest matches for this job.
                        </p>
                    </div>

                    <button type="button" :disabled="evaluatingAllApplicants || loading" @click="runBatchEvaluation"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <span v-if="evaluatingAllApplicants"
                            class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />

                        {{ evaluatingAllApplicants
                            ? "Evaluating Applicants..."
                            : "Evaluate All & Get Top 10"
                        }}
                    </button>
                </div>

                <!-- Batch Error -->
                <div v-if="batchEvaluationError"
                    class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ batchEvaluationError }}
                </div>

                <!-- Evaluation Summary -->
                <div v-if="batchEvaluationSummary.returned > 0 || batchEvaluationSummary.total_applicants > 0"
                    class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Total Applicants</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ batchEvaluationSummary.total_applicants }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-emerald-50 p-3">
                        <p class="text-xs text-emerald-700">Successful</p>
                        <p class="mt-1 text-xl font-bold text-emerald-800">
                            {{ batchEvaluationSummary.successful }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-red-50 p-3">
                        <p class="text-xs text-red-700">Failed</p>
                        <p class="mt-1 text-xl font-bold text-red-800">
                            {{ batchEvaluationSummary.failed }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-50 p-3">
                        <p class="text-xs text-blue-700">Top Results</p>
                        <p class="mt-1 text-xl font-bold text-blue-800">
                            {{ batchEvaluationSummary.returned }}
                        </p>
                    </div>
                </div>

                <!-- Top 10 Results -->
                <div v-if="topTenEvaluations.length > 0" class="mt-6">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-900">
                            Top Matching Applicants
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Ranked by AI match score. Review each applicant before making a decision.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <article v-for="(evaluation, index) in topTenEvaluations" :key="evaluation.application_id"
                            class="flex flex-col gap-4 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-700">
                                    {{ index + 1 }}
                                </div>

                                <div class="min-w-0">
                                    <h4 class="truncate font-semibold text-slate-900">
                                        {{
                                            applications.find(
                                                (item) => item.id === evaluation.application_id
                                            )?.applicant_name || `Applicant #${evaluation.application_id}`
                                        }}
                                    </h4>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Application #{{ evaluation.application_id }}
                                        ·
                                        {{ evaluation.recommendation.replace("_", " ") }}
                                    </p>

                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        <span v-for="skill in evaluation.result.matched_skills.slice(0, 4)" :key="skill"
                                            class="rounded-md bg-emerald-50 px-2 py-1 text-xs text-emerald-700">
                                            {{ skill }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center justify-between gap-4 sm:justify-end">
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-700">
                                        {{ evaluation.match_score }}%
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Match Score
                                    </p>
                                </div>

                                <button type="button" @click="openApplicant(evaluation.application_id)"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                    View Details
                                </button>
                            </div>
                        </article>
                    </div>

                    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                        AI evaluations are recommendations only. The employer must review
                        applicant details and manually decide whether to shortlist or reject.
                    </p>
                </div>
            </section>

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