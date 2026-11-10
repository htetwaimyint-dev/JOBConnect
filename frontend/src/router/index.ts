import { createRouter, createWebHistory } from "vue-router";
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
        component: EmployeeLayout,
        children: [
            {
                path: "dashboard",
                name: "EmployerDashboard",
                component: () =>
                    import("@/features/employer/pages/Dashboard.vue"),
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
