export type InterviewType = "online" | "onsite" | "phone";

export type InterviewStatus =
    | "scheduled"
    | "completed"
    | "cancelled"
    | "rescheduled";

export interface Interviewer {
    id: number;
    name: string;
    position: string;
}

export interface InterviewCompany {
    id: number | null;
    name: string | null;
    logo: string | null;
}

export interface InterviewJob {
    id: number | null;
    title: string | null;
    company: InterviewCompany;
}

export interface InterviewApplication {
    id: number;
    applicant_name: string;
    applicant_phone: string | null;
    status: string;
    applied_at: string | null;
    job: InterviewJob;
    job_seeker?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export interface Interview {
    id: number;
    application_id: number;
    interview_type: InterviewType;
    scheduled_at: string;
    location: string | null;
    meeting_link: string | null;
    notes: string | null;
    status: InterviewStatus;
    interviewers: Interviewer[];
    application?: InterviewApplication | null;
    created_at: string;
    updated_at: string;
}

export interface InterviewFormData {
    application_id: number;
    interview_type: InterviewType;
    scheduled_at: string;
    location: string | null;
    meeting_link: string | null;
    notes: string | null;
    interviewer_ids: number[];
}

export interface InterviewFilters {
    search: string;
    status: InterviewStatus | "all";
    interview_type: InterviewType | "all";
    from?: string;
    to?: string;
}

export interface InterviewerFormData {
    name: string;
    position: string;
}

export interface JobseekerInterviewFilters {
    search: string;
    status: InterviewStatus | "all";
    interview_type: InterviewType | "all";
    from?: string;
    to?: string;
}
