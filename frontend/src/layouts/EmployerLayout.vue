<script setup lang="ts">
import { RouterView } from "vue-router";

import AppNavbar from "@/components/common/AppNavbar.vue";
import EmployeeSidebar from "@/features/employer/components/EmployeeSidebar.vue";
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <EmployeeSidebar />

    <div class="ml-64 min-h-screen">
      <AppNavbar title="Employer" />

      <main class="p-6 sm:p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>
