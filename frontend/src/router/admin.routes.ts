import type { RouteRecordRaw } from "vue-router";

import AdminLayout from "@/layouts/AdminLayout.vue";
import Dashboard from "@/features/admin/pages/Dashboard.vue";
import CategoriesPage from "@/features/admin/pages/CategoriesPage.vue";

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
                path: "categories",
                name: "AdminCategories",
                component: CategoriesPage,
            },
        ],
    },
];
