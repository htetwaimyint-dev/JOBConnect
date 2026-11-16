import { useQuery } from "@tanstack/vue-query";
import { categoryRepository } from "../repositories/categoryRepository";

export interface JobCategory {
    id: number;
    name: string;
}

export const categoryKeys = {
    all: ["job-categories"] as const,
    list: (filters?: { search?: string }) =>
        [...categoryKeys.all, "list", filters] as const,
};

export function useJobCategories(search?: string) {
    return useQuery({
        queryKey: categoryKeys.list({ search }),

        queryFn: async (): Promise<JobCategory[]> => {
            const response = await categoryRepository.getCategories(search);
            return response.data.data;
        },

        staleTime: 1000 * 60 * 10,
    });
}
