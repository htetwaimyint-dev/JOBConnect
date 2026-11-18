import api from "@/services/api";

import type {
    JobApprovalData,
    JobListParams,
    JobListResponse,
    JobResponse,
} from "../types/job-approval.types";

const jobApprovalRepository = {
    async getPendingJobs(
        params?: JobListParams
    ): Promise<JobListResponse> {
        const response = await api.get<JobListResponse>(
            "/admin/jobs/pending",
            { params }
        );

        return response.data;
    },

    async getJobById(id: number): Promise<JobResponse> {
        const response = await api.get<JobResponse>(
            `/admin/jobs/${id}`
        );

        return response.data;
    },

    async updateJobApproval(
        id: number,
        data: JobApprovalData
    ): Promise<JobResponse> {
        const response = await api.patch<JobResponse>(
            `/admin/jobs/${id}/approval`,
            data
        );

        return response.data;
    },
};

export default jobApprovalRepository;