
import api from "@/services/api";

export const categoryService = {
    // Get categories for employer job form
    getJobCategories() {
        return api.get("/employer/job-categories");
    },

    // Get categories for public HomePage
    getPublicCategories() {
        return api.get("/public/categories");
    },
};
