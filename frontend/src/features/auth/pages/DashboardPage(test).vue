<script setup lang="ts">
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.store.ts";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  try {
    await authStore.logout();
    router.push("/login");
  } catch (error) {
    console.error("Logout error:", error);
  }
};
</script>

<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">Welcome to JobConnect</h1>

    <p v-if="authStore.user">Hello, {{ authStore.user.name }}</p>

    <button
      @click="handleLogout"
      class="mt-5 bg-red-600 text-white px-5 py-2 rounded-lg"
    >
      Logout
    </button>
  </div>
</template>
