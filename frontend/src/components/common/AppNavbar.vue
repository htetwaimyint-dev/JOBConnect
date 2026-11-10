<script setup lang="ts">
import { ref } from "vue";
import {
  Bell,
  ChevronDown,
  UserRound,
  Settings,
  LogOut,
} from "lucide-vue-next";

const showProfileMenu = ref(false);
const showNotifications = ref(false);

const props = withDefaults(
  defineProps<{
    title?: string;
  }>(),
  {
    title: "JobSeeker",
  },
);

const user = {
  name: "Aung Kyaw Htay",
  role: "Job Seeker",
  avatar: "",
  notifications: 3,
};

const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
  showNotifications.value = false;
};

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showProfileMenu.value = false;
};

const handleLogout = () => {
  console.log("Logout clicked");
};
</script>

<template>
  <header
    class="sticky top-0 z-[60] flex h-20 w-full items-center justify-between border-b border-slate-200 bg-white px-6 shadow-sm"
  >
    <!-- Page Title -->
    <div class="flex items-center">
      <h2 class="text-xl font-bold text-slate-800">
        {{ props.title }}
      </h2>
    </div>

    <!-- Right Actions -->
    <div class="flex shrink-0 items-center gap-5">
      <!-- Notifications -->
      <div class="relative">
        <button
          type="button"
          @click="toggleNotifications"
          class="relative rounded-full p-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600"
        >
          <Bell :size="23" />

          <span
            v-if="user.notifications > 0"
            class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
          >
            {{ user.notifications }}
          </span>
        </button>

        <!-- Notification Dropdown -->
        <div
          v-if="showNotifications"
          class="absolute right-0 top-14 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-xl"
        >
          <h3 class="mb-3 font-semibold text-slate-800">Notifications</h3>

          <p class="border-b border-slate-100 py-3 text-sm text-slate-600">
            Your profile has been viewed.
          </p>

          <p class="py-3 text-sm text-slate-600">
            You have a new job recommendation.
          </p>
        </div>
      </div>

      <!-- Divider -->
      <div class="h-9 w-px bg-slate-200"></div>

      <!-- User Profile -->
      <div class="relative">
        <button
          type="button"
          @click="toggleProfileMenu"
          class="flex items-center gap-3 rounded-lg p-1.5 transition hover:bg-slate-50"
        >
          <!-- Avatar -->
          <div
            class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-blue-100 text-sm font-bold text-blue-700"
          >
            {{ user.name.charAt(0) }}
          </div>

          <!-- User Information -->
          <div class="hidden text-left sm:block">
            <p class="text-sm font-semibold text-slate-800">
              {{ user.name }}
            </p>

            <p class="text-xs text-slate-500">
              {{ user.role }}
            </p>
          </div>

          <ChevronDown
            :size="17"
            class="text-slate-500 transition"
            :class="{ 'rotate-180': showProfileMenu }"
          />
        </button>

        <!-- Profile Dropdown -->
        <div
          v-if="showProfileMenu"
          class="absolute right-0 top-14 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
        >
          <button
            type="button"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600"
          >
            <UserRound :size="18" />
            My Profile
          </button>

          <button
            type="button"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600"
          >
            <Settings :size="18" />
            Settings
          </button>

          <div class="my-1 border-t border-slate-100"></div>

          <button
            type="button"
            @click="handleLogout"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-red-500 hover:bg-red-50"
          >
            <LogOut :size="18" />
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
