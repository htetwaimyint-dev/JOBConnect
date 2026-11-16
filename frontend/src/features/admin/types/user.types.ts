export interface Company {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  email_verified_at: string | null;
  created_at: string;
  company: string | null;
  index?: number;
}

export interface JobSeeker {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  email_verified_at: string | null;
  created_at: string;
  profile: string | null;
  index?: number;
}

export interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}
