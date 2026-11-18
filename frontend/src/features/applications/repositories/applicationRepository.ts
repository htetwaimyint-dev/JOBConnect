
import api from "@/services/api";

import type {
    Application,
    ApplicationListResponse,
    ApplicationStatus,
    JobApplicationQuestion,
    SubmitApplicationData,
} from "../types/application.types";

interface ApplicationListParams {
    page?: number;
    per_page?: number;
    status?: ApplicationStatus;
    search?: string;
}

interface ApplicationDetailResponse {
    success: boolean;
    message: string;
    data: Application;
}

interface JobQuestionsResponse {
    success: boolean;
    message: string;
    data: JobApplicationQuestion[];
}

const applicationRepository = {
    async getMyApplications(
        params: ApplicationListParams = {},
    ): Promise<ApplicationListResponse> {
        const response = await api.get<ApplicationListResponse>(
            "/job-seeker/applications",
            { params },
        );

        return response.data;
    },

    async getApplicationById(
        id: number,
    ): Promise<ApplicationDetailResponse> {
        const response = await api.get<ApplicationDetailResponse>(
            `/job-seeker/applications/${id}`,
        );

        return response.data;
    },

    async getApplicationQuestions(
        jobId: number,
    ): Promise<JobApplicationQuestion[]> {
        const response = await api.get<JobQuestionsResponse>(
            `/jobs/${jobId}/application-questions`,
        );

        return response.data.data;
    },

    async submitApplication(
        data: SubmitApplicationData,
    ): Promise<ApplicationDetailResponse> {
        const response = await api.post<ApplicationDetailResponse>(
            "/job-seeker/applications",
            data,
        );

        return response.data;
    },

    async withdrawApplication(
        id: number,
    ): Promise<ApplicationDetailResponse> {
        const response = await api.patch<ApplicationDetailResponse>(
            `/job-seeker/applications/${id}/withdraw`,
        );

        return response.data;
    },
};

export default applicationRepository;