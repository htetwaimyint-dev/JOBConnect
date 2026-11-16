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
      router.push("/");
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
  <div class="relative flex min-h-screen w-full overflow-hidden bg-white">
    <!-- ========================================== -->
    <!-- LEFT SIDE: Branding (Only visible on Large Screens) -->
    <!-- ========================================== -->
    <div
      class="relative hidden w-1/2 flex-col justify-between bg-slate-900 p-12 lg:flex xl:p-16"
    >
      <!-- Background Image & Overlay -->
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2850&q=80"
          alt="Team working together"
          class="h-full w-full object-cover opacity-40 transition-transform duration-1000 hover:scale-105"
        />
        <!-- Gradient Overlay -->
        <div
          class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"
        ></div>
      </div>

      <!-- Logo -->
      <div class="relative z-10 flex items-center gap-3">
        <div
          class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-600/30"
        >
          <svg
            xmlns="www.w3.org/2000/svg"
            class="h-6 w-6"
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
        <span class="text-2xl font-bold tracking-tight text-white"
          >JobConnect AI</span
        >
      </div>

      <!-- Features (Sarah ရဲ့ ပုံအစား ထည့်ထားတယ်) -->
      <div class="relative z-10 max-w-md">
        <h2 class="mb-8 text-3xl font-bold leading-tight text-white">
          Find your dream job <br />
          <span class="text-blue-400">faster with AI.</span>
        </h2>

        <div class="space-y-6">
          <!-- Feature 1 -->
          <div class="flex items-start gap-4">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400"
            >
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">
                Verified Companies
              </h3>
              <p class="mt-1 text-sm text-slate-400">
                Apply with confidence to trusted and verified employers.
              </p>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="flex items-start gap-4">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400"
            >
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">AI Job Matching</h3>
              <p class="mt-1 text-sm text-slate-400">
                Get personalized job recommendations based on your skills.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 text-sm text-slate-500">
        &copy; {{ new Date().getFullYear() }} JobConnect AI. All rights
        reserved.
      </div>
    </div>

    <!-- ========================================== -->
    <!-- RIGHT SIDE: Login Form (Visible on all screens) -->
    <!-- ========================================== -->
    <div
      class="relative flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16 xl:px-24"
    >
      <!-- Mobile Background (Only visible on small screens) -->
      <div class="absolute inset-0 z-0 lg:hidden">
        <img
          src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2850&q=80"
          alt="Background"
          class="h-full w-full object-cover opacity-5"
        />
      </div>

      <!-- Form Container -->
      <div class="relative z-10 mx-auto w-full max-w-md lg:max-w-sm">
        <!-- Mobile Logo -->
        <div class="mb-10 flex items-center gap-3 lg:hidden">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
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
          <span class="text-xl font-bold text-gray-900">JobConnect AI</span>
        </div>

        <!-- Header -->
        <div class="mb-8 text-center lg:text-left">
          <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
            Welcome back
          </h2>
          <p class="mt-2 text-sm text-gray-500">
            Please enter your details to sign in.
          </p>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6">
          <BaseAlert
            variant="error"
            title="Login failed"
            :message="errorMessage"
            dismissible
            @dismiss="errorMessage = ''"
          />
        </div>

        <!-- Form -->
        <div class="space-y-6">
          <LoginForm @submit="handleLogin" />
        </div>

        <!-- Footer link -->
        <p class="mt-8 text-center text-sm text-gray-600 lg:text-left">
          Don't have an account?
          <RouterLink
            to="/register"
            class="font-semibold text-blue-600 transition-colors hover:text-blue-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded"
          >
            Sign up
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
