import { createRouter, createWebHistory } from "vue-router";
import AuthLayout from "@/layouts/AuthLayout.vue";
import LoginPage from "@/features/auth/pages/LoginPage.vue";
import RegisterPage from "@/features/auth/pages/RegisterPage.vue";
import DashboardPage from "@/features/auth/pages/DashboardPage(test).vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployeeLayout from "@/layouts/EmployerLayout.vue";
import AdminLayout from "@/layouts/AdminLayout.vue";

const routes = [
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
        children: [
            {
                path: "dashboard",
                name: "JobSeekerDashboard",
                component: () =>
                    import("@/features/job-seeker/pages/Dashboard.vue"),
            },
            // {
            //     path: "settings",
            //     name: "JobSeekerSettings",
            //     component: () =>
            //         import("@/features/job-seeker/pages/JobSeekerSettingsPage.vue"),
            // },
        ],
    },

    // Employee Layout
    {
        path: "/employer",
        children: [
            {
                path: "dashboard",
                name: "EmployerDashboard",
                component: () =>
                    import("@/features/employer/pages/Dashboard.vue"),
            },
            {
                path: "profile",
                name: "CompanyProfilePage",
                component: CompanyProfilePage,
            },
            {
                path: "profile/create",
                name: "CompanyProfileForm",
                component: CompanyProfileForm,
            },
        ],
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            {
                path: "dashboard",
                name: "AdminDashboard",
                component: () => import("@/features/admin/pages/Dashboard.vue"),
            },
        ],
    },
    {
        path: "/",
        component: AuthLayout,
        children: [
            {
                path: "login",
                component: LoginPage,
            },
            {
                path: "register",
                component: RegisterPage,
            },
        ],
    },
    {
        path: "/dashboard",
        component: DashboardPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/company/profile",
        component: CompanyProfilePage,
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("token");

    if (to.meta.requiresAuth && !token) {
        return "/login";
    }

    if ((to.path === "/login" || to.path === "/register") && token) {
        return "/dashboard";
    }
});

export default router;
