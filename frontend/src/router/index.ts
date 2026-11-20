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

    if (to.meta.guestOnly && authStore.isAuthenticated) {
        let redirectPath = "/";

        switch (authStore.user?.role) {
            case "employer":
                redirectPath = "/employer/dashboard";
                break;

            case "admin":
                redirectPath = "/admin/dashboard";
                break;

            default:
                redirectPath = "/";
                break;
        }

        if (to.path !== redirectPath) {
            return redirectPath;
        }
    }

    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        if (to.path !== "/") {
            return "/";
        }
    }

    if (
        to.path === "/" &&
        (authStore.user?.role === "employer" ||
            authStore.user?.role === "admin")
    ) {
        return "/employer/dashboard";
    }
    return true;
});

export default router;
