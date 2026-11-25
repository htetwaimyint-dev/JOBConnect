<script setup lang="ts">
import { computed } from "vue";
import { RouterLink, useRouter } from "vue-router";
import { BriefcaseBusiness, X, LogOut, Settings } from "lucide-vue-next";

import { navigationConfig } from "@/config/navigation";
import { useAuthStore } from "@/stores/auth.store";
import { useUiStore } from "@/stores/ui.store";

const authStore = useAuthStore();
const uiStore = useUiStore();
const router = useRouter();

// Current role
const role = computed(() => authStore.user?.role ?? "");

// Role-based navigation
const roleNavigation = computed(() => {
  return (
    navigationConfig[role.value] ?? {
      main: [],
      account: [],
    }
  );
});

const navigationItems = computed(() => roleNavigation.value.main);
const accountItems = computed(() => roleNavigation.value.account);

// Move Settings to sidebar footer
const settingsItem = computed(() =>
  accountItems.value.find((item) => item.label.toLowerCase() === "settings"),
);

// Other account links
const otherAccountItems = computed(() =>
  accountItems.value.filter((item) => item.label.toLowerCase() !== "settings"),
);

// User information
const userName = computed(() => authStore.user?.name ?? "User");

const userInitial = computed(() => {
  return userName.value.trim().charAt(0).toUpperCase() || "U";
});

const roleLabel = computed(() => {
  switch (role.value) {
    case "admin":
      return "Admin";

    case "employer":
      return "Employer";

    case "job_seeker":
      return "Job Seeker";

    default:
      return role.value;
  }
});

// Shared navigation styles
const menuClass =
  "flex min-h-[44px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200";

// Active navigation styles
const activeMenuClass = "!bg-[#102D59] !text-white !font-semibold shadow-sm";

// Logout
const handleLogout = async () => {
  try {
    uiStore.closeSidebar();

    await authStore.logout();

    await router.replace("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};
</script>

<template>
  <!-- Background Overlay -->
  <Transition name="sidebar-overlay">
    <div
      v-if="uiStore.isSidebarOpen"
      class="fixed inset-0 z-40 bg-slate-900/25 backdrop-blur-[2px]"
      @click="uiStore.closeSidebar()"
    ></div>
  </Transition>

  <!-- Sidebar -->
  <Transition name="sidebar">
    <aside
      v-if="uiStore.isSidebarOpen"
      class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col border-r border-slate-200 bg-white shadow-2xl"
      aria-label="Dashboard navigation"
    >
      <!-- Header -->
      <div
        class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-5"
      >
        <RouterLink
          to="/"
          class="flex items-center gap-3 no-underline"
          @click="uiStore.closeSidebar()"
        >
          <div
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#102D59] text-white"
          >
            <BriefcaseBusiness :size="20" />
          </div>

          <h2 class="text-lg font-bold tracking-tight text-[#102D59]">
            JobConnect
          </h2>
        </RouterLink>

        <button
          type="button"
          class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-[#102D59]"
          aria-label="Close sidebar"
          @click="uiStore.closeSidebar()"
        >
          <X :size="20" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto px-4 py-6">
        <!-- Main Navigation -->
        <div>
          <p
            class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400"
          >
            Workspace
          </p>

          <div class="space-y-1">
            <template v-for="item in navigationItems" :key="item.to">
              <!-- Menu with Submenu -->
              <div v-if="item.children?.length" class="space-y-1">
                <div
                  class="flex min-h-[44px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-600"
                >
                  <component :is="item.icon" :size="19" class="shrink-0" />

                  <span>{{ item.label }}</span>
                </div>

                <div class="ml-4 space-y-1 border-l-2 border-slate-100 pl-3">
                  <RouterLink
                    v-for="child in item.children"
                    :key="child.to"
                    :to="child.to"
                    :class="[
                      menuClass,
                      'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
                    ]"
                    :active-class="activeMenuClass"
                    @click="uiStore.closeSidebar()"
                  >
                    <component :is="child.icon" :size="17" class="shrink-0" />

                    <span>{{ child.label }}</span>
                  </RouterLink>
                </div>
              </div>

              <!-- Regular Menu -->
              <RouterLink
                v-else
                :to="item.to"
                :class="[
                  menuClass,
                  'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
                ]"
                :active-class="activeMenuClass"
                @click="uiStore.closeSidebar()"
              >
                <component :is="item.icon" :size="19" class="shrink-0" />

                <span>{{ item.label }}</span>
              </RouterLink>
            </template>
          </div>
        </div>

        <!-- Account Navigation -->
        <div v-if="otherAccountItems.length" class="mt-8">
          <p
            class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400"
          >
            Account
          </p>

          <div class="space-y-1">
            <RouterLink
              v-for="item in otherAccountItems"
              :key="item.to"
              :to="item.to"
              :class="[
                menuClass,
                'text-slate-600 hover:bg-slate-100 hover:text-[#102D59]',
              ]"
              :active-class="activeMenuClass"
              @click="uiStore.closeSidebar()"
            >
              <component :is="item.icon" :size="19" class="shrink-0" />

              <span>{{ item.label }}</span>
            </RouterLink>
          </div>
        </div>
      </nav>

      <!-- User Account Footer -->
      <div class="shrink-0 border-t border-slate-100 p-4">
        <!-- User Information -->
        <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#102D59] text-sm font-bold text-white"
          >
            {{ userInitial }}
          </div>

          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-slate-800">
              {{ userName }}
            </p>

            <p class="text-xs font-medium capitalize text-slate-500">
              {{ roleLabel }}
            </p>
          </div>
        </div>

        <!-- Settings -->
        <RouterLink
          v-if="settingsItem"
          :to="settingsItem.to"
          class="flex min-h-[44px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-[#102D59]"
          :active-class="activeMenuClass"
          @click="uiStore.closeSidebar()"
        >
          <Settings :size="19" class="shrink-0" />

          <span>Settings</span>
        </RouterLink>

        <!-- Logout -->
        <button
          type="button"
          class="mt-1 flex min-h-[44px] w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
          @click="handleLogout"
        >
          <LogOut :size="19" class="shrink-0" />

          <span>Logout</span>
        </button>
      </div>
    </aside>
  </Transition>
</template>

<style scoped>
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}

.sidebar-overlay-enter-active,
.sidebar-overlay-leave-active {
  transition: opacity 0.3s ease;
}

.sidebar-overlay-enter-from,
.sidebar-overlay-leave-to {
  opacity: 0;
}
</style>
