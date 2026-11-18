import type { Education } from "./education.types";
import type { Experience } from "./experience.types";
import type { Skill } from "./skill.types";
import type { Resume } from "./resume.types";

// Job Seeker Profile Response
export interface JobSeekerProfile {
    id: number;
    name?: string;

    profile_photo: string | null;
    phone: string | null;
    date_of_birth: string | null;
    gender: string | null;
    address: string | null;
    city: string | null;
    professional_title: string | null;
    bio: string | null;
    expected_salary: number | null;
    experience_years: number | null;
    linkedin_url: string | null;
    portfolio_url: string | null;
    created_at: string | null;
    updated_at: string | null;

    // Education records
    educations?: Education[];

    // Experience records
    experiences?: Experience[];

    // Skills
    skills?: Skill[];

    // Public resumes returned by Employer Profile API
    resumes?: Resume[];
}

// Data for updating Job Seeker Profile
export interface UpdateProfileData {
    profile_photo?: File | null;
    phone?: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    address?: string | null;
    city?: string | null;
    professional_title?: string | null;
    bio?: string | null;
    expected_salary?: number | null;
    experience_years?: number | null;
    linkedin_url?: string | null;
    portfolio_url?: string | null;
}
