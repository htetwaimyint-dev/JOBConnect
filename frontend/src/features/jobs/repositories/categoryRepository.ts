import api from "@/services/api";

export const categoryRepository = {
    getCategories(search?: string) {
        return api.get("/employer/job-categories", { params: { search } });
    },
};
