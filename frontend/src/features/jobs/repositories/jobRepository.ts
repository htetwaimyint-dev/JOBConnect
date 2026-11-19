import api from "@/services/api";
import type { JobFilters } from "../../job-seeker/types/job";

import type {
  Job,
  JobFormData,
  JobListParams,
  JobListResponse,
} from "../types/job.types";

const cleanParams = (params?: JobListParams) => {
  const cleaned = { ...params };

  if (cleaned.status === "all") {
    delete cleaned.status;
  }

  return cleaned;
};

const jobRepository = {
  createJob(data: JobFormData) {
    return api.post("/employer/jobs", data);
  },

  getJobs(params?: JobListParams) {
    return api.get<JobListResponse>("/jobs", {
      params: cleanParams(params),
    });
  },

  getJobsByCompany(params?: JobListParams) {
    return api.get<JobListResponse>("/employer/jobs", {
      params: cleanParams(params),
    });
  },

  async getJobsByCompanyId(companyId: number, filters: JobFilters = {}) {
    const response = await api.get(`/job-seeker/companies/${companyId}/jobs`, {
      params: filters,
    });
    return response.data; // { status: true, company: {...}, jobs: { data: [...], ... } }
  },

  getEmployerJobById(id: number | string) {
    return api.get<{ data: Job }>(`employer/jobs/${id}`);
  },

  getPublicJobById(id: number | string) {
    return api.get<{ data: Job }>(`jobs/${id}`);
  },

  updateJob(id: number, data: JobFormData) {
    return api.put(`/employer/jobs/${id}`, data);
  },

  closeJob(id: number) {
    return api.patch(`/employer/jobs/${id}/close`);
  },

  deleteJob(id: number) {
    return api.delete(`/employer/jobs/${id}`);
  },
};

export default jobRepository;
