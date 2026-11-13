<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

import PublicHeader from "./PublicHeader.vue";
import DashboardHeader from "./DashboardHeader.vue";

const route = useRoute();

const isDashboard = computed(() => {
  return (
    route.path.startsWith("/jobseeker") ||
    route.path.startsWith("/employer") ||
    route.path.startsWith("/admin")
  );
});
</script>

<template>
  <DashboardHeader v-if="isDashboard" />
  <PublicHeader v-else />
</template>
