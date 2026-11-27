export type UserStatus = "active" | "suspended";
 
export interface BaseUser {
  id: number;
  name: string;
  email: string;
  role: "Employer" | "Job Seeker";
  logo: string;
  isDeleted: boolean;
  joinedDate: string;
  status: UserStatus;
  deleted_at?: string | null;
}
 
export interface EmployerCompany {
  id: number;
  name: string;
  logo: string | null;
  jobs_count?: number;
}
 
export interface EmployerProfile {
  profile_photo: string | null;
}
 
export interface Employer extends BaseUser {
  role: "Employer";
  company: EmployerCompany | null;
  profile?: EmployerProfile | null;
  jobs_count?: number;
  companyLogo: string;
  jobCount: number;
}
 
export interface JobSeekerProfile {
  profile_photo: string | null;
  professional_title?: string | null;
}
 
export interface JobSeeker extends BaseUser {
  role: "Job Seeker";
  profile?: JobSeekerProfile | null;
  resume_count?: number;
  profileTitle: string;
  profileUrl?: string;
}
 
export type UserManagementStatusFilter =
  | "All Statuses"
  | "Active"
  | "Suspended";
 
