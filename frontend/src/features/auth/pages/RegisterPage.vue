<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/auth.store.ts";

import RegisterForm from "../components/RegisterForm.vue";

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

    router.push("/dashboard");
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || "Registration failed";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="max-w-md mx-auto mt-20 p-6 shadow rounded-xl">
    <h1 class="text-2xl font-bold text-center mb-6">
      Create JobConnect Account
    </h1>

    <p v-if="errorMessage" class="text-red-500 mb-4">
      {{ errorMessage }}
    </p>

    <RegisterForm @submit="handleRegister" />

    <p v-if="loading" class="text-center mt-3">Registering...</p>

    <p class="text-center mt-4">
      Already have an account?
      <RouterLink to="/login" class="text-blue-600"> Login </RouterLink>
    </p>
  </div>
</template>
