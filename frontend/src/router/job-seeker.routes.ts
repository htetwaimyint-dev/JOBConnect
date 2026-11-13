
import type { RouteRecordRaw } from "vue-router";

import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";

import Dashboard from "@/features/job-seeker/pages/Dashboard.vue";
import ProfilePage from "@/features/job-seeker/pages/ProfilePage.vue";
import JobListPage from "@/features/jobs/pages/JobListPage.vue";
import SavedJobsPage from "@/features/jobs/pages/SavedJobsPage.vue";
import ApplicationsPage from "@/features/job-seeker/pages/ApplicationsPage.vue";
import InterviewsPage from "@/features/job-seeker/pages/InterviewsPage.vue";
import OffersPage from "@/features/job-seeker/pages/OffersPage.vue";
import SettingsPage from "@/features/job-seeker/pages/SettingsPage.vue";

export const jobSeekerRoutes: RouteRecordRaw[] = [
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
                component: JobListPage,
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
