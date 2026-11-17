import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth.store";

import { authRoutes } from "./auth.routes";
import { adminRoutes } from "./admin.routes";
import { employerRoutes } from "./employer.routes";
import { jobseekerRoutes } from "./jobseeker.routes";
import { publicRoutes } from "./public.routes";

const routes: RouteRecordRaw[] = [
    ...publicRoutes,
    ...authRoutes,
    ...adminRoutes,
    ...employerRoutes,
    ...jobseekerRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,

    scrollBehavior(to, from, savedPosition) {
        // If the browser's back/forward button was used, keep the saved scroll position
        if (savedPosition) {
            return savedPosition;
        } else {
            // Always scroll to the top of the page on a new navigation
            return { top: 0, left: 0 };
        }
    },
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    // Redirect authenticated users away from guest-only pages
    if (to.meta.guestOnly && authStore.isAuthenticated) {
        switch (authStore.user?.role) {
            case "job_seeker":
                return "/jobseeker/dashboard";

            case "employer":
                return "/employer/dashboard";

            case "admin":
                return "/admin/dashboard";

            default:
                return "/";
        }
    }

    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    return true;
});

export default router;
