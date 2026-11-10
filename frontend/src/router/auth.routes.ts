import type { RouteRecordRaw } from "vue-router";
import LoginPage from "@/features/auth/pages/LoginPage.vue";
import RegisterPage from "@/features/auth/pages/RegisterPage.vue";

const GUEST_META = { guestOnly: true };

export const authRoutes: RouteRecordRaw[] = [
  {
    path: "/login",
    name: "login",
    component: LoginPage,
    meta: GUEST_META,
  },
  {
    path: "/register",
    name: "register",
    component: RegisterPage,
    meta: GUEST_META,
  },
];
