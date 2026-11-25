<script setup lang="ts">
import { computed, onMounted } from "vue";

import {
    Send,
    Clock3,
    CalendarCheck,
    CheckCircle2,
    RefreshCw,
    Sparkles,
} from "lucide-vue-next";

import ApplicationCard from "../components/ApplicationCard.vue";
import ApplicationLoading from "../components/ApplicationLoading.vue";
import ApplicationError from "../components/ApplicationError.vue";
import ApplicationEmpty from "../components/ApplicationEmpty.vue";
import ApplicationFilters from "../components/ApplicationFilters.vue";
import ApplicationPagination from "../components/ApplicationPagination.vue";
import RecommendedJobs from "../components/RecommendedJobs.vue";
import CareerTip from "../components/CareerTip.vue";

import { useApplications } from "../composables/useApplications";
import { useRecommendations } from "@/features/jobs/composables/useRecommendations.ts";

// --------------------------------------------------
// Application Composable
// --------------------------------------------------

const {
    applications,
    loading,
    errorMessage,
    currentPage,
    selectedStatus,
    searchKeyword,
    pagination,
    filteredApplications,
    pageStats,
    fetchApplications,
    changeStatus,
    changePage,
    formatDate,
    withdrawApplication,
} = useApplications();

// --------------------------------------------------
// Recommended / More Job Opportunities
// --------------------------------------------------

const { getRecommendations } = useRecommendations();

const recommendationsQuery = getRecommendations(10);

const recommendedJobs = computed(
    () =>
        recommendationsQuery.data.value?.recommendations
            ?.map((recommendation) => recommendation.job)
            .filter((job) => job != null) ?? [],
);

const isRefreshing = computed(
    () => recommendationsQuery.data.value?.is_refreshing ?? false,
);

function refresh() {
    fetchApplications(currentPage.value);
    recommendationsQuery.refetch();
}

// --------------------------------------------------
// Initial Loading
// --------------------------------------------------

onMounted(() => {
    fetchApplications();
});
</script>

