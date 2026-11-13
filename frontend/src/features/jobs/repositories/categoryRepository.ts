import api from "@/services/api";

export const categoryService = {
    getJobCategories() {
        return api.get("/employer/job-categories");
    },
};
