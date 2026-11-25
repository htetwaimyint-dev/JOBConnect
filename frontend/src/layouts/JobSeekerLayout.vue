<script setup lang="ts">
import { RouterView } from "vue-router";
import { useUiStore } from "@/stores/ui.store";

import AppHeader from "@/components/layout/AppHeader.vue";
import AppSidebar from "@/components/layout/AppSidebar.vue";
import AppPageContainer from "@/components/layout/AppPageContainer.vue";

const uiStore = useUiStore();
</script>

<template>
  <div class="min-h-screen w-full bg-slate-50">
    <!-- Sidebar Overlay -->
    <Transition name="fade">
      <div
        v-if="uiStore.isSidebarOpen"
        class="fixed inset-0 z-40 bg-slate-900/50"
        @click="uiStore.closeSidebar"
      ></div>
    </Transition>

    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main Content -->
    <div class="min-h-screen w-full min-w-0">
      <!-- Header -->
      <AppHeader />

      <!-- Page Content -->
      <AppPageContainer>
        <RouterView />
      </AppPageContainer>
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
