export interface Education {
    id: number;
    institution: string;
    degree: string;
    field_of_study: string | null;
    start_date: string;
    end_date: string | null;
    description: string | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface CreateEducationData {
    institution: string;
    degree: string;
    field_of_study?: string | null;
    start_date: string;
    end_date?: string | null;
    description?: string | null;
}

export type UpdateEducationData = Partial<CreateEducationData>;