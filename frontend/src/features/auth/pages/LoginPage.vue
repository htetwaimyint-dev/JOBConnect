<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/auth.store.ts";

import LoginForm from "../components/LoginForm.vue";

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
    errorMessage.value = error.response?.data?.message || "Login failed";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="max-w-md mx-auto mt-20 p-6 shadow rounded-xl">
    <h1 class="text-2xl font-bold text-center mb-6">Login to JobConnect</h1>

    <p v-if="errorMessage" class="text-red-500 mb-4">
      {{ errorMessage }}
    </p>

    <LoginForm @submit="handleLogin" />

    <p v-if="loading" class="text-center mt-3">Logging in...</p>

    <p class="text-center mt-4">
      Don't have an account?
      <RouterLink to="/register" class="text-blue-600"> Register </RouterLink>
    </p>
  </div>
</template>
