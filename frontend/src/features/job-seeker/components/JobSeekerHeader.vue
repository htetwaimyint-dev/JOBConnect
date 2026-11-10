<script setup lang="ts">
import { ref } from "vue";
import { RouterLink } from "vue-router";

import {
  Search,
  Bell,
  UserRound,
  ChevronDown,
  LogOut,
  X,
  CheckCheck,
} from "lucide-vue-next";

const showNotifications = ref(false);
const showProfileMenu = ref(false);
const searchQuery = ref("");

const user = {
  name: "John Doe",
  role: "Job Seeker",
  avatar: "JD",
};

const notifications = [
  {
    id: 1,
    title: "Application Update",
    message: "Your application status has been updated.",
    time: "10 minutes ago",
    unread: true,
  },
  {
    id: 2,
    title: "New Job Recommendation",
    message: "A new job matches your skills.",
    time: "1 hour ago",
    unread: true,
  },
  {
    id: 3,
    title: "Interview Reminder",
    message: "You have an upcoming interview.",
    time: "Yesterday",
    unread: false,
  },
];

const unreadCount = notifications.filter(
  (notification) => notification.unread,
).length;

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showProfileMenu.value = false;
};

const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
  showNotifications.value = false;
};

const closeMenus = () => {
  showNotifications.value = false;
  showProfileMenu.value = false;
};

const handleSearch = () => {
  if (!searchQuery.value.trim()) return;

  // Connect job search functionality here.
  console.log("Search:", searchQuery.value);
};

const handleLogout = () => {
  closeMenus();

  // Connect logout API here.
};
</script>

<template>
  <header
    class="sticky top-0 z-40 flex h-20 w-full items-center justify-between gap-4 border-b border-slate-100 bg-white/95 px-4 backdrop-blur-md sm:px-6 lg:px-8"
  >
    <!-- Search -->
    <form
      class="flex max-w-2xl flex-1 items-center"
      @submit.prevent="handleSearch"
    >
      <div
        class="flex h-12 w-full items-center gap-3 rounded-xl border border-transparent bg-[#F1F5FC] px-4 transition-all duration-200 focus-within:border-blue-300 focus-within:bg-white focus-within:shadow-sm"
      >
        <Search class="h-5 w-5 shrink-0 text-[#526B96]" />

        <input
          v-model="searchQuery"
          type="search"
          placeholder="Search for jobs, companies..."
          class="w-full border-0 bg-transparent text-sm text-[#102D59] outline-none placeholder:text-[#8292B3] focus:ring-0"
        />

        <button
          v-if="searchQuery"
          type="button"
          @click="searchQuery = ''"
          class="rounded-md p-1 text-slate-400 hover:bg-slate-200"
          aria-label="Clear search"
        >
          <X class="h-4 w-4" />
        </button>
      </div>
    </form>

    <!-- Right Actions -->
    <div class="flex shrink-0 items-center gap-3 sm:gap-5">
      <!-- Notifications -->
      <div class="relative">
        <button
          type="button"
          @click="toggleNotifications"
          :aria-expanded="showNotifications"
          aria-label="Notifications"
          class="relative flex h-11 w-11 items-center justify-center rounded-xl text-[#526B96] transition hover:bg-blue-50 hover:text-blue-600"
        >
          <Bell class="h-6 w-6" :stroke-width="1.8" />

          <span
            v-if="unreadCount"
            class="absolute right-1 top-1 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[10px] font-bold text-white"
          >
            {{ unreadCount }}
          </span>
        </button>

        <!-- Notification Dropdown -->
        <div
          v-if="showNotifications"
          class="absolute right-0 top-14 z-50 w-[min(350px,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl shadow-slate-900/10"
        >
          <div
            class="flex items-center justify-between border-b border-slate-100 px-4 py-4"
          >
            <div>
              <h3 class="text-sm font-bold text-[#102D59]">Notifications</h3>
              <p class="mt-1 text-xs text-slate-400">
                {{ unreadCount }} unread notifications
              </p>
            </div>

            <button
              type="button"
              @click="closeMenus"
              class="rounded-lg p-2 text-slate-400 hover:bg-slate-100"
              aria-label="Close notifications"
            >
              <X class="h-4 w-4" />
            </button>
          </div>

          <div class="max-h-80 overflow-y-auto">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="flex gap-3 border-b border-slate-50 px-4 py-4 transition hover:bg-blue-50/50"
            >
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"
              >
                <Bell class="h-5 w-5" />
              </div>

              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                  <p class="text-sm font-semibold text-[#102D59]">
                    {{ notification.title }}
                  </p>

                  <span
                    v-if="notification.unread"
                    class="h-2 w-2 shrink-0 rounded-full bg-blue-600"
                  ></span>
                </div>

                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                  {{ notification.message }}
                </p>

                <p class="mt-2 text-[11px] text-slate-400">
                  {{ notification.time }}
                </p>
              </div>
            </div>
          </div>

          <RouterLink
            to="/jobseeker/notifications"
            @click="closeMenus"
            class="block bg-slate-50 px-4 py-3 text-center text-xs font-semibold text-blue-600 no-underline transition hover:bg-blue-50"
          >
            View All Notifications
          </RouterLink>
        </div>
      </div>

      <!-- Vertical Divider -->
      <div class="hidden h-9 border-l border-slate-200 sm:block"></div>

      <!-- Profile -->
      <div class="relative">
        <button
          type="button"
          @click="toggleProfileMenu"
          :aria-expanded="showProfileMenu"
          aria-haspopup="true"
          class="group flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-50 sm:gap-3 sm:p-2"
        >
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-blue-100 bg-gradient-to-br from-blue-100 to-blue-200 text-sm font-bold text-blue-700"
          >
            {{ user.avatar }}
          </div>

          <div class="hidden min-w-0 text-left md:block">
            <p class="max-w-36 truncate text-sm font-semibold text-[#102D59]">
              {{ user.name }}
            </p>
            <p class="mt-0.5 text-xs text-slate-400">
              {{ user.role }}
            </p>
          </div>

          <ChevronDown
            class="hidden h-4 w-4 text-slate-400 transition-transform group-hover:text-blue-600 sm:block"
            :class="{ 'rotate-180': showProfileMenu }"
          />
        </button>

        <!-- Profile Dropdown -->
        <div
          v-if="showProfileMenu"
          class="absolute right-0 top-14 z-50 w-56 rounded-2xl border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10"
        >
          <div
            class="flex items-center gap-3 border-b border-slate-100 px-3 py-3"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
            >
              {{ user.avatar }}
            </div>

            <div class="min-w-0">
              <p class="truncate text-sm font-bold text-[#102D59]">
                {{ user.name }}
              </p>
              <p class="text-xs text-slate-400">
                {{ user.role }}
              </p>
            </div>
          </div>

          <RouterLink
            to="/jobseeker/profile"
            @click="closeMenus"
            class="mt-2 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-600 no-underline transition hover:bg-blue-50 hover:text-blue-600"
          >
            <UserRound class="h-4 w-4" />
            My Account
          </RouterLink>

          <button
            type="button"
            @click="handleLogout"
            class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-red-500 transition hover:bg-red-50"
          >
            <LogOut class="h-4 w-4" />
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
