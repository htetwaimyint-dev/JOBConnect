import api from "@/services/api";

import type {
    Job,
    JobFormData,
    JobListParams,
    JobListResponse,
} from "../types/job.types";
import type { MaybeRefOrGetter } from "vue";

const jobRepository = {
    createJob(data: JobFormData) {
        return api.post("/employer/jobs", data);
    },

    getJobs(params?: JobListParams) {
        return api.get<JobListResponse>("/jobs", {
            params,
        });
    },

    getJobsByCompany(params?: JobListParams) {
        return api.get<JobListResponse>("/employer/jobs", {
            params,
        });
    },

    getJobById(id: MaybeRefOrGetter<number | string>) {
        return api.get<{ data: Job }>(`/jobs/${id}`);
    },

    updateJob(id: number, data: JobFormData) {
        return api.put(`/employer/jobs/${id}`, data);
    },

    deleteJob(id: number) {
        return api.delete(`/employer/jobs/${id}`);
    },
};

export default jobRepository;
