import api from "@/services/api";

import type {
    LoginData,
    RegisterData,
    AuthResponse,
} from "../types/auth.types";

export const authRepository = {
    login(data: LoginData) {
        return api.post<AuthResponse>("/login", data);
    },

    register(data: RegisterData) {
        return api.post<AuthResponse>("/register", data);
    },

    logout() {
        return api.post("/logout");
    },

    getCurrentUser() {
        return api.get("/user");
    },
};
