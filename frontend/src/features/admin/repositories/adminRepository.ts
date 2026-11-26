import api from "@/services/api";

export const adminRepository = {

  // Dashboard
  async getDashboard() {
    const res = await api.get("/admin/dashboard");
    return res.data;
  },

  // Company Management
  async getCompanies(
    params: {
      page?: number;
      search?: string;
      status?: string;
      per_page?: number;
    } = {},
  ) {
    const res = await api.get("/admin/companies", {
      params: {
        page: params.page ?? 1,
        search: params.search,
        status: params.status,
        per_page: params.per_page ?? 10,
      },
    });

    return res.data;
  },

  async suspendCompany(id: number) {
    return await api.patch(`/admin/companies/${id}/suspend`);
  },

  async restoreCompany(id: number) {
    return await api.patch(`/admin/companies/${id}/restore`);
  },

  // Job Seeker Management

  async getJobSeekers(
    params: {
      page?: number;
      search?: string;
      status?: string;
      per_page?: number;
    } = {},
  ) {
    const res = await api.get("/admin/job-seekers", {
      params: {
        page: params.page ?? 1,
        search: params.search,
        status: params.status,
        per_page: params.per_page ?? 10,
      },
    });

    return res.data;
  },

  async suspendJobSeeker(id: number) {
    return await api.patch(`/admin/job-seekers/${id}/suspend`);
  },

  async restoreJobSeeker(id: number) {
    return await api.patch(`/admin/job-seekers/${id}/restore`);
  },
};
