export interface Company {
    id: number;
    name: string;
    slug: string;
    logo: string;
    coverImage: string;
    tagline: string;
    description: string;
    industry: string;
    location: string;
    website: string;
    foundedYear: number;
    employeeCount: string;
    openJobsCount: number;
    isVerified: boolean;
    benefits: Benefit[];
    gallery: string[];
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
