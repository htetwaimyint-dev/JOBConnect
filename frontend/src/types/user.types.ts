export type UserRole = "job_seeker" | "admin" | "employer";
export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
}
