
export type ResumeVisibility = "private" | "public";

export interface Resume {
    id: number;
    title: string;
    file_type: string;
    is_default: boolean;
    visibility: ResumeVisibility;
    created_at: string;
    updated_at: string;
}

export interface CreateResumeData {
    title: string;
    file: File;
    is_default?: boolean;
    visibility?: ResumeVisibility;
}

export interface UpdateResumeData {
    title?: string;
    file?: File;
    is_default?: boolean;
    visibility?: ResumeVisibility;
}
