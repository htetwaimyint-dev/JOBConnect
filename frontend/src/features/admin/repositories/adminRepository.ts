import api from "@/services/api";

export const adminRepository = {
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

  async getJobSeekers(page = 1) {
    const res = await api.get(`/admin/job-seekers?page=${page}`);
    return res.data;
  },
  async deleteJobSeeker(id: number) {
    return await api.delete(`/admin/job-seekers/${id}`);
  },
};
