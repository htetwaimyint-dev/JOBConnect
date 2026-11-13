import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import EmployerDashboard from "@/features/employer/pages/EmployerDashboard.vue";
import CreateJobPage from "@/features/jobs/pages/CreateJobPage.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import type { RouteRecordRaw } from "vue-router";

export const employerRoutes: RouteRecordRaw[] = [
    {
        path: "/employer",
        component: JobSeekerLayout,
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
