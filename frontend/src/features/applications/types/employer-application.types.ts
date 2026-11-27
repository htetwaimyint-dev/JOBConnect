
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
    id: number;
    job_seeker_id: number;
    profile_photo: string | null;
    phone: string | null;
    date_of_birth: string | null;
    gender: string | null;
    address: string | null;
    city: string | null;
    professional_title: string | null;
    bio: string | null;
    expected_salary: string | null;
    experience_years: string | null;
    linkedin_url: string | null;
    portfolio_url: string | null;
}

// ==========================================
// Job Seeker Skill
// ==========================================

export interface EmployerJobSeekerSkill {
    id: number;
    name: string;
    skill_level: string;
}

// ==========================================
// Job Seeker Education
// ==========================================

export interface EmployerJobSeekerEducation {
    id: number;
    institution: string;
    degree: string | null;
    field_of_study: string | null;
    start_date: string | null;
    end_date: string | null;
    description: string | null;
}

// ==========================================
// Job Seeker Experience
// ==========================================

export interface EmployerJobSeekerExperience {
    id: number;
    job_title: string;
    company_name: string;
    employment_type: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
}

// ==========================================
// Job Seeker
// ==========================================

export interface EmployerJobSeeker {
    id: number;
    name: string;
    email: string;
    profile?: EmployerJobSeekerProfile | null;
    skills?: EmployerJobSeekerSkill[] | null;
    educations?: EmployerJobSeekerEducation[] | null;
    experiences?: EmployerJobSeekerExperience[] | null;
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
    analysis_id?: number;
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
// Batch AI Evaluation
// ==========================================

// Applicant result returned by the batch progress API.
export interface EmployerBatchAiEvaluationItem {
    application_id: number;
    applicant_id: number;
    applicant_name: string;
    professional_title: string | null;
    city: string | null;
    profile_photo: string | null;
    analysis_id: number;
    match_score: number;
    recommendation: "strong_match" | "moderate_match" | "low_match";
    matched_skills: string[];
    missing_skills: string[];
    strengths: string[];
    weaknesses: string[];
    reasoning: string;
}

// Response when starting a batch.
export interface EmployerBatchAiEvaluationResponse {
    success: boolean;
    message: string;
    data: {
        batch_id: number;
        job_id: number;
        status: "queued" | "processing" | "completed";
        total_applicants: number;
        created: boolean;
    };
}

// Response when checking batch progress.
export interface EmployerBatchAiEvaluationProgressResponse {
    success: boolean;
    message: string;
    data: {
        batch_id: number;
        job_id: number;
        status:
        | "queued"
        | "processing"
        | "completed"
        | "completed_with_errors"
        | "failed";
        is_final: boolean;
        total_applicants: number;
        completed_count: number;
        failed_count: number;
        processing_count: number;
        queued_count: number;
        progress_percentage: number;
        started_at: string | null;
        completed_at: string | null;
        completed_applicants: EmployerBatchAiEvaluationItem[];
        top_applicants: EmployerBatchAiEvaluationItem[];
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
    resume: EmployerApplicationResume;

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
