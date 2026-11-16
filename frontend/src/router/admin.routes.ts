import type { RouteRecordRaw } from "vue-router";

import AdminLayout from "@/layouts/AdminLayout.vue";

import Dashboard from "@/features/admin/pages/Dashboard.vue";
import UsersPage from "@/features/admin/pages/UsersPage.vue";
import CompanyApprovalPage from "@/features/admin/pages/CompanyApprovalPage.vue";
import JobApprovalPage from "@/features/admin/pages/JobApprovalPage.vue";
import CategoriesPage from "@/features/admin/pages/CategoriesPage.vue";
import ReportsPage from "@/features/admin/pages/ReportsPage.vue";
import AdminSettingsPage from "@/features/admin/pages/AdminSettingsPage.vue";

export const adminRoutes: RouteRecordRaw[] = [
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
                component: Dashboard,
            },
            {
                path: "users",
                name: "AdminUsers",
                component: UsersPage,
            },
            {
                path: "company-approvals",
                name: "AdminCompanyApproval",
                component: CompanyApprovalPage,
            },
            {
                path: "job-approvals",
                name: "AdminJobApproval",
                component: JobApprovalPage,
            },
            {
                path: "categories",
                name: "AdminCategories",
                component: CategoriesPage,
            },
            {
                path: "reports",
                name: "AdminReports",
                component: ReportsPage,
            },
            {
                path: "settings",
                name: "AdminSettings",
                component: AdminSettingsPage,
            },
        ],
    },
];
