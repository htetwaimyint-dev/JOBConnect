export interface JobCompany {
    id: number;
    name: string;
    logo: string | null;
}

export interface JobCategory {
    id: number;
    name: string;
}

export interface Job {
    id: number;
    company_id: number;
    category_id: number | null;

    title: string;
    description: string;
    requirements: string;
    responsibilities: string | null;

    employment_type:
    | 'full_time'
    | 'part_time'
    | 'contract'
    | 'internship';

    workplace_type:
    | 'onsite'
    | 'remote'
    | 'hybrid';

    location: string | null;
    salary_min: number | null;
    salary_max: number | null;
    vacancies: number;
    deadline: string | null;

    status:
    | 'draft'
    | 'pending'
    | 'published'
    | 'rejected'
    | 'closed';

    company?: JobCompany;
    category?: JobCategory;

    created_at: string;
    updated_at: string;
}

export interface JobListParams {
    page?: number;
    search?: string;
    category_id?: number;
    employment_type?: Job['employment_type'];
    workplace_type?: Job['workplace_type'];
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