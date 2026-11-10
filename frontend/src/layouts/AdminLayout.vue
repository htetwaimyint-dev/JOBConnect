<script setup lang="ts">
import { RouterView } from "vue-router";

import AppNavbar from "@/components/common/AppNavbar.vue";
import AdminSidebar from "@/features/admin/components/AdminSidebar.vue";
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Area -->
    <div class="ml-64 min-h-screen">
      <!-- Navbar -->
      <AppNavbar title="Admin" />

      <!-- Page Content -->
      <main class="p-6 sm:p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>
