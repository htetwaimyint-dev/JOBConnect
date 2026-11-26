<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import {
    ArrowLeft,
    BriefcaseBusiness,
    ChevronRight,
    Clock3,
    Home,
    LoaderCircle,
    MapPin,
    Sparkles,
    TriangleAlert,
    Users,
} from "lucide-vue-next";

import { useJobs } from "../composables/useJobs";
import JobDetailContent from "../components/JobDetailContent.vue";
import { useAuthStore } from "@/stores/auth.store";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const jobId = computed(() => String(route.params.id));

const { getJobById } = useJobs();

const { data: job, isLoading, isError } = getJobById(jobId);

const showApplicationForm = ref(false);
const applyError = ref("");
const applicationSuccess = ref(false);

const isEmployer = computed(() => authStore.user?.role === "employer");

// Format job location using the actual Job interface fields.
const jobLocation = computed(() => {
    if (!job.value) return "Location not specified";

    return (
        [job.value.city, job.value.country].filter(Boolean).join(", ") ||
        "Location not specified"
    );
});

// Temporary example profiles.
// Replace with the AI recommendation API later.
const recommendedProfiles = [
    {
        id: 1,
        name: "Alex Morgan",
        title: "Frontend Developer",
        experience: "3 Years Experience",
        skills: ["Vue.js", "TypeScript", "Tailwind CSS"],
        match: 95,
        initials: "AM",
        color: "from-blue-500 to-indigo-600",
    },
    {
        id: 2,
        name: "Sarah Johnson",
        title: "Full Stack Developer",
        experience: "2 Years Experience",
        skills: ["Laravel", "Vue.js", "MySQL"],
        match: 89,
        initials: "SJ",
        color: "from-emerald-500 to-teal-600",
    },
    {
        id: 3,
        name: "Daniel Smith",
        title: "Web Developer",
        experience: "4 Years Experience",
        skills: ["PHP", "JavaScript", "React"],
        match: 84,
        initials: "DS",
        color: "from-purple-500 to-violet-600",
    },
];

function goBack() {
    if (authStore.user?.role === "employer") {
        router.push({ name: "EmployerJobs" });
        return;
    }

    if (window.history.state?.back) {
        router.back();
        return;
    }

    router.push({ name: "home" });
}

function applyForJob() {
    applyError.value = "";
    applicationSuccess.value = false;

    if (!authStore.isAuthenticated) {
        router.push({ name: "login" });
        return;
    }

    if (authStore.user?.role !== "job_seeker") {
        applyError.value = "Only job seekers can apply for this job.";
        return;
    }

    showApplicationForm.value = true;
}

function handleApplicationSubmitted() {
    showApplicationForm.value = false;
    applicationSuccess.value = true;
}

function editJob() {
    if (job.value?.status === "published" || job.value?.status === "closed") {
        window.alert(
            "This job cannot be edited because it is published or closed.",
        );
        return;
    }

    router.push({
        name: "employer-job-edit",
        params: { id: job.value?.id },
    });
}
</script>

