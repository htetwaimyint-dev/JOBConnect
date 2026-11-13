
import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth.store";
import PublicLayout from "@/layouts/PublicLayout.vue";
import { authRoutes } from "./auth.routes";
import { adminRoutes } from "./admin.routes";
import { employerRoutes } from "./employer.routes";
import { jobseekerRoutes } from "./jobseeker.routes";

const routes: RouteRecordRaw[] = [
    ...authRoutes,
    ...adminRoutes,
    ...employerRoutes,
    ...jobseekerRoutes,
    // Public Layout
    {
        path: "/",
        component: PublicLayout,
        children: [],
    },
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

    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    if (to.path === "/login" && authStore.token) {
        return "/";
    }

    return true;
});

export default router;