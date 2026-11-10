<script setup lang="ts">
import { RouterView } from "vue-router";

import AppNavbar from "@/components/common/AppNavbar.vue";
import JobSeekerSidebar from "@/features/job-seeker/components/JobSeekerSidebar.vue";

const handleLogout = () => {
  console.log("Job Seeker Logout");
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <JobSeekerSidebar />

    <!-- Main Area -->
    <div class="ml-64 min-h-screen">
      <!-- Navbar -->
      <AppNavbar />

      <!-- Page Content -->
      <main class="p-6 sm:p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>
