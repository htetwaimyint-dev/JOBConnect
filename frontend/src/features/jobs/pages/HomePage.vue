<script setup lang="ts">
import { computed, ref } from "vue";
import { useRouter, RouterLink } from "vue-router";

import { useCategories } from "../composables/useCategories";
import { useJobs } from "@/features/jobs/composables/useJobs";
import JobCard from "@/features/jobs/components/JobCard.vue";
import { useAuthStore } from "@/stores/auth.store";

const router = useRouter();
const searchQuery = ref("");

const authStore = useAuthStore();

// Role-based Get Started navigation
const getStartedPath = computed(() => {
    if (!authStore.isAuthenticated) return "/register";

    switch (authStore.user?.role) {
        case "job_seeker":
            return "/jobseeker/dashboard";
        case "employer":
            return "/employer/dashboard";
        case "admin":
            return "/admin/dashboard";
        default:
            return "/";
    }
});

const getStartedText = computed(() =>
    authStore.isAuthenticated ? "Go to Dashboard" : "Get Started"
);

// Categories
const { getPublicCategories } = useCategories();

const {
    data: categoryResponse,
    isLoading,
    isError,
} = getPublicCategories();

// Latest Jobs
const { getJobs } = useJobs();

const {
    data: jobResponse,
    isLoading: jobsLoading,
    isError: jobsError,
} = getJobs({ page: 1 });

// Search Jobs
const findJobs = () => {
    router.push({
        path: "/jobs",
        query: searchQuery.value.trim()
            ? { search: searchQuery.value.trim() }
            : {},
    });
};

// Search by Category
const findJobsByCategory = (categoryId: number) => {
    router.push({
        path: "/jobs",
        query: { category_id: String(categoryId) },
    });
};

// Category Logo URL
const getCategoryLogoUrl = (logo: string | null): string => {
    if (!logo) return "";
    return `http://127.0.0.1:8000/storage/${logo}`;
};
</script>

