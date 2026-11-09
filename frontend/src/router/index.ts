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
import DashboardPage from "@/features/auth/pages/DashboardPage(test).vue";

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
    path: "/",
    redirect: "/login",
  },
  {
    path: "/",
    component: AuthLayout,
    children: [
      {
        path: "login",
        component: LoginPage,
      },
      {
        path: "register",
        component: RegisterPage,
      },
    ],
  },
  {
    path: "/dashboard",
    component: DashboardPage,
    meta: {
      requiresAuth: true,
    },
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: routes,
});

router.beforeEach((to) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    return "/login";
  }

  if ((to.path === "/login" || to.path === "/register") && token) {
    return "/dashboard";
  }
});

export default router;
