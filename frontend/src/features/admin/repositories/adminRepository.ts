import api from "@/services/api";

export const adminRepository = {
  async getCompanies(page = 1) {
    const res = await api.get(`/admin/companies?page=${page}`);
    return res.data;
  },
  async deleteCompany(id: number) {
    return await api.delete(`/admin/companies/${id}`);
  },

  async getJobSeekers(page = 1) {
    const res = await api.get(`/admin/job-seekers?page=${page}`);
    return res.data;
  },
  async deleteJobSeeker(id: number) {
    return await api.delete(`/admin/job-seekers/${id}`);
  },
};
