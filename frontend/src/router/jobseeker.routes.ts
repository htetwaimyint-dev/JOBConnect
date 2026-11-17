import type { RouteRecordRaw } from "vue-router";

import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import JobSeekerSettingsPage from "@/features/job-seeker/pages/JobSeekerSettingsPage.vue";

import Dashboard from "@/features/job-seeker/pages/Dashboard.vue";
import ProfilePage from "@/features/job-seeker/pages/ProfilePage.vue";
import JobListPage from "@/features/jobs/pages/JobListPage.vue";
import SavedJobsPage from "@/features/job-seeker/pages/SavedJobsPage.vue";
import ApplicationsPage from "@/features/job-seeker/pages/ApplicationsPage.vue";
import InterviewsPage from "@/features/job-seeker/pages/InterviewsPage.vue";
import OffersPage from "@/features/job-seeker/pages/OffersPage.vue";
import CompanyProfilePage from "@/features/job-seeker/pages/CompanyProfilePage.vue";
import CompanyDetailPage from "@/features/job-seeker/pages/CompanyDetailPage.vue";
//import SettingsPage from "@/features/job-seeker/pages/SettingsPage.vue";

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
        meta: { title: "Dashboard" },
      },
      {
        //add meta
        path: "profile",
        name: "JobSeekerProfile",
        component: ProfilePage,
        meta: { title: "My Profile" },
      },
      {
        path: "jobs",
        name: "JobSeekerJobs",
        component: JobListPage,
        meta: { title: "Find Jobs" },
      },
      {
        path: "saved-jobs",
        name: "JobSeekerSavedJobs",
        component: SavedJobsPage,
        meta: { title: "Saved Jobs" },
      },
      {
        path: "applications",
        name: "JobSeekerApplications",
        component: ApplicationsPage,
        meta: { title: "My Applications" },
      },
      {
        path: "interviews",
        name: "JobSeekerInterviews",
        component: InterviewsPage,
        meta: { title: "Interviews" },
      },
      {
        path: "companyProfile",
        name: "CompanyProfile",
        component: CompanyProfilePage,
        meta: { title: "Company Profile" },
      },
      {
        path: "offers",
        name: "JobSeekerOffers",
        component: OffersPage,
        meta: { title: "Job Offers" },
      },
      {
        path: "settings",
        name: "JobSeekerSettings",
        component: JobSeekerSettingsPage,
        meta: { title: "Settings" },
      },
      {
        path: "/companies/:id", // :id က Dynamic Parameter ပါ
        name: "CompanyDetail",
        component: CompanyDetailPage,
        meta: { title: "Company Detail" },
      },
    ],
  },
];
