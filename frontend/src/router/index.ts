import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth.store";
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
    ...adminRoutes,
    ...employerRoutes,
    ...jobseekerRoutes,
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
        component: JobSeekerLayout,
        meta: {
            requiresAuth: true,
            role: "employer",
        },
        children: [
            {
                path: "/employer/profile",
                name: "company-profile",
                component: CompanyProfilePage,
            },
            {
                path: "/employer/profile/edit",
                name: "employer.company-profile.edit",
                component: CompanyProfileForm,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Redirect user to their own dashboard
const getDashboardPath = (role?: string) => {
  switch (role) {
    case "job_seeker":
      return "/jobseeker/dashboard";

    case "employer":
      return "/employer/dashboard";

    case "admin":
      return "/admin/dashboard";

    default:
      return "/";
  }
};

// Navigation Guard
router.beforeEach(async (to) => {
    const authStore = useAuthStore();

<<<<<<< Updated upstream
    if (!authStore.initialized) {
        await authStore.initializeAuth();
    }

    if (to.meta.requiresAuth && !authStore.token) {
        return "/login";
    }

    if (to.meta.role && authStore.user?.role !== to.meta.role) {
        return "/";
    }

    if (to.path === "/login" && authStore.token) {
        return "/";
    }
=======
  // Initialize authentication before checking routes
  await authStore.initializeAuth();

  const requiresAuth = to.matched.some(
    (record) => record.meta.requiresAuth,
  );

  const guestOnly = to.matched.some(
    (record) => record.meta.guestOnly,
  );

  // User must login first
  if (requiresAuth && !authStore.isAuthenticated) {
    return {
      name: "login",
      query: { redirect: to.fullPath },
    };
  }

  // Check user role
  if (
    requiresAuth &&
    to.meta.role &&
    authStore.user?.role !== to.meta.role
  ) {
    return getDashboardPath(authStore.user?.role);
  }

  // Logged-in users should not visit login/register
  if (guestOnly && authStore.isAuthenticated) {
    return getDashboardPath(authStore.user?.role);
  }
>>>>>>> Stashed changes

    return true;
});

export default router;
