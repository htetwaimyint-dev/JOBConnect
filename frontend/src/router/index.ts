import {
  createRouter,
  createWebHistory,
  type RouteRecordRaw,
} from "vue-router";

// Demo Components
import { useAuthStore } from "@/stores/auth.store.ts";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployeeLayout from "@/layouts/EmployerLayout.vue";
import AdminLayout from "@/layouts/AdminLayout.vue";

// Dashboard Components
import JobSeekerDashboard from "@/features/job-seeker/pages/Dashboard.vue";
import AdminDashboard from "@/features/admin/pages/Dashboard.vue";
import EmployerDashboard from "@/features/employer/pages/Dashboard.vue";

// Import Route Files
import { authRoutes } from "./auth.routes";

// Combine All Routes
const routes: RouteRecordRaw[] = [
  //Auth Routes
  ...authRoutes,

  // Public Website Layout
  {
    path: "/",
    component: PublicLayout,
    children: [
      // Add your public website pages here later
    ],
  },

  // Job Seeker Layout
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
        component: JobSeekerDashboard,
      },
    ],
  },

  //admin
  {
    path: "/admin",
    component: AdminLayout,
    meta: {
      requiresAuth: true,
      role: "admin",
    },
    children: [
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: AdminDashboard,
      },
<<<<<<< HEAD
    //   {
    //       path: "settings",
    //       name: "JobSeekerSettings",
    //       component: () =>
    //           import("@/features/job-seeker/pages/JobSeekerSettingsPage.vue"),
    //   },
=======
>>>>>>> main
    ],
  },

  // Employee Layout
  {
    path: "/employer",
    component: EmployeeLayout,
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
    ],
  },
<<<<<<< HEAD

=======
>>>>>>> main
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const authStore = useAuthStore();
  const token = localStorage.getItem("token");

  // Check Authentication
  if (to.meta.requiresAuth && !authStore.token) {
    return "/login";
  }

  // Check Authorization
  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    return "/";
  }
  return true;
});

export default router;
