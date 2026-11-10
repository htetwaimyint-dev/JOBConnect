import type { User } from "@/types/user.types";
export interface LoginData {
  email: string;
  password: string;
  remember: boolean;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  role: "job_seeker" | "employer";
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
}
