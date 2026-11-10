export interface CompanyForm {
    name: string;
    tagline: string;
    industry: string;
    website: string;
    company_email: string;
    phone: string;
    description: string;
    address: string;
    city: string;
    foundedYear: number | null;
    employeeCount: string;
}

export interface GalleryImage {
    file: File;
    preview: string;
}
