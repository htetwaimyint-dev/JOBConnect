import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";

import { useAuthStore } from "@/stores/auth.store";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployerLayout from "@/layouts/EmployerLayout.vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import CompanyProfileForm from "@/features/employer/pages/CompanyProfileForm.vue";

// Dashboard Pages
import Dashboard from "@/features/job-seeker/pages/Dashboard.vue";
import AdminDashboard from "@/features/admin/pages/Dashboard.vue";
import EmployerDashboard from "@/features/employer/pages/Dashboard.vue";

// Job Seeker Pages
import ProfilePage from "@/features/job-seeker/pages/ProfilePage.vue";
import JobsPage from "@/features/job-seeker/pages/JobsPage.vue";
import SavedJobsPage from "@/features/job-seeker/pages/SavedJobsPage.vue";
import ApplicationsPage from "@/features/job-seeker/pages/ApplicationsPage.vue";
import InterviewsPage from "@/features/job-seeker/pages/InterviewsPage.vue";
import OffersPage from "@/features/job-seeker/pages/OffersPage.vue";
import SettingsPage from "@/features/job-seeker/pages/SettingsPage.vue";

// Auth Routes
import { authRoutes } from "./auth.routes";
import CompanyProfilePage from "@/features/employer/pages/CompanyProfilePage.vue";

const routes: RouteRecordRaw[] = [
    ...authRoutes,

    // Public Layout
    {
        path: "/",
        component: PublicLayout,
        children: [],
    },

    // Job Seeker Routes
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

    // Admin Routes
    {
        path: "/admin",
        component: AdminLayout,
        meta: {
            requiresAuth: true,
            role: "admin",
        },
    },
    // Employer Routes
    {
        path: "/employer",
        component: EmployerLayout,
        meta: {
            requiresAuth: true,
            role: "employer",
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    // Authentication check
    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    // Role authorization check
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    return true;
});

export default router;
