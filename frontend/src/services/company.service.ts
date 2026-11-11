import api from "./api";
import type { Company } from "@/features/employer/types/company.types";

interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T | null;
}

export const companyService = {
    async getCompanyProfile(): Promise<Company> {
        const response =
            await api.get<ApiResponse<Company>>("/employer/profile");

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
