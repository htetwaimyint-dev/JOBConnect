
export type ApplicationStatus =
    | "pending"
    | "reviewed"
    | "shortlisted"
    | "interview"
    | "accepted"
    | "rejected"
    | "withdrawn";

export interface ApplicationCompany {
    id: number;
    name: string;
    logo: string | null;
}

export interface ApplicationJob {
    id: number;
    title: string;
    company: ApplicationCompany;
}

export interface ApplicationResume {
    id: number;
    title: string;
    file_type: string;
}

export interface ApplicationQuestion {
    id: number | null;
    question: string | null;
    is_required: boolean | null;
}

export interface ApplicationAnswer {
    id: number;
    question: ApplicationQuestion;
    answer: string;
}

export interface Application {
    id: number;
    applicant_name: string | null;
    applicant_phone: string | null;
    status: ApplicationStatus;
    applied_at: string;
    job: ApplicationJob;
    resume: ApplicationResume | null;
    answers?: ApplicationAnswer[];
    created_at: string;
}

export interface ApplicationPagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface ApplicationListResponse {
    success: boolean;
    message: string;
    data: Application[];
    meta: ApplicationPagination;
}

export interface ApplicationDetailResponse {
    success: boolean;
    message: string;
    data: Application;
}

export interface JobApplicationQuestion {
    id: number;
    question: string;
    is_required: boolean;
    sort_order: number;
}

export interface SubmitApplicationData {
    job_id: number;
    applicant_name: string;
    applicant_phone: string;
    resume_id: number;
    answers: {
        job_question_id: number;
        answer: string;
    }[];
}