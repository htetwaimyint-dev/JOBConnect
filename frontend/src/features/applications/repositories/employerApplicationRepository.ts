
import api from "@/services/api";

import type {
    EmployerApplication,
    EmployerApplicationListResponse,
    EmployerApplicationDetailResponse,
    EmployerApplicationParams,
    UpdateEmployerApplicationStatusData,
    EmployerAiEvaluationResponse,
    EmployerBatchAiEvaluationResponse,
} from "../types/employer-application.types";

const employerApplicationRepository = {
    // Get all applications belonging to the employer's company
    async getApplications(
        params: EmployerApplicationParams = {},
    ): Promise<EmployerApplicationListResponse> {
        const response = await api.get<EmployerApplicationListResponse>(
            "/employer/applications",
            { params },
        );

        return response.data;
    },

    // Get applications for a specific job
    async getJobApplications(
        jobId: number,
        params: EmployerApplicationParams = {},
    ): Promise<EmployerApplicationListResponse> {
        const response = await api.get<EmployerApplicationListResponse>(
            `/employer/jobs/${jobId}/applications`,
            { params },
        );

        return response.data;
    },

    // Get a single application detail
    async getApplicationDetail(
        applicationId: number,
    ): Promise<EmployerApplication> {
        const response =
            await api.get<EmployerApplicationDetailResponse>(
                `/employer/applications/${applicationId}`,
            );

        return response.data.data;
    },

    // Update application status
    async updateStatus(
        applicationId: number,
        data: UpdateEmployerApplicationStatusData,
    ): Promise<EmployerApplication> {
        const response =
            await api.patch<EmployerApplicationDetailResponse>(
                `/employer/applications/${applicationId}/status`,
                data,
            );

        return response.data.data;
    },

    // Run AI evaluation for a single applicant
    async evaluateSingleApplicant(
        applicationId: number,
    ): Promise<EmployerAiEvaluationResponse> {
        const response = await api.post<EmployerAiEvaluationResponse>(
            `/employer/applications/${applicationId}/ai-screening`,
        );

        return response.data;
    },
    // Run batch AI evaluation for all eligible applicants of a job
    async evaluateAllApplicants(
        jobId: number,
    ): Promise<EmployerBatchAiEvaluationResponse> {
        const response =
            await api.post<EmployerBatchAiEvaluationResponse>(
                `/employer/jobs/${jobId}/ai-screening`,
            );

        return response.data;
    },
};

export default employerApplicationRepository;
