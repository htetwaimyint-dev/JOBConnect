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
            await api.get<ApiResponse<Company>>("/company/profile");

        if (!response.data.success || !response.data.data) {
            throw new Error(
                response.data.message || "Unable to retrieve company profile",
            );
        }

        return response.data.data;
    },

    // A missing profile should return HTTP 404 from the backend.
    async getCompanyProfileForForm(): Promise<Company | null> {
        try {
            return await this.getCompanyProfile();
        } catch (error: unknown) {
            const status = (error as { response?: { status?: number } })
                ?.response?.status;

            if (status === 404) return null;

            throw error;
        }
    },

    async createCompanyProfile(formData: FormData): Promise<Company> {
        const response = await api.post<ApiResponse<Company>>(
            "/company/profile",
            formData,
        );

        if (!response.data.success || !response.data.data) {
            throw new Error(
                response.data.message || "Unable to create company profile",
            );
        }

        return response.data.data;
    },

    async updateCompanyProfile(formData: FormData): Promise<Company> {
        const response = await api.put<ApiResponse<Company>>(
            "/company/profile",
            formData,
        );

        if (!response.data.success || !response.data.data) {
            throw new Error(
                response.data.message || "Unable to update company profile",
            );
        }

        return response.data.data;
    },
};
