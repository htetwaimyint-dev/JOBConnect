<script setup lang="ts">
import { RouterView } from "vue-router";
import AppHeader from "@/components/layout/AppHeader.vue";
import AppFooter from "@/components/layout/AppFooter.vue";
</script>

<template>
  <div class="flex min-h-screen flex-col bg-[#F8FAFC]">
    <!-- Website Header -->
    <AppHeader />

    <!-- Main Page Content -->
    <main class="flex-1">
      <RouterView />
    </main>

    <!-- Website Footer -->
    <AppFooter />
  </div>
</template>
