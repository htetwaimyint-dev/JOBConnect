
import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";

import { useAuthStore } from "@/stores/auth.store";

import { authRoutes } from "./auth.routes";
import { publicRoutes } from "./public.routes";
import { jobSeekerRoutes } from "./job-seeker.routes";
import { employerRoutes } from "./employer.routes";
import { adminRoutes } from "./admin.routes";

const routes: RouteRecordRaw[] = [
    ...authRoutes,
    ...publicRoutes,
    ...jobSeekerRoutes,
    ...employerRoutes,
    ...adminRoutes,
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    // Authentication check
    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    // Role authorization check
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    return true;
});

export default router;
