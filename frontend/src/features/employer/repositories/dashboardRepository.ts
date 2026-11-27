import api from "@/services/api";

import type {
  ApiResponse,
  ApplicantStats,
  OfferStats,
  InterviewDashboard,
} from "../types/dashboard.types";

export const dashboardRepository = {
  async getApplicantStats(): Promise<ApplicantStats> {
    const response = await api.get<ApiResponse<ApplicantStats>>(
      "/employer/applicants/stats",
    );

    return response.data.data;
  },

  async getOfferStats(): Promise<OfferStats> {
    const response = await api.get<ApiResponse<OfferStats>>(
      "/employer/offers/stats",
    );

    return response.data.data;
  },

  async getInterviewDashboard(): Promise<InterviewDashboard> {
    const response = await api.get<ApiResponse<InterviewDashboard>>(
      "/employer/interviews/stats",
    );

    return response.data.data;
  },

  async getYearlyApplicantStats(year: number) {
    const response = await api.get("/employer/applicants/yearly-stats", {
      params: { year },
    });

    return response.data.data;
  },
};
