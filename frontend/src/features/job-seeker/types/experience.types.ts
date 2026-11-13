export interface Experience {
    id: number;
    job_title: string;
    company_name: string;
    employment_type: string | null;
    start_date: string | null;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface CreateExperienceData {
    job_title: string;
    company_name: string;
    employment_type?: string | null;
    start_date?: string | null;
    end_date?: string | null;
    is_current?: boolean;
    description?: string | null;
}

export type UpdateExperienceData = Partial<CreateExperienceData>;