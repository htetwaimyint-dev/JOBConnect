import type { RouteRecordRaw } from "vue-router";

import AdminLayout from "@/layouts/AdminLayout.vue";
import SettingsPage from "@/components/common/SettingsPage.vue";
import Dashboard from "@/features/admin/pages/Dashboard.vue";
import EmployerManagementPage from "@/features/admin/pages/EmployerManagementPage.vue";
import JobSeekerManagementPage from "@/features/admin/pages/JobSeekerManagementPage.vue";
import CompanyApprovalPage from "@/features/admin/pages/CompanyApprovalPage.vue";
import JobApprovalPage from "@/features/admin/pages/JobApprovalPage.vue";
import CategoriesPage from "@/features/admin/pages/CategoriesPage.vue";
import ReportsPage from "@/features/admin/pages/ReportsPage.vue";
import CompanyDetailPage from "@/features/job-seeker/pages/CompanyDetailPage.vue";
import JobSeekerDetailPage from "@/features/employer/pages/JobSeekerDetailPage.vue";

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
        component: EmployerManagementPage,
        meta: { title: "Companies" },
      },
      {
        path: "job-seekers",
        name: "AdminJobSeekers",
        component: JobSeekerManagementPage,
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
        component: SettingsPage,
      },
      {
        path: "companies/:id",
        name: "AdminCompanyDetail",
        component: CompanyDetailPage,
        meta: { title: "Company Detail" },
      },
      {
        path: "job-seekers/:id",
        name: "AdminJobSeekersDetail",
        component: JobSeekerDetailPage,
        meta: { title: "Job Seekers" },
      },
    ],
  },
];
