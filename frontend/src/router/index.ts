import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";

// Demo Components
import { useAuthStore } from "@/stores/auth.store.ts";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployeeLayout from "@/layouts/EmployerLayout.vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";

// Dashboard Components
import JobSeekerDashboard from "@/features/job-seeker/pages/Dashboard.vue";
import AdminDashboard from "@/features/admin/pages/Dashboard.vue";
import EmployerDashboard from "@/features/employer/pages/Dashboard.vue";

// Import Route Files
import { authRoutes } from "./auth.routes";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";

// Combine All Routes
const routes: RouteRecordRaw[] = [
    //Auth Routes
    ...authRoutes,

    // Public Website Layout
    {
        path: "/",
        component: PublicLayout,
        children: [
            // Add your public website pages here later
        ],
    },

    // Job Seeker Layout
    {
        path: "/jobseeker",
        component: JobSeekerLayout,
        meta: {
            requiresAuth: true,
            role: "job_seeker",
        },
        children: [
            {
                path: "dashboard",
                name: "JobSeekerDashboard",
                component: JobSeekerDashboard,
            },
        ],
    },

    //admin
    {
        path: "/admin",
        component: AdminLayout,
        meta: {
            requiresAuth: true,
            role: "admin",
        },
        children: [
            {
                path: "dashboard",
                name: "AdminDashboard",
                component: AdminDashboard,
            },
        ],
    },

    // Employee Layout
    {
        path: "/employer",
        //component: EmployeeLayout,
        meta: {
            requiresAuth: true,
            role: "employer",
        },
        children: [
            {
                path: "dashboard",
                name: "EmployerDashboard",
                component: EmployerDashboard,
            },
            {
                path: "profile/create",
                name: "EmployerProfileForm",
                component: CompanyProfileForm,
            },
            {
                path: "profile",
                name: "CompanyProfilePage",
                component: CompanyProfilePage,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    // Check Authentication
    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    // Check Authorization
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    // Prevent authenticated users from accessing login
    if (to.meta.guestOnly && authStore.token) {
        return "/";
    }
    return true;
});

export default router;
