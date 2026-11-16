import api from "@/services/api";

export const categoryRepository = {
    getCategories(search?: string) {
        return api.get("/employer/job-categories", { params: { search } });
    },
    // Get categories for employer job form

    getJobCategories() {
        return api.get("/employer/job-categories");
    },

    // Get categories for public HomePage
    getPublicCategories() {
        return api.get("/public/categories");
    },
};
