<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

import PublicHeader from "./PublicHeader.vue";
import DashboardHeader from "./DashboardHeader.vue";
import { useAuthStore } from "@/stores/auth.store";

const route = useRoute();
const authStore = useAuthStore();

const isDashboard = computed(() => {
  const role = authStore.user?.role;

  return (
    role === "employer" ||
    role === "admin" ||
    route.path.startsWith("/employer") ||
    route.path.startsWith("/admin")
  );
});
</script>

<template>
  <DashboardHeader v-if="isDashboard" />
  <PublicHeader v-else />
</template>
