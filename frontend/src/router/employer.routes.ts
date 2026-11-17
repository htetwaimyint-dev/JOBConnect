import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import EmployerDashboard from "@/features/employer/pages/EmployerDashboard.vue";
import EmployerSettingsPage from "@/features/employer/pages/EmployerSettingsPage.vue";
import ApplicantsPage from "@/features/employer/pages/ApplicantsPage.vue";
import JobPreview from "@/features/jobs/pages/JobPreview.vue";
import DirectOfferPage from "@/features/employer/pages/DirectOfferPage.vue";

import CreateJobPage from "@/features/jobs/pages/CreateJobPage.vue";
import EmployerLayout from "@/layouts/EmployerLayout.vue";
import type { RouteRecordRaw } from "vue-router";
import JobDetail from "@/features/jobs/pages/JobDetail.vue";

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
                path: "dashboard",
                name: "EmployerDashboard",
                component: EmployerDashboard,
            },
            {
                path: "jobs",
                name: "employer-job-create",
                component: CreateJobPage,
            },
            {
                path: "applicants",
                name: "EmployerApplicants",
                component: ApplicantsPage,
            },
            {
                path: "profile",
                name: "company-profile",
                component: CompanyProfilePage,
            },
            {
                path: "job-seekers",
                name: "EmployerJobSeekerProfiles",
                component: CompanyProfilePage,
            },
            {
                path: "direct-offers",
                name: "EmployerDirectOffers",
                component: DirectOfferPage,
            },
            {
                path: "profile/edit",
                name: "employer.company-profile.edit",
                component: CompanyProfileForm,
            },
            {
                path: "settings",
                name: "EmployerSettings",
                component: EmployerSettingsPage,
            },

            {
                path: "/employer/jobs/preview",
                name: "employer-job-preview",
                component: JobPreview,
            },

            {
                path: "/employer/jobs/:id",
                name: "job-detail",
                component: JobDetail,
            },
        ],
    },
];
