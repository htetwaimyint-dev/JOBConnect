
import type {
    ApplicationStatus,
    ApplicationPagination,
} from "./application.types";

export type {
    ApplicationStatus,
    ApplicationPagination,
};

// ==========================================
// Employer Application Job
// ==========================================

export interface EmployerApplicationJob {
    id: number | null;
    title: string | null;
    company: {
        id: number | null;
        name: string | null;
        logo: string | null;
    };
}

// ==========================================
// Employer Application Resume
// ==========================================

export interface EmployerApplicationResume {
    id: number | null;
    title: string | null;
    file_type: string | null;
}

// ==========================================
// Employer Application Answers
// ==========================================

export interface EmployerApplicationAnswer {
    id: number;
    question: {
        id: number | null;
        question: string | null;
        is_required: boolean | null;
    };
    answer: string;
}

// ==========================================
// Job Seeker Profile
// ==========================================

export interface EmployerJobSeekerProfile {
    [key: string]: unknown;
}

// ==========================================
// Job Seeker
// ==========================================

export interface EmployerJobSeeker {
    id: number;
    name: string;
    email: string;
    profile?: EmployerJobSeekerProfile | null;
    skills?: Record<string, unknown>[] | null;
    educations?: Record<string, unknown>[] | null;
    experiences?: Record<string, unknown>[] | null;
}

// ==========================================
// Application Status History
// ==========================================

export interface EmployerApplicationStatusHistory {
    id: number;
    old_status: string | null;
    new_status: string;
    note: string | null;
    changed_by_company_id: number | null;
    created_at: string;
}

// ==========================================
// AI Evaluation Result
// ==========================================

export interface EmployerAiEvaluationResult {
    match_score: number;
    matched_skills: string[];
    missing_skills: string[];
    relevant_experience: string[];
    strengths: string[];
    weaknesses: string[];
    recommendation: "strong_match" | "moderate_match" | "low_match";
    reasoning: string;
}

// ==========================================
// AI Evaluation API Response
// ==========================================

export interface EmployerAiEvaluationResponse {
    success: boolean;
    message: string;
    data: {
        application_id: number;
        analysis_id: number;
        match_score: number;
        recommendation: string;
        result: EmployerAiEvaluationResult;
    };
}

// ==========================================
// Employer Application
// ==========================================

export interface EmployerApplication {
    id: number;
    applicant_name: string | null;
    applicant_phone: string | null;
    status: ApplicationStatus;
    applied_at: string;

    job: EmployerApplicationJob;
    resume: EmployerApplicationResume | null;

    job_seeker?: EmployerJobSeeker | null;
    answers?: EmployerApplicationAnswer[];

    // AI evaluation result only (not the complete API response)
    ai_evaluation?: EmployerAiEvaluationResult | null;

    status_histories?: EmployerApplicationStatusHistory[];

    created_at: string;
}

// ==========================================
// Application List API Response
// ==========================================

export interface EmployerApplicationListResponse {
    success: boolean;
    message: string;
    data: EmployerApplication[];
    meta: ApplicationPagination;
}

// ==========================================
// Application Detail API Response
// ==========================================

export interface EmployerApplicationDetailResponse {
    success: boolean;
    message: string;
    data: EmployerApplication;
}

// ==========================================
// Application Query Parameters
// ==========================================

export interface EmployerApplicationParams {
    page?: number;
    perpage?: number;
    status?: ApplicationStatus;
    category_id?: number;
    search?: string;
}

// ==========================================
// Update Application Status
// ==========================================

export interface UpdateEmployerApplicationStatusData {
    new_status: Exclude<
        ApplicationStatus,
        "pending" | "withdrawn"
    >;
    note?: string;
}
