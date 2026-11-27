
import { computed, onScopeDispose, ref } from "vue";

import employerApplicationRepository from "../repositories/employerApplicationRepository";

import type {
    EmployerApplication,
    EmployerApplicationParams,
    ApplicationStatus,
    UpdateEmployerApplicationStatusData,
    EmployerAiEvaluationResult,
    EmployerBatchAiEvaluationItem,
    EmployerBatchAiEvaluationProgressResponse,
} from "../types/employer-application.types";

export function useEmployerApplications() {
    // ==========================================
    // Application Data
    // ==========================================

    const applications = ref<EmployerApplication[]>([]);
    const selectedApplication = ref<EmployerApplication | null>(null);

    // ==========================================
    // Loading and Error
    // ==========================================

    const loading = ref(false);
    const detailLoading = ref(false);
    const updatingStatus = ref(false);
    const errorMessage = ref("");

    // ==========================================
    // Single AI Evaluation
    // ==========================================

    const evaluatingApplicant = ref(false);
    const evaluationError = ref("");

    // ==========================================
    // Batch AI Evaluation
    // ==========================================

    const evaluatingAllApplicants = ref(false);
    const batchEvaluationError = ref("");

    const activeBatchId = ref<number | null>(null);
    const batchStatus = ref<string | null>(null);
    const batchProgress = ref(0);

    const batchEvaluationResults = ref<
        EmployerBatchAiEvaluationItem[]
    >([]);

    const batchEvaluationSummary = ref({
        total_applicants: 0,
        successful: 0,
        failed: 0,
        processing: 0,
        queued: 0,
        returned: 0,
    });

    // Rank all completed applicants by match score.
    // Only the first 10 are displayed.
    const topTenEvaluations = computed(() =>
        [...batchEvaluationResults.value]
            .sort((a, b) => {
                if (b.match_score !== a.match_score) {
                    return b.match_score - a.match_score;
                }

                return a.application_id - b.application_id;
            })
            .slice(0, 10),
    );

    // ==========================================
    // Batch Polling
    // ==========================================

    let batchPollingTimer: ReturnType<typeof setTimeout> | null = null;

    const stopBatchPolling = () => {
        if (batchPollingTimer !== null) {
            clearTimeout(batchPollingTimer);
            batchPollingTimer = null;
        }
    };

    // Clean up polling when the component scope is disposed.
    onScopeDispose(stopBatchPolling);

    // ==========================================
    // Search and Filters
    // ==========================================

    const searchKeyword = ref("");
    const selectedStatus = ref<ApplicationStatus | "">("");
    const selectedCategory = ref<number | "">("");

    // Job Filter
    const selectedJobId = ref<number | null>(null);

    // ==========================================
    // Pagination
    // ==========================================

    const currentPage = ref(1);

    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
    });

    // ==========================================
    // Fetch Applications
    // ==========================================

    const fetchApplications = async (page = 1) => {
        loading.value = true;
        errorMessage.value = "";

        try {
            const params: EmployerApplicationParams = {
                page,
                perpage: pagination.value.per_page,
                search: searchKeyword.value.trim() || undefined,
                status: selectedStatus.value || undefined,
                category_id: selectedCategory.value || undefined,
            };

            const response = selectedJobId.value
                ? await employerApplicationRepository.getJobApplications(
                    selectedJobId.value,
                    params,
                )
                : await employerApplicationRepository.getApplications(
                    params,
                );

            applications.value = response.data;
            pagination.value = response.meta;
            currentPage.value = response.meta.current_page;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            errorMessage.value =
                apiError.response?.data?.message ??
                "Failed to load applicants. Please try again.";
        } finally {
            loading.value = false;
        }
    };

    // ==========================================
    // Fetch Application Detail
    // ==========================================

    const fetchApplicationDetail = async (applicationId: number) => {
        detailLoading.value = true;
        errorMessage.value = "";

        try {
            selectedApplication.value =
                await employerApplicationRepository.getApplicationDetail(
                    applicationId,
                );

            // Refresh list to reflect automatic pending -> reviewed change.
            await fetchApplications(currentPage.value);
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            errorMessage.value =
                apiError.response?.data?.message ??
                "Failed to load applicant details.";
        } finally {
            detailLoading.value = false;
        }
    };

    // ==========================================
    // Update Application Status
    // ==========================================

    const updateApplicationStatus = async (
        applicationId: number,
        data: UpdateEmployerApplicationStatusData,
    ): Promise<boolean> => {
        updatingStatus.value = true;
        errorMessage.value = "";

        try {
            const updatedApplication =
                await employerApplicationRepository.updateStatus(
                    applicationId,
                    data,
                );

            selectedApplication.value = updatedApplication;

            await fetchApplications(currentPage.value);

            return true;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            errorMessage.value =
                apiError.response?.data?.message ??
                "Failed to update application status.";

            return false;
        } finally {
            updatingStatus.value = false;
        }
    };

    // ==========================================
    // Single Applicant AI Evaluation
    // ==========================================

    const evaluateSingleApplicant = async (
        applicationId: number,
    ): Promise<boolean> => {
        if (
            !selectedApplication.value ||
            selectedApplication.value.id !== applicationId
        ) {
            return false;
        }

        if (
            !["pending", "reviewed"].includes(
                selectedApplication.value.status,
            )
        ) {
            evaluationError.value =
                "This application is not eligible for AI evaluation.";

            return false;
        }

        evaluatingApplicant.value = true;
        evaluationError.value = "";

        try {
            const response =
                await employerApplicationRepository.evaluateSingleApplicant(
                    applicationId,
                );

            console.log("AI Evaluation API Response:", response);
            console.log("AI Evaluation Result:", response.data.result);

            const evaluation: EmployerAiEvaluationResult = {
                ...response.data.result,
                analysis_id: response.data.analysis_id,
            };

            // Update the currently selected applicant.
            if (selectedApplication.value?.id === applicationId) {
                selectedApplication.value = {
                    ...selectedApplication.value,
                    ai_evaluation: evaluation,
                };
            }

            // Also update the application list.
            applications.value = applications.value.map((application) =>
                application.id === applicationId
                    ? {
                        ...application,
                        ai_evaluation: evaluation,
                    }
                    : application,
            );

            return true;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            evaluationError.value =
                apiError.response?.data?.message ??
                "AI evaluation failed. Please try again.";

            return false;
        } finally {
            evaluatingApplicant.value = false;
        }
    };

    // ==========================================
    // Apply Batch Progress
    // ==========================================

    const applyBatchProgress = (
        data: EmployerBatchAiEvaluationProgressResponse["data"],
    ) => {
        batchStatus.value = data.status;
        batchProgress.value = data.progress_percentage;

        batchEvaluationSummary.value = {
            total_applicants: data.total_applicants,
            successful: data.completed_count,
            failed: data.failed_count,
            processing: data.processing_count,
            queued: data.queued_count,
            returned: data.top_applicants.length,
        };

        // Keep all valid completed results.
        // Top 10 is calculated separately.
        batchEvaluationResults.value = data.completed_applicants;

        // Convert batch result into the application AI evaluation format.
        const evaluationMap = new Map<
            number,
            EmployerAiEvaluationResult
        >(
            data.completed_applicants.map((item) => [
                item.application_id,
                {
                    analysis_id: item.analysis_id,
                    match_score: item.match_score,
                    matched_skills: item.matched_skills,
                    missing_skills: item.missing_skills,
                    relevant_experience: [],
                    strengths: item.strengths,
                    weaknesses: item.weaknesses,
                    recommendation: item.recommendation,
                    reasoning: item.reasoning,
                },
            ]),
        );

        // Update applicants already loaded in the current list.
        applications.value = applications.value.map((application) => {
            const evaluation = evaluationMap.get(application.id);

            if (!evaluation) {
                return application;
            }

            return {
                ...application,
                ai_evaluation: evaluation,
            };
        });

        // Update the currently selected applicant.
        if (selectedApplication.value) {
            const evaluation = evaluationMap.get(
                selectedApplication.value.id,
            );

            if (evaluation) {
                selectedApplication.value = {
                    ...selectedApplication.value,
                    ai_evaluation: evaluation,
                };
            }
        }
    };

    // ==========================================
    // Poll Batch Progress
    // ==========================================

    const pollBatchProgress = async (
        jobId: number,
        batchId: number,
    ): Promise<void> => {
        try {
            const response =
                await employerApplicationRepository.getBatchProgress(
                    jobId,
                    batchId,
                );

            if (!response.success) {
                throw new Error(response.message);
            }

            applyBatchProgress(response.data);

            // Stop polling when every batch item is processed.
            if (response.data.is_final) {
                stopBatchPolling();

                evaluatingAllApplicants.value = false;

                return;
            }

            // Schedule the next request after the current one finishes.
            batchPollingTimer = setTimeout(() => {
                void pollBatchProgress(jobId, batchId);
            }, 2500);
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
                message?: string;
            };

            batchEvaluationError.value =
                apiError.response?.data?.message ??
                apiError.message ??
                "Unable to retrieve batch progress. Retrying...";

            // Retry after a temporary request failure.
            batchPollingTimer = setTimeout(() => {
                void pollBatchProgress(jobId, batchId);
            }, 5000);
        }
    };

    // ==========================================
    // Start Batch AI Evaluation
    // ==========================================

    const evaluateAllApplicants = async (
        jobId: number,
    ): Promise<boolean> => {
        if (!Number.isInteger(jobId) || jobId <= 0) {
            batchEvaluationError.value = "Invalid job ID.";

            return false;
        }

        if (evaluatingAllApplicants.value) {
            return false;
        }

        stopBatchPolling();

        evaluatingAllApplicants.value = true;
        batchEvaluationError.value = "";
        batchEvaluationResults.value = [];

        activeBatchId.value = null;
        batchStatus.value = null;
        batchProgress.value = 0;

        batchEvaluationSummary.value = {
            total_applicants: 0,
            successful: 0,
            failed: 0,
            processing: 0,
            queued: 0,
            returned: 0,
        };

        try {
            // Start the batch.
            const response =
                await employerApplicationRepository.evaluateAllApplicants(
                    jobId,
                );

            if (!response.success) {
                throw new Error(response.message);
            }

            const batch = response.data;

            activeBatchId.value = batch.batch_id;
            batchStatus.value = batch.status;

            batchEvaluationSummary.value.total_applicants =
                batch.total_applicants;

            // Fetch the first progress response immediately.
            await pollBatchProgress(jobId, batch.batch_id);

            return true;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
                message?: string;
            };

            batchEvaluationError.value =
                apiError.response?.data?.message ??
                apiError.message ??
                "Unable to start batch AI evaluation.";

            stopBatchPolling();
            evaluatingAllApplicants.value = false;

            return false;
        }
    };

    // ==========================================
    // Statistics
    // ==========================================

    const pageStats = computed(() => ({
        total: pagination.value.total,

        pending: applications.value.filter(
            (item) => item.status === "pending",
        ).length,

        shortlisted: applications.value.filter(
            (item) => item.status === "shortlisted",
        ).length,

        interview: applications.value.filter(
            (item) => item.status === "interview",
        ).length,

        accepted: applications.value.filter(
            (item) => item.status === "accepted",
        ).length,
    }));

    // ==========================================
    // Search and Filter Changes
    // ==========================================

    const applyFilters = () => {
        fetchApplications(1);
    };

    // Keep selectedJobId when resetting other filters.
    const resetFilters = () => {
        searchKeyword.value = "";
        selectedStatus.value = "";
        selectedCategory.value = "";

        fetchApplications(1);
    };

    // ==========================================
    // Pagination
    // ==========================================

    const changePage = (page: number) => {
        if (
            page < 1 ||
            page > pagination.value.last_page ||
            loading.value
        ) {
            return;
        }

        fetchApplications(page);
    };

    // ==========================================
    // Date Formatting
    // ==========================================

    const formatDate = (date: string | null | undefined): string => {
        if (!date) {
            return "Date unavailable";
        }

        const parsedDate = new Date(date);

        if (Number.isNaN(parsedDate.getTime())) {
            return "Date unavailable";
        }

        return parsedDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    // ==========================================
    // Return
    // ==========================================

    return {
        applications,
        selectedApplication,

        loading,
        detailLoading,
        updatingStatus,
        errorMessage,

        searchKeyword,
        selectedStatus,
        selectedCategory,
        selectedJobId,

        currentPage,
        pagination,
        pageStats,

        evaluatingApplicant,
        evaluationError,

        evaluatingAllApplicants,
        batchEvaluationError,
        activeBatchId,
        batchStatus,
        batchProgress,
        batchEvaluationResults,
        batchEvaluationSummary,
        topTenEvaluations,

        evaluateAllApplicants,
        evaluateSingleApplicant,

        fetchApplications,
        fetchApplicationDetail,
        updateApplicationStatus,

        applyFilters,
        resetFilters,
        changePage,
        formatDate,
    };
}