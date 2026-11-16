import type { User } from "@/types/user.types";

export interface UpdateAccountProfileData {
    name: string;
    email: string;
}

export interface UpdateAccountPasswordData {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
}

export interface AccountResponse {
    message: string;
    success: boolean;
    user?: User;
    token?: string | null;
}