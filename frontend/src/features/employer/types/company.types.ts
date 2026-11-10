export interface Company {
    id: number;
    employer_id: number;
    name: string;
    logo: string | null;
    description: string | null;
    industry: string | null;
    website: string | null;
    company_email: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;

    verification_status: "pending" | "verified" | "rejected";
    verified_at: string | null;

    created_at: string | null;
    updated_at: string | null;

    // Future fields
    coverImage?: string | null;
    tagline?: string | null;
    foundedYear?: number | null;
    employeeCount?: string | null;
    openJobsCount?: number;
    benefits?: Benefit[];
    gallery?: string[];
}

export interface Benefit {
    id: number;
    title: string;
    description: string;
    icon: string;
}

export interface Job {
    id: number;
    title: string;
    department: string;
    location: string;
    employmentType: string;
    experienceLevel: string;
    postedAt: string;
}
