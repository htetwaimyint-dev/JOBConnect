<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/auth.store.ts";

import LoginForm from "../components/LoginForm.vue";

import BaseAlert from "@/components/common/BaseAlert.vue";

import type { LoginData } from "../types/auth.types.ts";

const authStore = useAuthStore();
const router = useRouter();

const errorMessage = ref("");
const loading = ref(false);

const handleLogin = async (data: LoginData) => {
  try {
    loading.value = true;
    errorMessage.value = "";

    await authStore.login(data);

    const role = authStore.user?.role;

    if (role === "job_seeker") {
      router.push("/jobs");
    } else if (role === "admin") {
      router.push("/admin/dashboard");
    } else if (role === "employer") {
      router.push("/employer/dashboard");
    } else {
      errorMessage.value = "Invalid user role";
    }
  } catch (error: any) {
    console.log("Login Error:", error);
    console.log("Response Data:", error.response?.data);
    console.log("Status Code:", error.response?.status);

    errorMessage.value =
      error.response?.data?.message || error.message || "Login failed";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-blue-50 px-4 py-10 sm:px-6 lg:px-8"
  >
    <div class="w-full max-w-md">
      <!-- Brand / Header -->
      <div class="mb-8 text-center">
        <div
          class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-7 w-7"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 13.255A23.93 23.93 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
          </svg>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
          Welcome back
        </h1>
        <p class="mt-2 text-sm text-gray-500">
          Sign in to your JobConnect account
        </p>
      </div>

      <!-- Card -->
      <div
        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl shadow-gray-200/50 sm:p-8"
      >
        <!-- Error alert -->
        <BaseAlert
          v-if="errorMessage"
          class="mb-5"
          variant="error"
          title="Login failed"
          :message="errorMessage"
          dismissible
          @dismiss="errorMessage = ''"
        />

        <LoginForm @submit="handleLogin" />
      </div>

      <!-- Footer link -->
      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <RouterLink
          to="/register"
          class="font-semibold text-blue-600 hover:text-blue-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded"
        >
          Create one
        </RouterLink>
      </p>
    </div>
  </div>
</template>
