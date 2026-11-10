
import { createRouter, createWebHistory } from "vue-router";

// Demo Components
import Button from "@/components/common/demo/Button.vue";
import Checkbox from "@/components/common/demo/Checkbox.vue";
import Input from "@/components/common/demo/Input.vue";
import PasswordInput from "@/components/common/demo/PasswordInput.vue";
import Select from "@/components/common/demo/Select.vue";
import Alert from "@/components/common/demo/Alert.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import LoginPage from "@/features/auth/pages/LoginPage.vue";
import RegisterPage from "@/features/auth/pages/RegisterPage.vue";
import DashboardPage from "@/features/auth/pages/DashboardPage(test).vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";

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
            {
                path: "settings",
                name: "JobSeekerSettings",
                component: () =>
                    import("@/features/job-seeker/pages/JobSeekerSettingsPage.vue"),
            },
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
            component: () =>
                import("@/features/admin/pages/Dashboard.vue"),
            },
        ],
    },

    // Demo Routes
    {
        path: "/button",
        component: Button,
    },
    {
        path: "/checkbox",
        component: Checkbox,
    },
    {
        path: "/input",
        component: Input,
    },
    {
        path: "/passwordinput",
        component: PasswordInput,
    },
    {
        path: "/select",
        component: Select,
    },
    {
        path: "/alert",
        component: Alert,
    },

    {
        path: "/",
        redirect: "/login",
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
