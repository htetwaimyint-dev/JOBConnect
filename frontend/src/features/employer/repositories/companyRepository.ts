import api from "@/services/api";
import type {
  Company,
  CompanyDetailResponse,
} from "@/features/employer/types/company.types";

interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T | null;
}

export const companyRepository = {
  //fetch companies for job seekers
  async getCompanies(search: string = ""): Promise<Company[]> {
    const response = await api.get("job-seeker/companies", {
      params: { search: search },
    });

    if (!response.data.success || !response.data.data) {
      throw new Error(response.data.message || "Failed to fetch companies");
    }

    return response.data.data;
  },
  //fetch company details for job seekers
  async getCompanyById(id: number): Promise<Company> {
    const response = await api.get<ApiResponse<Company>>(
      `job-seeker/companies/${id}`,
    );

    if (!response.data.success || !response.data.data) {
      throw new Error(
        response.data.message || "Failed to fetch company details",
      );
    }

    return response.data.data;
  },
  async getCompanyJobs(
    id: number,
    params?: {
      page?: number;
      keyword?: string;
      job_type?: string;
      location?: string;
    },
  ): Promise<any> {
    const response = await api.get(`job-seeker/companies/${id}`, { params });

    if (!response.data.success || !response.data.data) {
      throw new Error(
        response.data.message || "Failed to fetch company jobs and details",
      );
    }

    const payload = response.data.data;

    return {
      status: true,
      company: payload.company ?? payload,
      jobs: payload.jobs ?? [],
    };
  },

  async getCompanyProfile(): Promise<Company> {
    const response = await api.get<ApiResponse<Company>>("/employer/profile");

    if (!response.data.success || !response.data.data) {
      throw new Error(response.data.message);
    }

    return response.data.data;
  },

  async getCompanyProfileForForm(): Promise<Company | null> {
    try {
      return await this.getCompanyProfile();
    } catch (error: any) {
      if (error?.response?.status === 404) {
        return null;
      }

      throw error;
    }
  },

  async createCompanyProfile(formData: FormData): Promise<Company> {
    const response = await api.post<ApiResponse<Company>>(
      "/employer/profile",
      formData,
    );

    if (!response.data.success || !response.data.data) {
      throw new Error(response.data.message);
    }

    return response.data.data;
  },

  async updateCompanyProfile(formData: FormData): Promise<Company> {
    formData.append("_method", "PUT");

    const response = await api.post<ApiResponse<Company>>(
      "/employer/profile",
      formData,
    );

    if (!response.data.success || !response.data.data) {
      throw new Error(response.data.message);
    }

    return response.data.data;
  },
};
