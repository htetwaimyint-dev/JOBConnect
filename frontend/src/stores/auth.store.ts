import { defineStore } from "pinia";
import { ref, computed } from "vue";

import { authRepository } from "@/features/auth/repositories/authRepository";
import { useUserStore } from "@/stores/user.store";

import type { LoginData, RegisterData } from "@/features/auth/types/auth.types";
import type { User } from "@/types/user.types";

import { getImageUrl } from "@/utils/image.utils";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<User | null>(null);
    const token = ref<string | null>(localStorage.getItem("token"));

    const isAuthenticated = computed(() => !!token.value);
    const initialized = ref<boolean>(false);

    const userStore = useUserStore();

    // Initialize Auth
    const initializeAuth = async () => {
        if (initialized.value) return;

        try {
            if (token.value) {
                const response = await authRepository.getCurrentUser();

                user.value = response.data.user;

                userStore.setProfilePhoto(
                    getImageUrl(user.value?.profile?.profile_photo ?? null)
                );
            }
        } catch (error) {
            localStorage.removeItem("token");

            token.value = null;
            user.value = null;

            userStore.clearProfilePhoto();
        } finally {
            initialized.value = true;
        }
    };

    // Login
    const login = async (data: LoginData) => {
        const response = await authRepository.login(data);

        token.value = response.data.token;
        localStorage.setItem("token", token.value);

        // Fetch complete authenticated user
        const userResponse = await authRepository.getCurrentUser();

        user.value = userResponse.data.user;

        userStore.setProfilePhoto(
            getImageUrl(user.value?.profile?.profile_photo ?? null)
        );

        return response.data;
    };

    // Register
    const register = async (data: RegisterData) => {
        const response = await authRepository.register(data);

        user.value = response.data.user;
        token.value = response.data.token;

        localStorage.setItem("token", response.data.token);

        userStore.setProfilePhoto(
            getImageUrl(user.value?.profile?.profile_photo ?? null)
        );

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

            userStore.clearProfilePhoto();
        }
    };

    return {
        user,
        token,
        isAuthenticated,
        initialized,
        login,
        register,
        logout,
        initializeAuth,
    };
});
