<script setup lang="ts">
import { computed, ref, watch, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";

import {
    Menu,
    Bell,
    UserRound,
    BriefcaseBusiness,
    Settings,
    LogOut,
    ChevronDown,
    X,
    Building2,
    UserSearch,
} from "lucide-vue-next";

import { useAuthStore } from "@/stores/auth.store";
import { useUiStore } from "@/stores/ui.store";
import { useUserStore } from "@/stores/user.store";
import NotificationBell from "@/features/notifications/components/NotificationBell.vue";

const route = useRoute();
const router = useRouter();

const authStore = useAuthStore();
const uiStore = useUiStore();
const userStore = useUserStore();

// Dropdown States
const isProfileOpen = ref(false);
const isNotificationOpen = ref(false);
const isUserMenuOpen = ref(false);

// Profile Dropdown Reference
const profileDropdownRef = ref<HTMLElement | null>(null);

// User Roles
const isAdmin = computed(() => authStore.user?.role === "admin");
const isEmployer = computed(() => authStore.user?.role === "employer");

// Active Navigation
const activeMenuPath = computed(() => {
    if (route.name === "jobdetail") {
        const from = route.query.from;

        if (from === "company-profile") {
            return isAdmin.value ? "/admin/companies" : "/employer/profile";
        }

        if (from === "job-post") {
            return "/employer/jobs";
        }

        if (from === "job-approval") {
            return "/admin/job-approvals";
        }
    }

    return route.path;
});

const isMenuActive = (path: string) => {
    return activeMenuPath.value === path;
};

// Admin Navigation
const adminNavigation = [
    {
        title: "Dashboard",
        path: "/admin/dashboard",
    },
    {
        title: "User",
        path: "#",
        children: [
            { title: "Companies", path: "/admin/companies" },
            { title: "Job Seekers", path: "/admin/job-seekers" },
        ],
    },
    {
        title: "Company Verify",
        path: "/admin/company-approvals",
    },
    {
        title: "Job Post Approval",
        path: "/admin/job-approvals",
    },
    {
        title: "Category",
        path: "/admin/categories",
    },
    {
        title: "Report",
        path: "/admin/reports",
    },
];

// Employer Navigation
const employerNavigation = [
    {
        title: "Dashboard",
        path: "/employer/dashboard",
    },
    {
        title: "Job Post",
        path: "/employer/jobs",
    },
    {
        title: "Applicants",
        path: "/employer/applicants",
    },
    {
        title: "Comany Profile",
        path: "/employer/profile",
    },
    {
        title: "Job Seeker Profile",
        path: "/employer/job-seekers",
    },
    {
        title: "Direct Offer",
        path: "/employer/job-offers",
    },
    {
        title: "Interviews",
        path: "/employer/interviews",
    },
];

// User Information
const userName = computed(() => authStore.user?.name || "User");

const userInitial = computed(
    () => userName.value.trim().charAt(0).toUpperCase() || "U",
);

const profilePhoto = computed(() => userStore.profilePhoto);

// Profile Paths
const profilePath = computed(() => {
    if (authStore.user?.role === "employer") {
        return "/employer/profile";
    }

    if (authStore.user?.role === "admin") {
        return "/admin/dashboard";
    }

    return "/jobseeker/profile";
});

const settingsPath = computed(() => {
    const role = authStore.user?.role;

    if (role === "employer") return "/employer/settings";
    if (role === "admin") return "/admin/settings";

    return "/jobseeker/settings";
});

// Close Dropdowns
const closeMenus = () => {
    isProfileOpen.value = false;
    isNotificationOpen.value = false;
    isUserMenuOpen.value = false;
};

// Toggle Profile
const toggleProfile = () => {
    isProfileOpen.value = !isProfileOpen.value;
    isNotificationOpen.value = false;
    isUserMenuOpen.value = false;
};

// Toggle User Submenu
const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
    isProfileOpen.value = false;
    isNotificationOpen.value = false;
};

// Toggle Notifications
const toggleNotifications = () => {
    isNotificationOpen.value = !isNotificationOpen.value;
    isProfileOpen.value = false;
    isUserMenuOpen.value = false;
};

