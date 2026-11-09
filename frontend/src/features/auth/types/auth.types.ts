import type { User } from "@/types/user.types";
export interface LoginData {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  role: "job_seeker" | "employer";
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
}
