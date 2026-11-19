export interface Job {
  id: number;
  company_id: number;
  category_id: number;
  title: string;
  description: string;
  employment_type: string; // e.g., 'full_time'
  workplace_type: string; // e.g., 'onsite'
  country: string;
  city: string;
  salary_min: number | null;
  salary_max: number | null;
  salary_type: string;
  vacancies: number;
  deadline: string;
  status: string;
  created_at: string;
}

export interface JobFilters {
  keyword?: string;
  job_type?: string;
  location?: string;
  page?: number;
}
