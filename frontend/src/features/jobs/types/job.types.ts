export interface JobCompany {
    id: number;
    name: string;
    logo: string | null;
}

export interface JobCategory {
    id: number;
    name: string;
}

export type EmploymentType =
    | "full_time"
    | "part_time"
    | "contract"
    | "internship";

export type WorkplaceType = "onsite" | "remote" | "hybrid";

export type SalaryType = "fixed" | "negotiable";

export type SubmissionStatus = "draft" | "pending";

export interface Job {
    id: number;
    company_id: number;
    category_id: number | null;

    title: string;
    description: string;
    requirements: string;
    responsibilities: string | null;

    employment_type: EmploymentType;

    workplace_type: WorkplaceType;
    location: string | null;
    salary_type: SalaryType;
    salary_min: number | null;
    salary_max: number | null;
    vacancies: number;
    deadline: string | null;

    status: "draft" | "pending" | "published" | "rejected" | "closed";

    company?: JobCompany;
    category?: JobCategory;

    created_at: string;
    updated_at: string;
}

// export interface JobFormData {
//     title: string;
//     category_id: number | null;
//     description: string;
//     requirements: string;
//     responsibilities: string;
//     employment_type: EmploymentType;
//     workplace_type: WorkplaceType;
//     location: string;
//     salary_type: SalaryType;
//     salary_min: number | null;
//     salary_max: number | null;
//     vacancies: number;
//     deadline: string;
//     submission_status: SubmissionStatus;
// }
export interface JobFormData {
    title: string;
    category_id: number | null;
    deadline: string;
    employment_type: EmploymentType;
    vacancies: number;
    workplace_type: WorkplaceType;
    country: string;
    city: string;
    salary_type: SalaryType;
    salary_min: number | undefined;
    salary_max: number | undefined;
    description: string;
    requirements: string;
    responsibilities: string;
    submission_status: SubmissionStatus;
}

export type JobFormField = keyof JobFormData;

export interface JobListParams {
    page?: number;
    search?: string;
    category_id?: number;
    employment_type?: Job["employment_type"];
    workplace_type?: Job["workplace_type"];
    location?: string;
}

export interface JobListResponse {
    data: Job[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}
