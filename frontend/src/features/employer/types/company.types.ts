export interface Company {
    id: number;
    employer_id: number;

    name: string;
    tagline: string | null;

    logo: string | null;
    cover_image: string | null;

    founded_year: number;
    employee_count: string | null;

    description: string | null;
    industry: string | null;
    website: string | null;
    company_email: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;

    gallery: string[];

    verification_status: "pending" | "verified" | "rejected";
    verified_at: string | null;

    created_at: string | null;
    updated_at: string | null;
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
