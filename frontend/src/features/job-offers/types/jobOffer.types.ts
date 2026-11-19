export type JobOfferStatus =
    | "pending"
    | "accepted"
    | "rejected"
    | "expired"
    | "withdrawn";

export type EmploymentType =
    | "full_time"
    | "part_time"
    | "contract"
    | "internship";

export type WorkplaceType = "onsite" | "remote" | "hybrid";

/** A published job the employer can attach to a direct offer. */
export interface EmployerOfferJob {
    id: number;
    title: string;
    employment_type: EmploymentType;
    workplace_type: WorkplaceType;
    country: string | null;
    city: string | null;
    salary_min?: number | string | null;
    salary_max?: number | string | null;
    salary_type?: "fixed" | "negotiable";
}

/** Fields available from job_seeker_profiles plus the related users record. */
export interface JobOfferCandidateProfile {
    profile_photo: string | null;
    phone: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    address: string | null;
    city: string | null;
    professional_title: string | null;
    bio: string | null;
    expected_salary: number | string | null;
    experience_years: number | string | null;
    linkedin_url: string | null;
    portfolio_url: string | null;
}

export interface JobOfferCandidate {
    id: number;
    name: string;
    email?: string | null;
    profile: JobOfferCandidateProfile | null;

    skills: CandidateSkill[];
    experiences: CandidateExperience[];
}

export interface JobOfferCompany {
    id: number;
    name: string;
    logo: string | null;
}

export interface JobOffer {
    id: number;
    job_id: number;
    job_seeker_id: number;
    company_id: number;
    salary_offered: number | string | null;
    offer_details: string | null;
    expires_at: string | null;
    status: JobOfferStatus;
    responded_at: string | null;
    created_at: string;
    updated_at: string;
    job?: EmployerOfferJob;
    company?: JobOfferCompany;
    job_seeker?: JobOfferCandidate;
}

/** Matches the columns currently available in job_offers. */
export interface CreateJobOfferPayload {
    job_seeker_id: number;
    job_id: number;
    salary_offered: number;
    offer_details: string | null;
    expires_at: string;
}

export interface JobOfferResponsePayload {
    status: "accepted" | "rejected";
}

export type JobOfferFilter = "all" | JobOfferStatus;

export interface CandidateSkill {
    id: number;
    name: string;
    skill_level: string;
}

export interface CandidateExperience {
    id: number;
    job_title: string;
    company_name: string;
    employment_type: string | null;
    start_date: string;
    end_date: string | null;
    is_current: boolean;
    description: string | null;
}
