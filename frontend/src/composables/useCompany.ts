import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import { companyService } from "@/services/company.service";
import { useCompanyProfileStore } from "../stores/company-profile.store";
import type { Company } from "@/features/employer/types/company.types";
import { watch } from "vue";

export function useCompanyProfile() {
    const store = useCompanyProfileStore();
    const queryClient = useQueryClient();

    const { data, isPending, isError, error, refetch } = useQuery({
        queryKey: ["company", "profile"],
        queryFn: () => companyService.getCompanyProfile(),
        retry: 1,
    });

    watch(
        () => data.value,
        (company: Company | null | undefined) => {
            if (company !== undefined) {
                store.populate(company as Company | null);
            }
        },
        { immediate: true },
    );

    const mutation = useMutation({
        mutationFn: (data: FormData) =>
            store.isEditMode
                ? companyService.updateCompanyProfile(data)
                : companyService.createCompanyProfile(data),

        onSuccess: async (company) => {
            store.populate(company);
            store.successMessage = "Company profile saved successfully.";

            await queryClient.invalidateQueries({
                queryKey: ["company", "profile"],
            });
        },

        onError: (error: any) => {
            store.errorMessage =
                error?.response?.data?.message ||
                "Unable to save company profile.";
        },
    });

    function submitForm() {
        store.errorMessage = "";
        store.successMessage = "";

        if (!store.form.name.trim()) {
            store.errorMessage = "Company name is required.";
            return;
        }

        mutation.mutate(store.buildFormData());
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
