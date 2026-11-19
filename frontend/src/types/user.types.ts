
export type UserRole = "job_seeker" | "admin" | "employer";
export interface JobSeekerProfile {
    profile_photo: string | null;
    phone?: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    address?: string | null;
    city?: string | null;
    professional_title?: string | null;
    bio?: string | null;
    expected_salary?: string | null;
    experience_years?: string | null;
    linkedin_url?: string | null;
    portfolio_url?: string | null;
}
export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    status?: string;
    email_verified_at?: string | null;
    created_at?: string;
    profile?: JobSeekerProfile | null;
}
