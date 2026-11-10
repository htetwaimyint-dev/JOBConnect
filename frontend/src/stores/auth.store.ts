import { defineStore } from "pinia";
import { ref, computed } from "vue";

import { authRepository } from "@/features/auth/repositories/authRepository";

import type { LoginData, RegisterData } from "@/features/auth/types/auth.types";

import type { User } from "@/types/user.types";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<User | null>(null);

    const token = ref<string | null>(localStorage.getItem("token"));

    const isAuthenticated = computed(() => !!token.value);

    // Login
    const login = async (data: LoginData) => {
        const response = await authRepository.login(data);

        user.value = response.data.user;
        token.value = response.data.token;

        localStorage.setItem("token", token.value);

        return response.data;
    };

    // Register
    const register = async (data: RegisterData) => {
        const response = await authRepository.register(data);

        user.value = response.data.user;
        token.value = response.data.token;

        localStorage.setItem("token", response.data.token);

        return response.data;
    };

    // Logout
    const logout = async () => {
        try {
            await authRepository.logout();
        } finally {
            localStorage.removeItem("token");

            token.value = null;
            user.value = null;
        }
    };

    return {
        user,
        token,
        isAuthenticated,
        login,
        register,
        logout,
    };
});
