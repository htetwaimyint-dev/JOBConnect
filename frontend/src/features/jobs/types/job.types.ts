export interface JobCompany {
  id: number;
  name: string;
  logo: string | null;
}

export interface JobCategory {
  id: number;
  name: string;
  logo: string | null;
}

export type EmploymentType =
  | "full_time"
  | "part_time"
  | "contract"
  | "internship";

export type WorkplaceType = "onsite" | "remote" | "hybrid";

export type SalaryType = "fixed" | "negotiable";

export type SubmissionStatus = "draft" | "pending";

export type JobStatus =
  | "draft"
  | "pending"
  | "published"
  | "rejected"
  | "closed";

export interface Job {
  id: number;
  company_id: number;

  company: JobCompany;
  category: JobCategory | null;

  title: string;
  description: string;
  requirements: string;
  responsibilities: string | null;

  employment_type: EmploymentType;
  workplace_type: WorkplaceType;

  country: string | null;
  city: string | null;

  salary_type: SalaryType;
  salary_min: number | null;
  salary_max: number | null;

  vacancies: number;
  gender: "any" | "male" | "female";
  deadline: string | null;

  status: JobStatus;
  rejection_reason?: string | null;

  created_at: string;
  updated_at: string;
  job_questions: JobApplicationQuestionForm[];
}

export interface JobFormData {
  title: string;
  category_id: number | null;
  deadline: string;
  employment_type: EmploymentType;
  vacancies: number;
  gender: "any" | "male" | "female";
  workplace_type: WorkplaceType;
  country: string;
  city: string;
  salary_type: SalaryType;
  salary_min: number | undefined;
  salary_max: number | undefined;
  description: string;
  requirements: string;
  responsibilities: string;
  submission_status: SubmissionStatus;
  job_questions: JobApplicationQuestionForm[];
}

export type JobDetailMode = "preview" | "public" | "admin" | "employer";

export type JobFormField = keyof JobFormData;

export interface JobListParams {
  page?: number;
  search?: string;
  category_id?: number;
  company_id?: number;
  employment_type?: EmploymentType;
  workplace_type?: WorkplaceType;
  city?: string;
  country?: string;
  status?: JobStatus | "all";
}

export interface JobListResponse {
  data: Job[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface JobApplicationQuestion {
  id?: number;
  question: string;
  is_required: boolean;
  sort_order: number;
}

export interface JobApplicationQuestionForm {
  id?: number;
  question: string;
  is_required: boolean;
  sort_order: number;
}

export interface JobStats {
  total: number;
  draft: number;
  pending: number;
  published: number;
  rejected: number;
  closed: number;
}
