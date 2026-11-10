
import { createRouter, createWebHistory } from "vue-router";

// Demo Components
import Button from "@/components/common/demo/Button.vue";
import Checkbox from "@/components/common/demo/Checkbox.vue";
import Input from "@/components/common/demo/Input.vue";
import PasswordInput from "@/components/common/demo/PasswordInput.vue";
import Select from "@/components/common/demo/Select.vue";
import Alert from "@/components/common/demo/Alert.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import LoginPage from "@/features/auth/pages/LoginPage.vue";
import RegisterPage from "@/features/auth/pages/RegisterPage.vue";
import { useAuthStore } from "@/stores/auth.store.ts";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployeeLayout from "@/layouts/EmployerLayout.vue";
import AdminLayout from "@/layouts/AdminLayout.vue";

const routes = [
  {
    path: "/button",
    component: Button,
  },
  {
    path: "/checkbox",
    component: Checkbox,
  },
  {
    path: "/input",
    component: Input,
  },
  {
    path: "/passwordinput",
    component: PasswordInput,
  },
  {
    path: "/select",
    component: Select,
  },
  {
    path: "/alert",
    component: Alert,
  },

  {
    path: "/login",
    component: LoginPage,
  },
  {
    path: "/register",
    component: RegisterPage,
  },
  {
    path: "/jobs",
    component: LoginPage,
    meta: {
      requiresAuth: true,
      role: "job_seeker",
    },
  },
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
    children: [
      {
        path: "dashboard",
        name: "JobSeekerDashboard",
        component: () =>
          import("@/features/job-seeker/pages/Dashboard.vue"),
      },
      // {
      //     path: "settings",
      //     name: "JobSeekerSettings",
      //     component: () =>
      //         import("@/features/job-seeker/pages/JobSeekerSettingsPage.vue"),
      // },
    ],
  },

  // Employee Layout
  {
    path: "/employer",
    component: EmployeeLayout,
    children: [
      {
        path: "dashboard",
        name: "EmployerDashboard",
        component: () =>
          import("@/features/employer/pages/Dashboard.vue"),
      },
    ],
  },
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: () =>
          import("@/features/admin/pages/Dashboard.vue"),
      },
    ],
  },

  // Demo Routes
  {
    path: "/button",
    component: Button,
  },
  {
    path: "/admin/dashboard",
    component: LoginPage,
    meta: {
      requiresAuth: true,
      role: "admin",
    },
  },
  {
    path: "/employer/dashboard",
    component: LoginPage,
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

router.beforeEach((to) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.token) {
    return "/login";
  }

  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    return "/login";
  }

});

export default router;
