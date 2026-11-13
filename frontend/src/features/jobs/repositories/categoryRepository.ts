import api from "@/services/api";

export const categoryService = {
    getCategories() {
        return api.get("/employer/job-categories");
    },
};
