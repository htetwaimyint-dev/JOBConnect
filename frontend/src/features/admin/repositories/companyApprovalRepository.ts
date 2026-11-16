import api from "@/services/api";

import type {
    CompanyApprovalData,
    CompanyListParams,
    CompanyListResponse,
    CompanyResponse,
} from "../types/company-approval.types";

const companyApprovalRepository = {
    /**
     * Get companies with status filtering and pagination.
     */
    async getPendingCompanies(
        params?: CompanyListParams
    ): Promise<CompanyListResponse> {
        const response = await api.get<CompanyListResponse>(
            "/admin/companies/pending",
            {
                params,
            }
        );

        return response.data;
    },

    /**
     * Get company details by ID.
     */
    async getCompanyById(
        id: number
    ): Promise<CompanyResponse> {
        const response = await api.get<CompanyResponse>(
            `/admin/companies/${id}`
        );

        return response.data;
    },

    /**
     * Approve or reject a company.
     */
    async updateCompanyApproval(
        id: number,
        data: CompanyApprovalData
    ): Promise<CompanyResponse> {
        const response = await api.patch<CompanyResponse>(
            `/admin/companies/${id}/approval`,
            data
        );

        return response.data;
    },
};

export default companyApprovalRepository;
