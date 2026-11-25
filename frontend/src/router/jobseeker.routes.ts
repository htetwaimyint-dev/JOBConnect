import type { RouteRecordRaw } from "vue-router";

import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import SettingsPage from "@/components/common/SettingsPage.vue";
import Dashboard from "@/features/job-seeker/pages/Dashboard.vue";
import ProfilePage from "@/features/job-seeker/pages/ProfilePage.vue";
import JobListPage from "@/features/jobs/pages/JobListPage.vue";
import SavedJobsPage from "@/features/jobs/pages/SavedJobsPage.vue";
import MyApplicationsPage from "@/features/applications/pages/MyApplicationsPage.vue";
import InterviewsPage from "@/features/job-seeker/pages/InterviewsPage.vue";
import OffersPage from "@/features/job-seeker/pages/OffersPage.vue";
import JobSeekerOffersPage from "@/features/job-offers/pages/JobSeekerOffersPage.vue";
import CompanyProfilePage from "@/features/job-seeker/pages/CompanyProfilePage.vue";
import CompanyDetailPage from "@/features/job-seeker/pages/CompanyDetailPage.vue";
import ApplicationDetailPage from "@/features/applications/pages/ApplicationDetailPage.vue";
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
        component: MyApplicationsPage,
        meta: { title: "My Applications" },
      },
      //HWM add path for application detail page
      {
        path: "applications/:id",
        name: "JobSeekerApplicationDetail",
        component: ApplicationDetailPage,
        meta: { title: "Application Details" },
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
        path: "job-offers",
        name: "job-seeker-offers",
        component: JobSeekerOffersPage,
      },
      {
        path: "settings",
        name: "JobSeekerSettings",
        component: SettingsPage,
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
