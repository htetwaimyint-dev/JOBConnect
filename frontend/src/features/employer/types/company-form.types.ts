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
    foundedYear: string | number;
    employeeCount: string;
    city: string;
    address: string;
    // Replaced plain string description with structured sections array
    sections: CompanyOverviewSection[];
}

export interface GalleryImage {
    file: File;
    preview: string;
}
