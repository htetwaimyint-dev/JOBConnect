import ApplicationsPage from "@/features/job-seeker/pages/ApplicationsPage.vue";
import Dashboard from "@/features/job-seeker/pages/Dashboard.vue";
import InterviewsPage from "@/features/job-seeker/pages/InterviewsPage.vue";
import JobsPage from "@/features/job-seeker/pages/JobsPage.vue";
import OffersPage from "@/features/job-seeker/pages/OffersPage.vue";
import ProfilePage from "@/features/job-seeker/pages/ProfilePage.vue";
import SavedJobsPage from "@/features/job-seeker/pages/SavedJobsPage.vue";
import SettingsPage from "@/features/job-seeker/pages/SettingsPage.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import type { RouteRecordRaw } from "vue-router";

export const jobseekerRoutes: RouteRecordRaw[] = [
    {
        path: "/jobseeker",
        component: JobSeekerLayout,
        meta: {
            requiresAuth: true,
            role: "job_seeker",
        },
        children: [
            {
                path: "dashboard",
                name: "JobSeekerDashboard",
                component: Dashboard,
            },
            {
                path: "profile",
                name: "JobSeekerProfile",
                component: ProfilePage,
            },
            {
                path: "jobs",
                name: "JobSeekerJobs",
                component: JobsPage,
            },
            {
                path: "saved-jobs",
                name: "JobSeekerSavedJobs",
                component: SavedJobsPage,
            },
            {
                path: "applications",
                name: "JobSeekerApplications",
                component: ApplicationsPage,
            },
            {
                path: "interviews",
                name: "JobSeekerInterviews",
                component: InterviewsPage,
            },
            {
                path: "offers",
                name: "JobSeekerOffers",
                component: OffersPage,
            },
            {
                path: "settings",
                name: "JobSeekerSettings",
                component: SettingsPage,
            },
        ],
    },
];
