export type CompanyVerificationStatus =
    | "pending"
    | "verified"
    | "rejected";

export type CompanyStatusFilter =
    | "all"
    | CompanyVerificationStatus;

export interface CompanyEmployer {
    id: number;
    name: string;
    email: string;
}

export interface Company {
    id: number;
    employer_id: number;

    name: string;
    tagline: string | null;

    logo: string | null;
    cover_image: string | null;

    founded_year: number | null;
    employee_count: string | null;

    description: string | null;
    industry: string | null;
    website: string | null;

    company_email: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;

    gallery: string[];

    verification_status: CompanyVerificationStatus;
    verified_at: string | null;
    rejection_reason: string | null;

    employer?: CompanyEmployer;

    created_at: string;
    updated_at: string;
}

export interface CompanyListParams {
    status?: CompanyStatusFilter;
    search?: string;
    page?: number;
    per_page?: number;
}

export interface CompanyApprovalData {
    verification_status: "verified" | "rejected";
    rejection_reason?: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
}

export interface CompanyListResponse {
    success: boolean;
    message: string;
    data: Company[];
    meta: PaginationMeta;
}

export interface CompanyResponse {
    success: boolean;
    message: string;
    data: Company;
}
