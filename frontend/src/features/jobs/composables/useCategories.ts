
import { useQuery } from "@tanstack/vue-query";

import { categoryService } from "../repositories/categoryRepository";

export function useCategories() {
    const getPublicCategories = () => {
        return useQuery({
            queryKey: ["public", "categories"],

            queryFn: async () => {
                const response =
                    await categoryService.getPublicCategories();

                return response.data;
            },
        });
    };

    return {
        getPublicCategories,
    };
}
