<script setup lang="ts">
import { RouterView } from "vue-router";
import { useUiStore } from "@/stores/ui.store";

<<<<<<< Updated upstream
import AppNavbar from "@/components/common/AppNavbar.vue";
import JobSeekerSidebar from "@/features/job-seeker/components/JobSeekerSidebar.vue";
=======

import AppHeader from "@/components/layout/AppHeader.vue";
import AppSidebar from "@/components/layout/AppSidebar.vue";
>>>>>>> Stashed changes

const uiStore = useUiStore();
</script>

<template>
  <<<<<<< Updated upstream
  <div class="min-h-screen w-full overflow-x-clip bg-slate-50">
    =======
  <div class="min-h-screen w-full bg-slate-50">
>>>>>>> Stashed changes
    <!-- Sidebar Overlay -->
        <Transition name="fade">
            <div
               
        v-if="uiStore.isSidebarOpen"
               
        class="fixed inset-0 z-40 bg-slate-900/50"
               
        @click="uiStore.closeSidebar"
           
      ></div>
        </Transition>

        <!-- Shared Sidebar -->
        <AppSidebar />

        <!-- Main Content -->
    <<<<<<< Updated upstream
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