<template>
    <main class="overflow-hidden">

        <!-- ====================================== -->
        <!-- HERO SECTION -->
        <!-- ====================================== -->
        <section class="relative isolate overflow-hidden bg-[#102D59]">

            <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute -bottom-40 -left-32 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl">
            </div>

            <div
                class="relative mx-auto grid max-w-[1400px] items-center gap-12 px-6 py-20 sm:px-10 lg:grid-cols-2 lg:px-16 lg:py-28">

                <!-- Hero Content -->
                <div class="text-center lg:text-left">

                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-blue-300/30 bg-white/10 px-4 py-2 text-sm font-semibold text-blue-100">
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        Your Career Journey Starts Here
                    </span>

                    <h1
                        class="mt-7 text-4xl leading-tight font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Find Your
                        <span class="text-cyan-300">Dream Job</span>
                        <br />
                        Build Your Future
                    </h1>

                    <p class="mx-auto mt-6 max-w-xl text-base leading-8 text-blue-100 sm:text-lg lg:mx-0">
                        Discover opportunities that match your skills, passion, and career goals.
                        Let JobConnect AI help you find the right path to success.
                    </p>

                    <!-- Search -->
                    <form
                        class="mx-auto mt-9 flex max-w-xl flex-col gap-3 rounded-2xl border border-white/50 bg-white p-3 shadow-2xl sm:flex-row lg:mx-0"
                        @submit.prevent="findJobs">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                            <svg class="h-5 w-5 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>

                            <input v-model="searchQuery" type="text" placeholder="Job title or keyword..."
                                class="h-12 w-full min-w-0 border-0 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0" />
                        </div>

                        <button type="submit"
                            class="h-12 rounded-xl bg-blue-600 px-7 font-bold text-white shadow-md transition duration-300 hover:bg-blue-700 active:scale-95">
                            Find Jobs
                        </button>
                    </form>

                    <!-- Hero Features -->
                    <div
                        class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm font-medium text-blue-100 lg:justify-start">
                        <span>✓ Easy Job Search</span>
                        <span>✓ AI-Powered Matching</span>
                        <span>✓ Career Opportunities</span>
                    </div>
                </div>

                <!-- Hero Visual -->
                <div class="relative mx-auto w-full max-w-lg">

                    <div class="absolute -inset-5 rounded-[2rem] bg-blue-400/10 blur-2xl"></div>

                    <div
                        class="relative rounded-3xl border border-white/20 bg-white/10 p-5 shadow-2xl backdrop-blur-md sm:p-7">

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100">Career Opportunities</p>
                                <h2 class="mt-1 text-2xl font-bold text-white">Your Next Chapter</h2>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-300/20 text-2xl">
                                ✨
                            </div>
                        </div>

                        <div class="mt-7 space-y-4">

                            <div class="rounded-2xl border border-white/60 bg-white p-5 shadow-lg">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-xl">
                                        💻</div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-[#102D59]">Software Developer</h3>
                                        <p class="mt-1 text-sm text-slate-500">Technology · Full Time</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">New</span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/60 bg-white p-5 shadow-lg">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-xl">
                                        🎨</div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-[#102D59]">UI/UX Designer</h3>
                                        <p class="mt-1 text-sm text-slate-500">Design · Remote</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">Popular</span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/60 bg-white p-5 shadow-lg">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl">
                                        📊</div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-[#102D59]">Marketing Specialist</h3>
                                        <p class="mt-1 text-sm text-slate-500">Marketing · Hybrid</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">Featured</span>
                                </div>
                            </div>

                        </div>

                        <p class="mt-5 text-center text-xs text-blue-100">
                            Find opportunities that move your career forward.
                        </p>
                    </div>
                </div>
            </div>
        </section>


        <!-- ====================================== -->
        <!-- POPULAR CATEGORIES -->
        <!-- ====================================== -->
        <section class="relative overflow-hidden bg-white py-20 sm:py-24">

            <div class="pointer-events-none absolute -left-32 top-10 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-cyan-100/50 blur-3xl">
            </div>

            <div class="relative mx-auto max-w-[1400px] px-6 sm:px-10 lg:px-16">

                <div class="mb-14 flex flex-col items-center text-center">

                    <span
                        class="rounded-full border border-blue-100 bg-blue-50 px-5 py-2 text-xs font-extrabold uppercase tracking-[0.18em] text-blue-700">
                        Explore Opportunities
                    </span>

                    <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-[#102D59] sm:text-4xl lg:text-5xl">
                        Find Jobs by
                        <span class="text-blue-600">Category</span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        Discover career opportunities across different industries.
                        Choose your preferred category and take the next step in your career.
                    </p>

                    <div class="mt-6 h-1.5 w-20 rounded-full bg-gradient-to-r from-blue-600 to-cyan-400"></div>
                </div>

                <!-- Loading -->
                <div v-if="isLoading" class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                    <div v-for="item in 8" :key="item" class="h-52 animate-pulse rounded-3xl bg-slate-100"></div>
                </div>

                <!-- Error -->
                <div v-else-if="isError"
                    class="rounded-3xl border border-red-100 bg-red-50/70 p-10 text-center text-red-600">
                    Unable to load categories. Please try again later.
                </div>

                <!-- Category Cards -->
                <div v-else-if="categoryResponse?.data?.length"
                    class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">

                    <button v-for="category in categoryResponse.data" :key="category.id" type="button"
                        class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 text-left shadow-[0_8px_30px_rgba(16,45,89,0.05)] transition-all duration-300 hover:-translate-y-2 hover:border-blue-200 hover:shadow-[0_20px_45px_rgba(37,99,235,0.12)] sm:p-7"
                        @click="findJobsByCategory(category.id)">
                        <div
                            class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-blue-50 transition-all duration-500 group-hover:scale-150 group-hover:bg-cyan-50">
                        </div>

                        <div
                            class="relative flex h-[76px] w-[76px] items-center justify-center overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 shadow-sm transition duration-300 group-hover:scale-110">
                            <img v-if="category.logo" :src="getCategoryLogoUrl(category.logo)" :alt="category.name"
                                class="h-full w-full object-cover" />

                            <span v-else class="text-3xl font-bold text-blue-600">
                                {{ category.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <h3
                            class="relative mt-6 text-base font-extrabold leading-snug text-[#102D59] transition-colors group-hover:text-blue-600 sm:text-lg">
                            {{ category.name }}
                        </h3>

                        <div class="relative mt-4 flex items-center justify-between">
                            <span
                                class="text-sm font-medium text-slate-500 transition-colors group-hover:text-blue-600">
                                Explore jobs
                            </span>

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-lg text-blue-600 transition-all duration-300 group-hover:translate-x-1 group-hover:bg-blue-600 group-hover:text-white">
                                →
                            </span>
                        </div>
                    </button>
                </div>

                <!-- Empty -->
                <div v-else class="rounded-3xl border border-slate-100 bg-slate-50 p-12 text-center">
                    <div class="text-4xl">📂</div>
                    <p class="mt-4 font-semibold text-slate-600">No categories available yet.</p>
                </div>

            </div>
        </section>


        <!-- ====================================== -->
        <!-- LATEST JOBS -->
        <!-- ====================================== -->
        <section class="relative isolate overflow-hidden bg-[#F1F5F9] py-20 sm:py-24">

            <div class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-blue-200/40 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -right-40 bottom-0 h-96 w-96 rounded-full bg-cyan-100/60 blur-3xl">
            </div>

            <div class="relative mx-auto max-w-[1400px] px-6 sm:px-10 lg:px-16">

                <div class="mb-14 flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">

                    <div class="max-w-2xl">
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-[0.18em] text-blue-700 shadow-sm">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                            Recent Opportunities
                        </span>

                        <h2
                            class="mt-6 text-3xl font-black leading-tight tracking-tight text-[#102D59] sm:text-4xl lg:text-5xl">
                            Discover Your Next
                            <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                                Career Move
                            </span>
                        </h2>

                        <p class="mt-5 max-w-xl text-base leading-8 text-slate-600 sm:text-lg">
                            Explore exciting opportunities from trusted companies.
                            Find a job that matches your skills, experience, and ambitions.
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <span
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm">
                                💼 Career Opportunities
                            </span>

                            <span
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm">
                                ✨ Latest Listings
                            </span>
                        </div>
                    </div>

                    <RouterLink to="/jobs"
                        class="group inline-flex shrink-0 items-center justify-center gap-3 self-start rounded-2xl bg-[#102D59] px-7 py-4 text-sm font-bold text-white shadow-lg shadow-blue-950/10 transition-all duration-300 hover:-translate-y-1 hover:bg-blue-700 hover:shadow-xl lg:self-auto">
                        Explore All Jobs
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 transition-transform group-hover:translate-x-1">→</span>
                    </RouterLink>
                </div>

                <!-- Loading -->
                <div v-if="jobsLoading" class="grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="item in 6" :key="item"
                        class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 animate-pulse rounded-2xl bg-slate-200"></div>
                            <div class="flex-1 space-y-3">
                                <div class="h-4 w-3/4 animate-pulse rounded bg-slate-200"></div>
                                <div class="h-3 w-1/2 animate-pulse rounded bg-slate-100"></div>
                            </div>
                        </div>
                        <div class="mt-8 space-y-4">
                            <div class="h-4 w-full animate-pulse rounded bg-slate-100"></div>
                            <div class="h-4 w-2/3 animate-pulse rounded bg-slate-100"></div>
                            <div class="h-10 w-full animate-pulse rounded-xl bg-slate-100"></div>
                        </div>
                    </div>
                </div>

                <!-- Error -->
                <div v-else-if="jobsError"
                    class="rounded-3xl border border-red-100 bg-white px-6 py-16 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-3xl">⚠️
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-slate-800">Unable to Load Jobs</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Something went wrong while loading the latest opportunities. Please try again later.
                    </p>
                </div>

                <!-- Jobs -->
                <div v-else-if="jobResponse?.data?.length" class="grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(job, index) in jobResponse.data.slice(0, 6)" :key="job.id" class="relative">
                        <span
                            class="pointer-events-none absolute -right-2 -top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full border-4 border-[#F1F5F9] bg-gradient-to-br from-blue-600 to-cyan-500 text-xs font-extrabold text-white shadow-lg">
                            {{ String(index + 1).padStart(2, "0") }}
                        </span>

                        <div
                            class="h-full rounded-3xl transition-all duration-300 hover:-translate-y-2 hover:drop-shadow-xl">
                            <JobCard :job="job" />
                        </div>
                    </div>
                </div>

                <!-- Empty -->
                <div v-else
                    class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white px-6 py-20 text-center shadow-sm">
                    <div
                        class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-50 to-cyan-50 text-4xl">
                        💼
                    </div>

                    <h3 class="mt-7 text-2xl font-extrabold text-[#102D59]">
                        New Opportunities Are Coming!
                    </h3>

                    <p class="mx-auto mt-3 max-w-lg leading-7 text-slate-500">
                        There are no job listings available right now.
                        Check back soon to discover exciting career opportunities.
                    </p>

                    <RouterLink to="/jobs"
                        class="mt-8 inline-flex items-center gap-3 rounded-xl bg-[#102D59] px-7 py-3.5 font-bold text-white shadow-lg transition hover:-translate-y-1 hover:bg-blue-600">
                        Browse Jobs <span>→</span>
                    </RouterLink>
                </div>

                <!-- Bottom CTA -->
                <div
                    class="mt-14 flex flex-col items-center justify-between gap-5 rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:flex-row sm:px-10">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 text-2xl text-white shadow-lg">
                            🚀
                        </div>

                        <div>
                            <h3 class="font-extrabold text-[#102D59]">Your next opportunity is waiting.</h3>
                            <p class="mt-1 text-sm text-slate-500">Discover more jobs and take the next step.</p>
                        </div>
                    </div>

                    <RouterLink to="/jobs"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-6 py-3 font-bold text-blue-700 transition duration-300 hover:border-blue-600 hover:bg-blue-600 hover:text-white">
                        View All Jobs <span>→</span>
                    </RouterLink>
                </div>

            </div>
        </section>


        <!-- ====================================== -->
        <!-- WHY CHOOSE JOBCONNECT -->
        <!-- ====================================== -->
        <section class="relative isolate overflow-hidden bg-[#102D59] py-20 sm:py-24">

            <div class="pointer-events-none absolute -left-32 top-10 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl">
            </div>

            <div class="relative mx-auto max-w-[1400px] px-6 sm:px-10 lg:px-16">

                <div class="mx-auto mb-14 max-w-3xl text-center">

                    <span
                        class="inline-flex rounded-full border border-white/15 bg-white/10 px-5 py-2 text-xs font-bold uppercase tracking-[0.18em] text-cyan-200">
                        Why JobConnect
                    </span>

                    <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Your Career,
                        <span class="text-cyan-300">Our Priority</span>
                    </h2>

                    <p class="mt-5 text-base leading-8 text-blue-100 sm:text-lg">
                        We make your job search easier with smart technology,
                        trusted companies, and a simple application experience.
                    </p>

                    <div class="mx-auto mt-6 h-1.5 w-20 rounded-full bg-gradient-to-r from-blue-400 to-cyan-300"></div>
                </div>

                <div class="grid grid-cols-1 gap-7 md:grid-cols-3">

                    <div
                        class="group rounded-3xl border border-white/10 bg-white/[0.07] p-8 text-center shadow-xl backdrop-blur-sm transition duration-300 hover:-translate-y-2 hover:border-cyan-300/30 hover:bg-white/[0.12]">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-cyan-200/20 bg-cyan-300/10 text-4xl transition duration-300 group-hover:scale-110">
                            🤖
                        </div>

                        <h3 class="mt-7 text-xl font-bold text-white">AI-Powered Job Matching</h3>
                        <p class="mt-4 leading-7 text-blue-100">
                            Get smarter job recommendations based on your skills
                            and job requirements to discover relevant opportunities.
                        </p>
                    </div>

                    <div
                        class="group rounded-3xl border border-white/10 bg-white/[0.07] p-8 text-center shadow-xl backdrop-blur-sm transition duration-300 hover:-translate-y-2 hover:border-cyan-300/30 hover:bg-white/[0.12]">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-cyan-200/20 bg-cyan-300/10 text-4xl transition duration-300 group-hover:scale-110">
                            🏢
                        </div>

                        <h3 class="mt-7 text-xl font-bold text-white">Verified Companies</h3>
                        <p class="mt-4 leading-7 text-blue-100">
                            Explore job opportunities from companies reviewed
                            and approved by the JobConnect administration.
                        </p>
                    </div>

                    <div
                        class="group rounded-3xl border border-white/10 bg-white/[0.07] p-8 text-center shadow-xl backdrop-blur-sm transition duration-300 hover:-translate-y-2 hover:border-cyan-300/30 hover:bg-white/[0.12]">
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-cyan-200/20 bg-cyan-300/10 text-4xl transition duration-300 group-hover:scale-110">
                            🚀
                        </div>

                        <h3 class="mt-7 text-xl font-bold text-white">Easy Application Process</h3>
                        <p class="mt-4 leading-7 text-blue-100">
                            Find suitable jobs, submit your applications,
                            and keep track of your application progress in one place.
                        </p>
                    </div>

                </div>
            </div>
        </section>


        <!-- ====================================== -->
        <!-- CALL TO ACTION -->
        <!-- ====================================== -->
        <section
            class="relative isolate overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-[#2563EB] py-20 sm:py-24">

            <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl">
            </div>
            <div
                class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-cyan-300/20 blur-3xl">
            </div>

            <div class="relative mx-auto max-w-4xl px-6 text-center sm:px-10">

                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-3xl shadow-lg">
                    🚀
                </div>

                <h2
                    class="mt-7 text-3xl leading-tight font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Ready to Find Your
                    <span class="text-cyan-200">Dream Job?</span>
                </h2>

                <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-blue-50 sm:text-lg">
                    Create your free account today, discover exciting opportunities,
                    and take the next step toward building your future career.
                </p>

                <div class="mt-9 flex flex-col items-center justify-center gap-4 sm:flex-row">

                    <RouterLink :to="getStartedPath"
                        class="group inline-flex min-w-52 items-center justify-center gap-3 rounded-2xl bg-white px-9 py-4 text-base font-extrabold text-[#102D59] shadow-xl shadow-blue-950/20 ring-1 ring-white/50 transition-all duration-300 hover:-translate-y-1 hover:scale-[1.03] hover:bg-cyan-50 hover:shadow-2xl active:scale-95">
                        <span>{{ getStartedText }}</span>

                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-blue-700 transition-all duration-300 group-hover:translate-x-1 group-hover:bg-blue-600 group-hover:text-white">
                            →
                        </span>
                    </RouterLink>

                    <RouterLink to="/jobs"
                        class="inline-flex min-w-48 items-center justify-center rounded-2xl border border-white/40 bg-white/10 px-8 py-4 font-bold text-white transition duration-300 hover:-translate-y-1 hover:bg-white/20">
                        Explore Jobs
                    </RouterLink>
                </div>

                <p class="mt-7 text-sm font-medium text-blue-100">
                    Start your career journey with JobConnect today.
                </p>

            </div>
        </section>

    </main>
</template>
