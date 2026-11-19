import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import EmployerDashboard from "@/features/employer/pages/EmployerDashboard.vue";
import EmployerSettingsPage from "@/features/employer/pages/EmployerSettingsPage.vue";
import ApplicantsPage from "@/features/employer/pages/ApplicantsPage.vue";

import CreateJobPage from "@/features/jobs/pages/CreateJobPage.vue";
import EmployerLayout from "@/layouts/EmployerLayout.vue";
import type { RouteRecordRaw } from "vue-router";
import JobDetail from "@/features/jobs/pages/JobDetail.vue";
import EmployerCreateJobOfferPage from "@/features/job-offers/pages/EmployerCreateJobOfferPage.vue";
import EmployerJobOfferPage from "@/features/job-offers/pages/EmployerJobOfferPage.vue";

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
                path: "/employer/jobs/create",
                name: "employer-job-create",
                component: CreateJobPage,
            },
            {
                path: "/employer/jobs/:id/edit",
                name: "employer-job-edit",
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
                component: CompanyProfilePage,
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
                path: "/employer/jobs/:id",
                name: "job-detail",
                component: JobDetail,
            },
            {
                path: "job-offers/:candidateId",
                name: "job-offer",
                component: EmployerCreateJobOfferPage,
                props: (route) => ({
                    candidateId: Number(route.params.candidateId),
                }),
            },

            {
                path: "job-offers/",
                name: "employer-job-offers",
                component: EmployerJobOfferPage,
            },
        ],
    },
];
