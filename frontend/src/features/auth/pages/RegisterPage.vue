<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { Briefcase, ShieldCheck, Zap } from "lucide-vue-next"; // Icon များ

import { useAuthStore } from "@/stores/auth.store.ts";
import RegisterForm from "../components/RegisterForm.vue";
import BaseAlert from "@/components/common/BaseAlert.vue";
import type { RegisterData } from "../types/auth.types.ts";

const authStore = useAuthStore();
const router = useRouter();

const errorMessage = ref("");
const loading = ref(false);

const handleRegister = async (data: RegisterData) => {
  try {
    loading.value = true;
    errorMessage.value = "";

    await authStore.register(data);

    router.push("/login");
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || "Registration failed";
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
        <div
          class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"
        ></div>
      </div>

      <!-- Logo -->
      <div class="relative z-10 flex items-center gap-3">
        <div
          class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-600/30"
        >
          <Briefcase class="h-6 w-6" />
        </div>
        <span class="text-2xl font-bold tracking-tight text-white"
          >JobConnect AI</span
        >
      </div>

      <!-- Features -->
      <div class="relative z-10 max-w-md">
        <h2 class="mb-8 text-3xl font-bold leading-tight text-white">
          Start your journey <br />
          <span class="text-blue-400">to a better career.</span>
        </h2>

        <div class="space-y-6">
          <!-- Feature 1 -->
          <div class="flex items-start gap-4">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400"
            >
              <ShieldCheck class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Secure Platform</h3>
              <p class="mt-1 text-sm text-slate-400">
                Your data is protected with enterprise-grade security.
              </p>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="flex items-start gap-4">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400"
            >
              <Zap class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Fast Setup</h3>
              <p class="mt-1 text-sm text-slate-400">
                Create your profile and start applying in minutes.
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
    <!-- RIGHT SIDE: Register Form (Visible on all screens) -->
    <!-- ========================================== -->
    <div
      class="relative flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16 xl:px-24"
    >
      <!-- Mobile Background -->
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
            <Briefcase class="h-6 w-6" />
          </div>
          <span class="text-xl font-bold text-gray-900">JobConnect AI</span>
        </div>

        <!-- Header -->
        <div class="mb-8 text-center lg:text-left">
          <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
            Create an account
          </h2>
          <p class="mt-2 text-sm text-gray-500">
            Join JobConnect and start your journey today.
          </p>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6">
          <BaseAlert
            variant="error"
            title="Registration failed"
            :message="errorMessage"
            dismissible
            @dismiss="errorMessage = ''"
          />
        </div>

        <!-- Form -->
        <div>
          <RegisterForm @submit="handleRegister" />
        </div>

        <!-- Footer link -->
        <p class="mt-8 text-center text-sm text-gray-600 lg:text-left">
          Already have an account?
          <RouterLink
            to="/login"
            class="font-semibold text-blue-600 transition-colors hover:text-blue-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded"
          >
            Sign in
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