// Outside Click
const handleOutsideClick = (event: PointerEvent) => {
    const target = event.target as Node;

    if (
        profileDropdownRef.value &&
        !profileDropdownRef.value.contains(target)
    ) {
        isProfileOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("pointerdown", handleOutsideClick);
});

onBeforeUnmount(() => {
    document.removeEventListener("pointerdown", handleOutsideClick);
});

// Close Dropdowns When Route Changes
watch(
    () => route.fullPath,
    () => {
        closeMenus();
    },
);

// Logout
const logout = async () => {
    try {
        closeMenus();

        await authStore.logout();

        userStore.clearProfilePhoto();
        uiStore.closeSidebar();

        await router.push("/login");
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>

<template>
    <header
        class="sticky top-0 z-30 flex h-16 w-full items-center border-b border-slate-200 bg-white/95 px-3 shadow-sm backdrop-blur-md sm:px-5 xl:px-8"
    >
        <!-- Left Section -->
        <div class="flex shrink-0 items-center gap-3 xl:gap-4">
            <!-- Sidebar Toggle -->
            <button
                type="button"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-blue-600 xl:hidden"
                aria-label="Toggle sidebar"
                :aria-expanded="uiStore.isSidebarOpen"
                @click="uiStore.toggleSidebar()"
            >
                <Menu v-if="!uiStore.isSidebarOpen" :size="22" />
                <X v-else :size="22" />
            </button>

            <!-- Logo -->
            <RouterLink
                to="/"
                class="flex shrink-0 items-center gap-1 no-underline"
                @click="closeMenus"
            >
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm sm:h-10 sm:w-10"
                >
                    <BriefcaseBusiness :size="21" />
                </span>

                <h2
                    class="m-0 whitespace-nowrap text-lg font-bold tracking-tight text-blue-600 sm:text-lg"
                >
                    JobConnect
                </h2>
            </RouterLink>
        </div>

        <!-- Admin Navigation -->
        <nav
            v-if="isAdmin"
            class="mx-2 hidden min-w-0 flex-1 items-center justify-center gap-2 xl:flex 2xl:mx-3"
        >
            <template v-for="item in adminNavigation" :key="item.title">
                <div v-if="item.children" class="relative">
                    <button
                        type="button"
                        class="nav-link flex items-center gap-1"
                        :class="
                            isUserMenuOpen ||
                            route.path.includes('/admin/companies') ||
                            route.path.includes('/admin/job-seekers')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600'
                        "
                        :aria-expanded="isUserMenuOpen"
                        @click="toggleUserMenu"
                    >
                        {{ item.title }}

                        <ChevronDown
                            :size="14"
                            class="transition-transform"
                            :class="{ 'rotate-180': isUserMenuOpen }"
                        />
                    </button>

                    <!-- User Dropdown -->
                    <Transition name="dropdown">
                        <div
                            v-if="isUserMenuOpen"
                            class="absolute left-1/2 top-full z-50 mt-2 w-48 -translate-x-1/2 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl"
                        >
                            <RouterLink
                                v-for="child in item.children"
                                :key="child.path"
                                :to="child.path"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-blue-600"
                                :class="{
                                    'bg-blue-50 text-blue-700':
                                        route.path === child.path,
                                }"
                                @click="closeMenus"
                            >
                                <Building2
                                    v-if="child.title === 'Companies'"
                                    :size="18"
                                    class="shrink-0 text-slate-400"
                                />

                                <UserSearch
                                    v-else-if="child.title === 'Job Seekers'"
                                    :size="18"
                                    class="shrink-0 text-slate-400"
                                />

                                <span class="whitespace-nowrap">
                                    {{ child.title }}
                                </span>
                            </RouterLink>
                        </div>
                    </Transition>
                </div>

                <!-- Admin Normal Links -->
                <RouterLink
                    v-else
                    :to="item.path"
                    class="nav-link"
                    :class="
                        route.path === item.path
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600'
                    "
                    @click="closeMenus"
                >
                    {{ item.title }}
                </RouterLink>
            </template>
        </nav>

        <!-- Employer Navigation -->
        <nav
            v-if="isEmployer"
            class="mx-2 hidden min-w-0 flex-1 items-center justify-center gap-2 xl:flex 2xl:mx-3"
        >
            <RouterLink
                v-for="item in employerNavigation"
                :key="item.path"
                :to="item.path"
                class="nav-link"
                :class="
                    route.path === item.path
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600'
                "
                @click="closeMenus"
            >
                {{ item.title }}
            </RouterLink>
        </nav>

        <!-- Right Section -->
        <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-4">
            <!-- Notification -->
            <div class="relative">
                <!-- <button
          type="button"
          aria-label="Notifications"
          :aria-expanded="isNotificationOpen"
          class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-blue-600"
          @click="toggleNotifications"
        >
          <Bell :size="21" />
        </button>

      
        <Transition name="dropdown">
          <div
            v-if="isNotificationOpen"
            class="absolute right-0 top-12 z-50 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-xl sm:w-80"
          >
            <div
              class="flex items-center justify-between border-b border-slate-100 pb-3"
            >
              <h3 class="text-sm font-semibold text-slate-800">
                Notifications
              </h3>

              <button
                type="button"
                aria-label="Close notifications"
                class="text-slate-400 hover:text-slate-700"
                @click="isNotificationOpen = false"
              >
                <X :size="18" />
              </button>
            </div>

            <div class="py-6 text-center">
              <Bell :size="28" class="mx-auto mb-2 text-slate-300" />
              <p class="text-sm text-slate-500">No notifications yet.</p>
            </div>
          </div>
        </Transition> -->
                <NotificationBell />
            </div>

            <!-- Profile Dropdown -->
            <div ref="profileDropdownRef" class="relative">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:px-2"
                    :aria-expanded="isProfileOpen"
                    aria-haspopup="true"
                    aria-label="Open profile menu"
                    @click="toggleProfile"
                >
                    <img
                        v-if="profilePhoto"
                        :src="profilePhoto"
                        alt="Profile"
                        class="h-9 w-9 rounded-full object-cover"
                    />

                    <div
                        v-else
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-700"
                    >
                        {{ userInitial }}
                    </div>

                    <div class="hidden text-left sm:block">
                        <p
                            class="max-w-32 truncate text-sm font-semibold text-slate-800"
                        >
                            {{ userName }}
                        </p>

                        <p class="text-xs capitalize text-slate-500">
                            {{
                                authStore.user?.role?.replace(/_/g, " ") ||
                                "User"
                            }}
                        </p>
                    </div>

                    <ChevronDown
                        :size="16"
                        class="hidden text-slate-500 transition sm:block"
                        :class="{ 'rotate-180': isProfileOpen }"
                    />
                </button>

                <!-- Profile Dropdown Menu -->
                <Transition name="dropdown">
                    <div
                        v-if="isProfileOpen"
                        class="absolute right-0 top-14 z-50 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
                    >
                        <div class="border-b border-slate-100 px-3 py-3">
                            <p
                                class="truncate text-sm font-semibold text-slate-800"
                            >
                                {{ userName }}
                            </p>

                            <p class="mt-1 text-xs capitalize text-slate-500">
                                {{
                                    authStore.user?.role?.replace(/_/g, " ") ||
                                    "User"
                                }}
                            </p>
                        </div>

                        <RouterLink
                            :to="profilePath"
                            class="dropdown-link"
                            @click="closeMenus"
                        >
                            <UserRound :size="17" />
                            My Profile
                        </RouterLink>

                        <RouterLink
                            :to="settingsPath"
                            class="dropdown-link"
                            @click="closeMenus"
                        >
                            <Settings :size="17" />
                            Settings
                        </RouterLink>

                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm text-red-600 transition hover:bg-red-50"
                            @click="logout"
                        >
                            <LogOut :size="17" />
                            Logout
                        </button>
                    </div>
                </Transition>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Shared Desktop Navigation Style */
.nav-link {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    white-space: nowrap;
    transition:
        background-color 0.2s ease,
        color 0.2s ease;
}

/* Dropdown Links */
.dropdown-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 8px;
    color: #334155;
    font-size: 14px;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.dropdown-link:hover {
    background-color: #f1f5f9;
}

/* Dropdown Transition */
.dropdown-enter-active,
.dropdown-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
