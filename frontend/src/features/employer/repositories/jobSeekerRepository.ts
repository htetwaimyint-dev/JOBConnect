import api from "@/services/api";
import type { JobSeeker } from "../types/job-seeker.types";

export interface JobSeekerResponse {
  success: boolean;
  message: string;
  data: JobSeeker[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
}

interface JobSeekerParams {
  search?: string;
  per_page?: number;
  page?: number;
}

const getJobSeekers = async (
  params: JobSeekerParams = {},
): Promise<JobSeekerResponse> => {
  const response = await api.get<JobSeekerResponse>(
    "/employer/job-seekers",
    { params },
  );

  return response.data;
};

export default {
  getJobSeekers,
};
