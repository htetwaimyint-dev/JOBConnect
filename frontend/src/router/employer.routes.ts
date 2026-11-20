import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";
import EmployerDashboard from "@/features/employer/pages/EmployerDashboard.vue";
import EmployerSettingsPage from "@/features/employer/pages/EmployerSettingsPage.vue";
import ApplicantsPage from "@/features/employer/pages/ApplicantsPage.vue";
import JobSeekersPage from "@/features/employer/pages/JobSeekersPage.vue";
import JobSeekerDetailPage from "@/features/employer/pages/JobSeekerDetailPage.vue";
import CreateJobPage from "@/features/jobs/pages/CreateJobPage.vue";
import JobDetail from "@/features/jobs/pages/JobDetail.vue";
import EmployerLayout from "@/layouts/EmployerLayout.vue";
import type { RouteRecordRaw } from "vue-router";
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
                path: "jobs",
                name: "EmployerJobs",
                component: () =>
                    import("@/features/jobs/pages/EmployerJobsPage.vue"),
            },
            {
                path: "jobs/create",
                name: "employer-job-create",
                component: CreateJobPage,
            },
            {
                path: "jobs/:id/edit",
                name: "employer-job-edit",
                component: CreateJobPage,
            },
            {
                path: "jobs/:id",
                name: "job-detail",
                component: JobDetail,
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
                component: JobSeekersPage,
            },
            {
                path: "direct-offers",
                name: "EmployerDirectOffers",
                component: CompanyProfilePage,
            },
            {
                path: "profile/create",
                name: "employer.company-profile.create",
                component: CompanyProfileForm,
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
                path: "job-seekers/:id",
                name: "EmployerJobSeekerDetail",
                component: JobSeekerDetailPage,
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
