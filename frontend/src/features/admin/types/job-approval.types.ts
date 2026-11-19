export type JobStatus =
    | "draft"
    | "pending"
    | "published"
    | "rejected"
    | "closed";

export type JobStatusFilter = "all" | JobStatus;

export type JobApprovalStatus =
    | "pending"
    | "published"
    | "rejected";

export type JobApprovalFilter = "all" | JobApprovalStatus;

export interface JobCompany {
    id: number;
    name: string;
    logo: string | null;
}

export interface JobCategory {
    id: number;
    name: string;
    logo: string | null;
}

export interface ApprovedAdmin {
    id: number;
    name: string;
    email: string;
}

export interface Job {
    id: number;
    company_id: number;

    company: JobCompany | null;
    category: JobCategory | null;

    title: string;
    description: string | null;
    requirements: string | null;
    responsibilities: string | null;

    employment_type: string;
    workplace_type: string;

    gender: string | null;

    country: string | null;
    city: string | null;

    salary_type: string | null;
    salary_min: number | string | null;
    salary_max: number | string | null;

    vacancies: number;
    deadline: string | null;

    status: JobStatus;

    approved_by: number | null;
    approved_admin?: ApprovedAdmin | null;
    approved_at: string | null;

    rejection_reason: string | null;

    created_at: string;
    updated_at: string;
}

export interface JobListParams {
    search?: string;
    page?: number;
    per_page?: number;
}

export interface JobApprovalData {
    status: "published" | "rejected";
    rejection_reason?: string;
}

export interface JobPaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
}

export interface JobListResponse {
    success: boolean;
    message: string;
    data: Job[];
    meta: JobPaginationMeta;
}

export interface JobResponse {
    success: boolean;
    message: string;
    data: Job;
}