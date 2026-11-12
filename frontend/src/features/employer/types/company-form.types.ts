export interface CompanyOverviewSection {
    id: string;
    title: string;
    content: string;
    icon?: "lightbulb" | "briefcase" | "star" | "building" | "target" | "users";
}

export interface CompanyForm {
    name: string;
    tagline: string;
    industry: string;
    website: string;
    company_email: string;
    phone: string;
    address: string;
    city: string;
    foundedYear: number;
    employeeCount: string;
    // Client-side dynamic sections array
    sections: CompanyOverviewSection[];
}

export interface GalleryImage {
    file: File;
    preview: string;
}
