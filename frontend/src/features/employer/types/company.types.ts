import type { Job } from "@/features/jobs/types/job.types";
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
export interface CompanyJobItem extends Omit<
  Job,
  "company" | "category" | "job_questions" | "salary_min" | "salary_max"
> {
  category_id: number;
  description_plain_text: string | null;
  salary_min: number | string | null;
  salary_max: number | string | null;
  approved_by: number | null;
  approved_at: string | null;
  rejection_reason: string | null;
}
export interface CompanyJobsPagination {
  current_page: number;
  data: CompanyJobItem[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
  }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
}

export interface CompanyDetailResponse {
  status: boolean;
  company: Company;
  jobs: CompanyJobsPagination;
}
