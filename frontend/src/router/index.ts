import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
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
        if (savedPosition) {
            return savedPosition;
        }

        return { top: 0, left: 0 };
    },
});

// Get home page according to user role
const getRoleHome = (role?: string): string => {
    switch (role) {
        case "admin":
            return "/admin/dashboard";

        case "employer":
            return "/employer/dashboard";

        case "job_seeker":
        default:
            return "/";
    }
};

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    // Initialize authentication
    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    // 1. Protect routes requiring authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return "/login";
    }

    // 2. Redirect authenticated users away from guest-only pages
    if (to.meta.guestOnly && authStore.isAuthenticated) {
        const redirectPath = getRoleHome(authStore.user?.role);

        if (to.path !== redirectPath) {
            return redirectPath;
        }
    }

    // 3. Prevent access to routes belonging to other roles
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        const redirectPath = getRoleHome(authStore.user?.role);

        if (to.path !== redirectPath) {
            return redirectPath;
        }
    }

    // 4. Redirect Admin and Employer from Home to their dashboards
    if (to.path === "/") {
        if (authStore.user?.role === "admin") {
            return "/admin/dashboard";
        }

        if (authStore.user?.role === "employer") {
            return "/employer/dashboard";
        }
    }

    return true;
});

export default router;