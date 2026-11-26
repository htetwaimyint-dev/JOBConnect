
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

export interface EmployerBatchAiEvaluationItem {
    application_id: number;
    analysis_id: number;
    match_score: number;
    recommendation: "strong_match" | "moderate_match" | "low_match";
    result: EmployerAiEvaluationResult;
}

export interface EmployerBatchAiEvaluationResponse {
    success: boolean;
    message: string;
    data: {
        job_id: number;
        total_applicants: number;
        successful: number;
        failed: number;
        returned: number;
        evaluations: EmployerBatchAiEvaluationItem[];
        failures: unknown[];
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
