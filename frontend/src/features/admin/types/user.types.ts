export type UserStatus = "active" | "suspended";
export interface BaseUser {
  id: number;
  name: string;
  email: string;
  role: "Employer" | "Job Seeker";
  logo: string;
  isDeleted: boolean;
  joinedDate: string;
}
export interface Employer extends BaseUser {
  role: "Employer";
  company: string;
  companyLogo: string;
  jobCount: number;
}
export interface JobSeeker extends BaseUser {
  role: "Job Seeker";
  profileTitle: string;
  profileUrl?: string;
}

export type UserManagementStatusFilter =
  | "All Statuses"
  | "Active"
  | "Suspended";
