import { ref } from "vue";

import applicationRepository from "../repositories/applicationRepository";

import type { Application } from "../types/application.types";

export function useApplicationDetail() {
    const application = ref<Application | null>(null);
    const loading = ref(false);
    const errorMessage = ref("");

    const fetchApplicationDetail = async (id: number) => {
        loading.value = true;
        errorMessage.value = "";
        application.value = null;

        try {
            const response =
                await applicationRepository.getApplicationById(id);

            application.value = response.data;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            errorMessage.value =
                apiError.response?.data?.message ??
                "Failed to load application details. Please try again.";
        } finally {
            loading.value = false;
        }
    };

    return {
        application,
        loading,
        errorMessage,
        fetchApplicationDetail,
    };
}