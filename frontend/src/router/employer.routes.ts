
import type { RouteRecordRaw } from "vue-router";

import EmployerLayout from "@/layouts/EmployerLayout.vue";

import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";
import CreateJobPage from "@/features/jobs/pages/CreateJobPage.vue";

export const employerRoutes: RouteRecordRaw[] = [
    {
        path: "/employer",
        component: EmployerLayout,
        meta: {
            requiresAuth: true,
            role: "employer",
        },
        children: [
            {
                path: "profile",
                name: "company-profile",
                component: CompanyProfilePage,
            },
            {
                path: "profile/edit",
                name: "employer.company-profile.edit",
                component: CompanyProfileForm,
            },
            {
                path: "jobs",
                name: "employer-job-create",
                component: CreateJobPage,
            },
        ],
    },
];