<template>
    <main class="min-h-screen bg-[#f3f6fb]">
        <!-- Loading State -->
        <div
            v-if="isLoading"
            class="flex min-h-screen flex-col items-center justify-center gap-4"
        >
            <div
                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-lg shadow-blue-100"
            >
                <LoaderCircle class="h-8 w-8 animate-spin text-blue-600" />
            </div>

            <div class="text-center">
                <p class="font-bold text-slate-800">Loading job details</p>

                <p class="mt-1 text-sm text-slate-400">
                    Please wait while we load the information.
                </p>
            </div>
        </div>

        <!-- Error State -->
        <div
            v-else-if="isError || !job"
            class="flex min-h-screen items-center justify-center px-4"
        >
            <div
                class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl shadow-slate-200/60"
            >
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50"
                >
                    <TriangleAlert class="h-8 w-8 text-red-500" />
                </div>

                <h2 class="mt-5 text-2xl font-extrabold text-slate-900">
                    Job Not Found
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    This job may have been removed or is no longer available.
                    Please return and explore other opportunities.
                </p>

                <button
                    type="button"
                    @click="goBack"
                    class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700 active:scale-[0.98]"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Jobs
                </button>
            </div>
        </div>

        <!-- Job Detail Page -->
        <div
            v-else
            class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 lg:px-10 lg:py-9"
        >
            <!-- Top Navigation -->
            <div class="mb-7 flex flex-wrap items-center justify-between gap-4">
                <button
                    type="button"
                    @click="goBack"
                    class="group inline-flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-700 hover:shadow-md"
                >
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 transition group-hover:bg-blue-50"
                    >
                        <ArrowLeft
                            class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        />
                    </span>

                    Back to Jobs
                </button>

                <nav
                    aria-label="Breadcrumb"
                    class="flex items-center gap-2 text-xs font-medium text-slate-400 sm:text-sm"
                >
                    <Home class="h-4 w-4" />

                    <span>Home</span>

                    <ChevronRight class="h-3.5 w-3.5" />

                    <span>Jobs</span>

                    <ChevronRight class="h-3.5 w-3.5" />

                    <span class="font-bold text-slate-700"> Job Details </span>
                </nav>
            </div>

            <!-- Job Hero Card -->
            <section
                class="relative mb-7 overflow-hidden rounded-[28px] bg-gradient-to-br from-[#111d3b] via-[#172b52] to-[#244b83] p-6 shadow-xl shadow-blue-950/10 sm:p-8 lg:p-10"
            >
                <!-- Decorative Background -->
                <div
                    class="pointer-events-none absolute -right-20 -top-32 h-80 w-80 rounded-full bg-blue-400/10 blur-3xl"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-indigo-400/10 blur-3xl"
                ></div>

                <div class="relative z-10">
                    <!-- Hero Label -->
                    <div class="mb-7 flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100 backdrop-blur"
                        >
                            <BriefcaseBusiness class="h-3.5 w-3.5" />
                            Career Opportunity
                        </span>

                        <span
                            class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/80"
                        >
                            Job Details
                        </span>
                    </div>

                    <div
                        class="grid gap-7 lg:grid-cols-[1fr_auto] lg:items-end"
                    >
                        <!-- Job Information -->
                        <div>
                            <h1
                                class="max-w-3xl text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl"
                            >
                                {{ job.title }}
                            </h1>

                            <p
                                class="mt-4 max-w-2xl text-sm leading-7 text-blue-100/75 sm:text-base"
                            >
                                Explore this opportunity, learn about the
                                responsibilities, and discover whether this role
                                matches your career goals.
                            </p>

                            <div
                                class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-blue-100/90"
                            >
                                <span class="inline-flex items-center gap-2">
                                    <MapPin class="h-4 w-4 text-cyan-300" />
                                    {{ jobLocation }}
                                </span>

                                <span class="inline-flex items-center gap-2">
                                    <Clock3 class="h-4 w-4 text-cyan-300" />

                                    {{
                                        job.employment_type.replaceAll("_", " ")
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Employer Only: Job Status -->
                        <div
                            v-if="isEmployer"
                            class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-md lg:min-w-[190px]"
                        >
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white"
                            >
                                <BriefcaseBusiness class="h-6 w-6" />
                            </div>

                            <div>
                                <p class="text-xs font-medium text-blue-100/70">
                                    Job Status
                                </p>

                                <p
                                    class="mt-1 text-sm font-extrabold capitalize text-white"
                                >
                                    {{ job.status.replaceAll("_", " ") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content Grid -->
            <div
                :class="[
                    'grid grid-cols-1 items-start gap-6',
                    isEmployer
                        ? 'xl:grid-cols-[minmax(0,1fr)_340px]'
                        : 'xl:grid-cols-1',
                ]"
            >
                <!-- Left: Job Details -->
                <section class="min-w-0">
                    <div
                        class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 transition duration-300 hover:shadow-md"
                    >
                        <!-- Section Header -->
                        <div
                            class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-7"
                        >
                            <div>
                                <p
                                    class="text-xs font-bold uppercase tracking-[0.15em] text-blue-600"
                                >
                                    Job Information
                                </p>

                                <h2
                                    class="mt-1 text-lg font-extrabold text-slate-900"
                                >
                                    Position Overview
                                </h2>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                            >
                                <BriefcaseBusiness class="h-5 w-5" />
                            </div>
                        </div>

                        <!-- Existing Job Detail Content -->
                        <div class="p-5 sm:p-7">
                            <JobDetailContent
                                :job="job"
                                :category-name="job.category?.name"
                                :mode="isEmployer ? 'employer' : 'public'"
                                :show-application-form="showApplicationForm"
                                :apply-error="applyError"
                                :application-success="applicationSuccess"
                                @apply="applyForJob"
                                @edit="editJob"
                                @submitted="handleApplicationSubmitted"
                                @cancel="showApplicationForm = false"
                            />
                        </div>
                    </div>
                </section>

                <!-- Right: Employer Recommended Profiles -->
                <aside v-if="isEmployer" class="min-w-0 xl:sticky xl:top-6">
                    <div
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                    >
                        <!-- Recommendation Header -->
                        <div
                            class="border-b border-slate-100 bg-gradient-to-br from-indigo-50 via-white to-blue-50 px-5 py-5"
                        >
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600"
                                >
                                    <Users class="h-5 w-5" />
                                </div>

                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-700"
                                >
                                    <Sparkles class="h-3 w-3" />
                                    Preview
                                </span>
                            </div>

                            <h3
                                class="mt-4 text-lg font-extrabold text-slate-900"
                            >
                                Recommended Profiles
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Candidates who may match this job's
                                requirements.
                            </p>
                        </div>

                        <!-- Example Profiles -->
                        <div class="divide-y divide-slate-100">
                            <div
                                v-for="profile in recommendedProfiles"
                                :key="profile.id"
                                class="p-5 transition duration-200 hover:bg-slate-50/80"
                            >
                                <!-- Profile Information -->
                                <div class="flex items-start gap-3">
                                    <div
                                        :class="[
                                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-sm font-extrabold text-white shadow-sm',
                                            profile.color,
                                        ]"
                                    >
                                        {{ profile.initials }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h4
                                            class="truncate text-sm font-extrabold text-slate-900"
                                        >
                                            {{ profile.name }}
                                        </h4>

                                        <p
                                            class="mt-1 text-xs font-medium text-slate-500"
                                        >
                                            {{ profile.title }}
                                        </p>

                                        <p
                                            class="mt-1 text-[11px] text-slate-400"
                                        >
                                            {{ profile.experience }}
                                        </p>
                                    </div>

                                    <!-- Example Match Score -->
                                    <div
                                        class="shrink-0 rounded-lg bg-emerald-50 px-2 py-1 text-xs font-extrabold text-emerald-700"
                                    >
                                        {{ profile.match }}%
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="mt-4">
                                    <p
                                        class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Skills
                                    </p>

                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="skill in profile.skills"
                                            :key="skill"
                                            class="rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] font-semibold text-slate-600"
                                        >
                                            {{ skill }}
                                        </span>
                                    </div>
                                </div>

                                <p
                                    class="mt-3 text-[10px] font-medium text-slate-400"
                                >
                                    Example match score · Not AI-generated
                                </p>
                            </div>
                        </div>

                        <!-- Recommendation Footer -->
                        <div
                            class="border-t border-slate-100 bg-slate-50 px-5 py-4"
                        >
                            <div class="flex items-start gap-2">
                                <Sparkles
                                    class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500"
                                />

                                <p class="text-xs leading-5 text-slate-500">
                                    AI-powered candidate recommendations will be
                                    available soon.
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>