<template>
    <main class="min-h-screen bg-slate-50">
        <!-- ====================================== -->
        <!-- Page Header -->
        <!-- ====================================== -->

        <section class="relative isolate overflow-hidden bg-[#102D59]">
            <div
                class="absolute -right-20 -top-28 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"
            ></div>

            <div
                class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"
            ></div>

            <div
                class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16"
            >
                <div
                    class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end"
                >
                    <div>
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-100"
                        >
                            <Sparkles class="h-4 w-4 text-cyan-300" />
                            JobSeeker Workspace
                        </div>

                        <h1
                            class="mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl"
                        >
                            My Applications
                        </h1>

                        <p
                            class="mt-3 max-w-xl text-sm leading-7 text-blue-100/80 sm:text-base"
                        >
                            Keep track of your applications, follow your
                            progress, and move closer to your next career
                            opportunity.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ====================================== -->
        <!-- Main Content -->
        <!-- ====================================== -->

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- ================================== -->
            <!-- Statistics -->
            <!-- ================================== -->

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <!-- Total Applications -->

                <div
                    class="group rounded-2xl border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                        >
                            <Send class="h-6 w-6" />
                        </div>

                        <span
                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                        >
                            All time
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold text-slate-900">
                        {{ pagination.total }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Total Applications
                    </p>
                </div>

                <!-- Pending -->

                <div
                    class="group rounded-2xl border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
                        >
                            <Clock3 class="h-6 w-6" />
                        </div>

                        <span
                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700"
                        >
                            This page
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold text-slate-900">
                        {{ pageStats.pending }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Pending Applications
                    </p>
                </div>

                <!-- Interviews -->

                <div
                    class="group rounded-2xl border border-violet-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600"
                        >
                            <CalendarCheck class="h-6 w-6" />
                        </div>

                        <span
                            class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700"
                        >
                            This page
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold text-slate-900">
                        {{ pageStats.interviews }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Interviews
                    </p>
                </div>

                <!-- Accepted -->

                <div
                    class="group rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <CheckCircle2 class="h-6 w-6" />
                        </div>

                        <span
                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                        >
                            This page
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-extrabold text-slate-900">
                        {{ pageStats.accepted }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Accepted
                    </p>
                </div>
            </div>

            <!-- ================================== -->
            <!-- Application List + Sidebar -->
            <!-- ================================== -->

            <div
                class="mt-10 grid grid-cols-1 items-start gap-8 lg:grid-cols-3"
            >
                <!-- Main Application Section -->

                <div class="min-w-0 space-y-6 lg:col-span-2">
                    <!-- Heading -->

                    <div
                        class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
                    >
                        <div>
                            <div
                                class="flex items-center gap-2 text-sm font-semibold text-blue-600"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-blue-600"
                                ></span>
                                APPLICATION TRACKER
                            </div>

                            <h2
                                class="mt-2 text-2xl font-bold tracking-tight text-slate-900"
                            >
                                Your Application History
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Monitor the progress of your job applications.
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="loading"
                            @click="refresh"
                            class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:opacity-50"
                        >
                            <RefreshCw
                                class="h-4 w-4"
                                :class="{ 'animate-spin': loading }"
                            />
                            Refresh
                        </button>
                    </div>

                    <!-- Search and Filter -->

                    <ApplicationFilters
                        v-model:search-keyword="searchKeyword"
                        v-model:selected-status="selectedStatus"
                        :result-count="filteredApplications.length"
                        @status-change="changeStatus"
                        @clear="
                            searchKeyword = '';
                            selectedStatus = '';
                            fetchApplications(1);
                        "
                    />

                    <!-- Loading State -->

                    <ApplicationLoading v-if="loading" />

                    <!-- Error State -->

                    <ApplicationError
                        v-else-if="errorMessage"
                        :message="errorMessage"
                        @retry="fetchApplications(currentPage)"
                    />

                    <!-- Empty State -->

                    <ApplicationEmpty
                        v-else-if="filteredApplications.length === 0"
                        :has-applications="applications.length > 0"
                    />

                    <!-- Application Cards -->

                    <div v-else class="space-y-4">
                        <ApplicationCard
                            v-for="application in filteredApplications"
                            :key="application.id"
                            :application="application"
                            :formatted-date="formatDate(application.applied_at)"
                            :on-withdraw="withdrawApplication"
                        />
                    </div>

                    <!-- Pagination -->

                    <ApplicationPagination
                        v-if="!loading && !errorMessage"
                        :current-page="currentPage"
                        :last-page="pagination.last_page"
                        :total="pagination.total"
                        :loading="loading"
                        @change-page="changePage"
                    />
                </div>

                <!-- ================================== -->
                <!-- Right Sidebar -->
                <!-- ================================== -->

                <aside class="space-y-6">
                    <!-- Recommended Jobs -->

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                    >
                        <!-- Header -->

                        <div
                            class="relative overflow-hidden bg-[#102D59] px-5 py-5"
                        >
                            <!-- Decorative Background -->

                            <div
                                class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-blue-400/10"
                            ></div>

                            <div
                                class="absolute -right-2 bottom-0 h-20 w-20 rounded-full bg-cyan-400/5"
                            ></div>

                            <!-- Header Content -->

                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <!-- Briefcase Icon (Without Square Container) -->

                                    <BriefcaseBusiness
                                        class="h-6 w-6 text-cyan-300"
                                    />

                                    <!-- Recommended Badge -->

                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1.5"
                                    >
                                        <Sparkles
                                            class="h-3.5 w-3.5 text-cyan-300"
                                        />

                                        <span
                                            class="text-[10px] font-semibold tracking-wide text-blue-100"
                                        >
                                            RECOMMENDED
                                        </span>
                                    </div>
                                </div>

                                <!-- Title -->

                                <h3
                                    class="mt-4 text-lg font-bold tracking-tight text-white"
                                    d
                                >
                                    More Opportunities
                                </h3>

                                <!-- Description -->

                                <p
                                    class="mt-1 max-w-xs text-xs leading-5 text-blue-100/80"
                                >
                                    Discover new jobs and find your next career
                                    opportunity.
                                </p>
                            </div>
                        </div>

                        <!-- Recommended Job List -->
                        <!-- <div v-if="isRefreshing" class="text-sm text-gray-500">
                            <RefreshCw
                                class="inline-block w-4 h-4 mr-1 animate-spin"
                            />
                            Finding newer job recommendations...
                        </div> -->
                        <RecommendedJobs
                            :jobs="recommendedJobs"
                            :loading="
                                recommendationsQuery.isPending.value ||
                                isRefreshing
                            "
                            :error="recommendationsQuery.isError.value"
                            @retry="recommendationsQuery.refetch()"
                        />
                    </div>
                    <!-- Career Tip -->

                    <CareerTip />
                </aside>
            </div>
        </section>
    </main>
</template>
