<script setup lang="ts">
import { RouterView } from "vue-router";
import { useUiStore } from "@/stores/ui.store";

import AppNavbar from "@/components/common/AppNavbar.vue";
import JobSeekerSidebar from "@/features/job-seeker/components/JobSeekerSidebar.vue";

const uiStore = useUiStore();
</script>

<template>
  <div class="min-h-screen w-full overflow-x-hidden bg-slate-50">

    <!-- Sidebar Overlay -->
    <Transition name="fade">
      <div v-if="uiStore.isSidebarOpen" class="fixed inset-0 z-40 bg-slate-900/50" @click="uiStore.closeSidebar" />
    </Transition>

    <!-- Sidebar -->
    <JobSeekerSidebar />

    <!-- Main Content -->
    <div class="min-h-screen w-full min-w-0">
      <AppNavbar />

      <main class="w-full min-w-0 p-4 sm:p-6 lg:p-8">
        <div class="mx-auto w-full min-w-0 max-w-[1600px]">
          <RouterView />
        </div>
      </main>
    </div>

  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
