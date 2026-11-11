//comeback from job-seeker-profile-types
export interface JobSeekerProfile {
    id: number;
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
}

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
