<script setup lang="ts">
import { RouterView } from "vue-router";
import { useUiStore } from "@/stores/ui.store";

import AppHeader from "@/components/layout/AppHeader.vue";
import AppSidebar from "@/components/layout/AppSidebar.vue";

const uiStore = useUiStore();
</script>

<template>
    <div
        class="relative flex h-dvh w-full flex-col overflow-hidden bg-slate-50"
    >
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
        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
            <!-- Shared Header -->
            <AppHeader class="shrink-0" />

            <!-- Page Content -->
            <main
                class="flex min-h-0 w-full min-w-0 flex-1 flex-col overflow-y-auto p-3 sm:p-4 lg:p-5"
            >
                <div
                    class="mx-auto flex min-h-0 w-full min-w-0 max-w-[1600px] flex-1 flex-col"
                >
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
