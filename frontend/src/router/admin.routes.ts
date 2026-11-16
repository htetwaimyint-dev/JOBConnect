import type { RouteRecordRaw } from "vue-router";

import AdminLayout from "@/layouts/AdminLayout.vue";

import Dashboard from "@/features/admin/pages/Dashboard.vue";
import CompaniesPage from "@/features/admin/pages/CompaniesPage.vue";
import JobSeekersPage from "@/features/admin/pages/JobSeekersPage.vue";
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
        path: "companies",
        name: "AdminCompanies",
        component: CompaniesPage,
        meta: { title: "Companies" },
      },
      {
        path: "job-seekers",
        name: "AdminJobSeekers",
        component: JobSeekersPage,
        meta: { title: "Job Seekers" },
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
