import api from "@/services/api";
import type {
    Interview,
    InterviewFilters,
    InterviewFormData,
    InterviewApplication,
    Interviewer,
    InterviewerFormData,
    InterviewStatus,
    JobseekerInterviewFilters,
} from "../types/interview.types";

type ApiResponse<T> = {
    success: boolean;
    message: string;
    data: T;
};

const interviewRepository = {
    async getInterviews(filters?: Partial<InterviewFilters>) {
        const response = await api.get<ApiResponse<Interview[]>>(
            "/employer/interviews",
            { params: filters },
        );

        return response.data.data;
    },

    async getInterview(id: number) {
        const response = await api.get<ApiResponse<Interview>>(
            `/employer/interviews/${id}`,
        );

        return response.data.data;
    },

    async getAvailableApplications() {
        const response = await api.get<ApiResponse<InterviewApplication[]>>(
            "/employer/interviews/available-applications",
        );

        return response.data.data;
    },

    async getJobseekerInterviews(filters?: Partial<JobseekerInterviewFilters>) {
        const response = await api.get<ApiResponse<Interview[]>>(
            "/job-seeker/interviews",
            { params: filters },
        );

        return response.data.data;
    },

    async requestReschedule(
        id: number,
        data: { scheduled_at?: string | null; reason?: string | null },
    ) {
        const response = await api.patch<ApiResponse<Interview>>(
            `/job-seeker/interviews/${id}/request-reschedule`,
            data,
        );

        return response.data.data;
    },

    async createInterview(data: InterviewFormData) {
        const response = await api.post<ApiResponse<Interview>>(
            "/employer/interviews",
            data,
        );

        return response.data.data;
    },

    async updateInterview(id: number, data: Partial<InterviewFormData>) {
        const response = await api.put<ApiResponse<Interview>>(
            `/employer/interviews/${id}`,
            data,
        );

        return response.data.data;
    },

    async updateInterviewStatus(id: number, status: InterviewStatus) {
        const response = await api.patch<ApiResponse<Interview>>(
            `/employer/interviews/${id}/status`,
            { status },
        );

        return response.data.data;
    },

    async getInterviewers() {
        const response = await api.get<ApiResponse<Interviewer[]>>(
            "/employer/interviewers",
        );

        return response.data.data;
    },

    async createInterviewer(data: InterviewerFormData) {
        const response = await api.post<ApiResponse<Interviewer>>(
            "/employer/interviewers",
            data,
        );

        return response.data.data;
    },

    async updateInterviewer(id: number, data: InterviewerFormData) {
        const response = await api.put<ApiResponse<Interviewer>>(
            `/employer/interviewers/${id}`,
            data,
        );

        return response.data.data;
    },

    async deleteInterviewer(id: number) {
        const response = await api.delete<ApiResponse<null>>(
            `/employer/interviewers/${id}`,
        );

        return response.data.data;
    },
};

export default interviewRepository;
