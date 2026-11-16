import { useQuery } from "@tanstack/vue-query";

import { categoryRepository } from "../repositories/categoryRepository";

export function useCategories() {
    const getPublicCategories = () => {
        return useQuery({
            queryKey: ["public", "categories"],

            queryFn: async () => {
                const response = await categoryRepository.getPublicCategories();

                return response.data;
            },
        });
    };

    return {
        getPublicCategories,
    };
}
