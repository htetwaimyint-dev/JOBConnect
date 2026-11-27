
import { computed, ref } from "vue";

import employerApplicationRepository from "../repositories/employerApplicationRepository";

import type {
    EmployerApplication,
    EmployerApplicationParams,
    ApplicationStatus,
    UpdateEmployerApplicationStatusData,
    EmployerBatchAiEvaluationItem,
} from "../types/employer-application.types";

export function useEmployerApplications() {
    // Application Data
    const applications = ref<EmployerApplication[]>([]);
    const selectedApplication = ref<EmployerApplication | null>(null);

    // Loading and Error
    const loading = ref(false);
    const detailLoading = ref(false);
    const updatingStatus = ref(false);
    const errorMessage = ref("");

    // AI Evaluation
    const evaluatingApplicant = ref(false);
    const evaluationError = ref("");

    // Batch AI Evaluation
    const evaluatingAllApplicants = ref(false);
    const batchEvaluationError = ref("");

    const batchEvaluationResults = ref<
        EmployerBatchAiEvaluationItem[]
    >([]);

    const batchEvaluationSummary = ref({
        total_applicants: 0,
        successful: 0,
        failed: 0,
        returned: 0,
    });

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

    // Search and Filters
    const searchKeyword = ref("");
    const selectedStatus = ref<ApplicationStatus | "">("");
    const selectedCategory = ref<number | "">("");

    // Job Filter
    const selectedJobId = ref<number | null>(null);

    // Pagination
    const currentPage = ref(1);

    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
    });

    // Fetch Applications
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

    // Fetch Application Detail
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

    // Update Application Status
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

    // Evaluate one applicant using AI
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

            // Update only the currently selected applicant.
            if (selectedApplication.value?.id === applicationId) {
                selectedApplication.value = {
                    ...selectedApplication.value,
                    ai_evaluation: {
                        ...response.data.result,
                        analysis_id: response.data.analysis_id,
                    },
                };
            }

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

    // Evaluate all eligible applicants for a job
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

        evaluatingAllApplicants.value = true;
        batchEvaluationError.value = "";
        batchEvaluationResults.value = [];

        batchEvaluationSummary.value = {
            total_applicants: 0,
            successful: 0,
            failed: 0,
            returned: 0,
        };

        try {
            const response =
                await employerApplicationRepository.evaluateAllApplicants(
                    jobId,
                );

            const { data } = response;

            batchEvaluationSummary.value = {
                total_applicants: data.total_applicants,
                successful: data.successful,
                failed: data.failed,
                returned: data.returned,
            };

            batchEvaluationResults.value = [...data.evaluations]
                .sort((a, b) => {
                    if (b.match_score !== a.match_score) {
                        return b.match_score - a.match_score;
                    }

                    return a.application_id - b.application_id;
                })
                .slice(0, 10);

            // Update AI evaluation data for applicants
            // already available in the current application list.
            const evaluationMap = new Map(
                batchEvaluationResults.value.map((item) => [
                    item.application_id,
                    item,
                ]),
            );

            applications.value = applications.value.map((application) => {
                const evaluation = evaluationMap.get(application.id);

                if (!evaluation) return application;

                return {
                    ...application,
                    ai_evaluation: {
                        ...evaluation.result,
                        analysis_id: evaluation.analysis_id,
                    },
                };
            });

            // Update the currently selected applicant if applicable.
            if (selectedApplication.value) {
                const evaluation = evaluationMap.get(
                    selectedApplication.value.id,
                );

                if (evaluation) {
                    selectedApplication.value = {
                        ...selectedApplication.value,
                        ai_evaluation: {
                            ...evaluation.result,
                            analysis_id: evaluation.analysis_id,
                        },
                    };
                }
            }

            return true;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            batchEvaluationError.value =
                apiError.response?.data?.message ??
                "Batch AI evaluation failed. Please try again.";

            return false;
        } finally {
            evaluatingAllApplicants.value = false;
        }
    };

    // Statistics
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

    // Search and Filter Changes
    const applyFilters = () => {
        fetchApplications(1);
    };

    // Reset search and status/category filters.
    // Keep selectedJobId so the current job filter remains active.
    const resetFilters = () => {
        searchKeyword.value = "";
        selectedStatus.value = "";
        selectedCategory.value = "";

        fetchApplications(1);
    };

    // Pagination
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

    // Date Formatting
    const formatDate = (date: string | null | undefined): string => {
        if (!date) return "Date unavailable";

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