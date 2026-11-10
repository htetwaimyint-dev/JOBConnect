import { createRouter, createWebHistory } from "vue-router";
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
