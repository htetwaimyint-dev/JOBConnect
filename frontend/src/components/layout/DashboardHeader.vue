<script setup lang="ts">
import { computed, ref } from "vue";
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

const route = useRoute();
const router = useRouter();

const authStore = useAuthStore();
const uiStore = useUiStore();
const userStore = useUserStore();

const isProfileOpen = ref(false);
const isNotificationOpen = ref(false);
const isUserMenuOpen = ref(false);

// User Roles
const isAdmin = computed(() => authStore.user?.role === "admin");
const isEmployer = computed(() => authStore.user?.role === "employer");

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
const userName = computed(() => {
    return authStore.user?.name || "User";
});

const userInitial = computed(() => {
    return userName.value.charAt(0).toUpperCase();
});

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

// Logout
const logout = async () => {
    try {
        await authStore.logout();

        userStore.clearProfilePhoto();
        uiStore.closeSidebar();

        closeMenus();

        await router.push("/login");
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>

<template>
    <header
        class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white/95 px-2 shadow-sm backdrop-blur-md sm:px-6 lg:px-8"
    >
        <!-- Left Section -->
        <div class="flex min-w-0 shrink-0 items-center gap-4">
            <!-- Sidebar Toggle -->
            <button
                type="button"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-blue-600 lg:hidden"
                aria-label="Toggle sidebar"
                @click="uiStore.toggleSidebar()"
            >
                <Menu v-if="!uiStore.isSidebarOpen" :size="23" />
                <X v-else :size="23" />
            </button>

            <!-- Logo -->
            <RouterLink
                to="/"
                class="flex shrink-0 items-center gap-2 no-underline"
                @click="closeMenus"
            >
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm sm:h-10 sm:w-10"
                >
                    <BriefcaseBusiness :size="22" />
                </span>

                <h2
                    class="m-0 whitespace-nowrap text-sm font-bold tracking-tight text-blue-700 sm:text-xl"
                >
                    JobConnect <span class="text-slate-900">AI</span>
                </h2>
            </RouterLink>
        </div>

        <!-- Admin Navigation -->
        <nav
            v-if="isAdmin"
            class="mx-3 hidden min-w-0 flex-1 items-center justify-center gap-1 overflow-visible lg:flex"
        >
            <template v-for="item in adminNavigation" :key="item.title">
                <div v-if="item.children" class="relative">
                    <button
                        type="button"
                        class="flex shrink-0 items-center gap-1 whitespace-nowrap rounded-lg px-2 py-2 text-xs font-medium transition xl:px-3 xl:text-sm"
                        :class="
                            isUserMenuOpen ||
                            route.path.includes('/admin/companies') ||
                            route.path.includes('/admin/job-seekers')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-blue-600'
                        "
                        @click="toggleUserMenu"
                    >
                        {{ item.title }}
                        <ChevronDown
                            :size="14"
                            class="transition-transform"
                            :class="{ 'rotate-180': isUserMenuOpen }"
                        />
                    </button>
                    <div
                        v-if="isUserMenuOpen"
                        class="absolute left-1/2 top-full mt-2 w-48 -translate-x-1/2 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl"
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
                            <!-- Icon အပိုင်း -->
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

                            <!-- Text အပိုင်း -->
                            <span class="whitespace-nowrap">{{
                                child.title
                            }}</span>
                        </RouterLink>
                    </div>
                </div>

                <RouterLink
                    v-else
                    :to="item.path"
                    class="flex shrink-0 items-center whitespace-nowrap rounded-lg px-2 py-2 text-xs font-medium transition xl:px-3 xl:text-sm"
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
            class="mx-3 hidden min-w-0 flex-1 items-center justify-center gap-1 overflow-x-auto lg:flex"
        >
            <RouterLink
                v-for="item in employerNavigation"
                :key="item.path"
                :to="item.path"
                class="flex shrink-0 items-center whitespace-nowrap rounded-lg px-2 py-2 text-xs font-medium transition xl:px-3 xl:text-sm"
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
        <div class="flex shrink-0 items-center gap-2 sm:gap-4">
            <!-- Notification -->
            <div class="relative">
                <button
                    type="button"
                    aria-label="Notifications"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-blue-600"
                    @click="toggleNotifications"
                >
                    <Bell :size="21" />

                    <span
                        class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"
                    ></span>
                </button>

                <!-- Notification Dropdown -->
                <div
                    v-if="isNotificationOpen"
                    class="absolute right-0 top-12 z-50 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-xl sm:w-80"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3"
                    >
                        <h3 class="font-semibold text-slate-800">
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

                        <p class="text-sm text-slate-500">
                            No notifications yet.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:px-2"
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

                        <p class="text-xs text-slate-500">
                            {{ authStore.user?.role || "User" }}
                        </p>
                    </div>

                    <ChevronDown
                        :size="16"
                        class="hidden text-slate-500 transition sm:block"
                        :class="{ 'rotate-180': isProfileOpen }"
                    />
                </button>

                <!-- Profile Dropdown Menu -->
                <div
                    v-if="isProfileOpen"
                    class="absolute right-0 top-14 z-50 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-xl"
                >
                    <RouterLink
                        :to="profilePath"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-blue-600"
                        @click="isProfileOpen = false"
                    >
                        <UserRound :size="18" />
                        My Profile
                    </RouterLink>

                    <RouterLink
                        :to="settingsPath"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-blue-600"
                        @click="isProfileOpen = false"
                    >
                        <Settings :size="18" />
                        Settings
                    </RouterLink>

                    <div class="my-1 border-t border-slate-100"></div>

                    <button
                        type="button"
                        class="flex w-full items-center gap-3 px-4 py-3 text-sm text-red-600 transition hover:bg-red-50"
                        @click="logout"
                    >
                        <LogOut :size="18" />
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>
