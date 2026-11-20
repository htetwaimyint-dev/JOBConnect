import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import { companyRepository } from "@/features/employer/repositories/companyRepository";
import { useCompanyProfileStore } from "../../../stores/company-profile.store";
import { computed, watch } from "vue";
import { useAuthStore } from "@/stores/auth.store";

export function useCompanyProfile() {
    const store = useCompanyProfileStore();
    const authStore = useAuthStore();
    const queryClient = useQueryClient();

    const userId = computed(() => authStore.user?.id || "guest");

    const { data, isPending, isError, error, refetch } = useQuery({
        queryKey: ["company", "profile", userId],
        queryFn: () => companyRepository.getCompanyProfileForForm(),
        retry: 1,
    });

    // Populate the form when the company profile is loaded.
    watch(
        () => data.value,
        (company) => {
            if (company) {
                store.populate(company);
            } else {
                store.reset();
            }
        },
        { immediate: true },
    );

    watch(
        () => isError.value,
        (hasError) => {
            if (hasError) {
                store.reset(); //
            }
        },
    );

    const mutation = useMutation({
        mutationFn: (formData: FormData) => {
            return store.isEditMode
                ? companyRepository.updateCompanyProfile(formData)
                : companyRepository.createCompanyProfile(formData);
        },

        onSuccess: async (company) => {
            store.populate(company);
            store.successMessage = "Company profile saved successfully.";
            store.errorMessage = "";
            store.clearErrors();

            // Update the cached profile immediately.
            queryClient.setQueryData(["company", "profile", userId.value], company);

            await queryClient.invalidateQueries({
                queryKey: ["company", "profile", userId.value],
            });
        },

        onError: (error: any) => {
            const response = error?.response?.data;

            store.errorMessage =
                response?.message || "Unable to save company profile.";

            if (response?.errors) {
                store.setErrors(response.errors);
            } else {
                store.clearErrors();
            }
        },
    });

    function submitForm() {
        store.errorMessage = "";
        store.successMessage = "";
        store.clearErrors();

        if (mutation.isPending.value) {
            return;
        }

        if (!store.form.name.trim()) {
            store.setErrors({
                name: ["The company name is required."],
            });
            return;
        }

        const formData = store.buildFormData();

        for (const [key, value] of formData.entries()) {
            console.log(
                key,
                value instanceof File ? `File: ${value.name}` : value,
            );
        }

        mutation.mutate(formData);
    }

    return {
        data,
        isPending,
        isError,
        error,
        refetch,
        mutation,
        submitForm,
    };
}
