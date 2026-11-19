<script setup lang="ts">
import { watch } from "vue";
import { useRoute, RouterLink } from "vue-router";

import {
    ArrowLeft,
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    FileText,
    Phone,
    UserRound,
    RefreshCw,
} from "lucide-vue-next";

import { useApplicationDetail } from "../composables/useApplicationDetail";
import ApplicationStatusBadge from "../components/ApplicationStatusBadge.vue";
import { getCompanyLogoUrl } from "../utils/companyLogo";

const route = useRoute();

const {
    application,
    loading,
    errorMessage,
    fetchApplicationDetail,
} = useApplicationDetail();

watch(
    () => route.params.id,
    (id) => {
        const applicationId = Number(id);

        if (!Number.isInteger(applicationId) || applicationId <= 0) {
            errorMessage.value = "Invalid application ID.";
            application.value = null;
            return;
        }

        fetchApplicationDetail(applicationId);
    },
    { immediate: true },
);

const formatDate = (date: string | null | undefined): string => {
    if (!date) return "Date unavailable";

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) {
        return "Date unavailable";
    }

    return parsedDate.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const retry = () => {
    const applicationId = Number(route.params.id);

    if (Number.isInteger(applicationId) && applicationId > 0) {
        fetchApplicationDetail(applicationId);
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">

        <!-- Page Container -->
        <div class="mx-auto max-w-5xl">

            <!-- Back Button -->
            <RouterLink :to="{ name: 'JobSeekerApplications' }"
                class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-blue-700">
                <ArrowLeft class="h-4 w-4" />
                Back to My Applications
            </RouterLink>

            <!-- Loading State -->
            <div v-if="loading" class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="flex justify-center">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-100 border-t-blue-600"></div>
                </div>

                <p class="mt-4 font-medium text-slate-700">
                    Loading application details...
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Please wait a moment.
                </p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMessage" class="rounded-2xl border border-red-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <FileText class="h-7 w-7 text-red-600" />
                </div>

                <h2 class="mt-4 text-lg font-semibold text-slate-800">
                    Unable to Load Application
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    {{ errorMessage }}
                </p>

                <button type="button" @click="retry"
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">
                    <RefreshCw class="h-4 w-4" />
                    Try Again
                </button>
            </div>

            <!-- Application Detail -->
            <div v-else-if="application" class="space-y-6">

                <!-- Header Card -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="bg-gradient-to-r from-[#102D59] to-blue-800 px-6 py-8 sm:px-8">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100">
                                    Application Details
                                </p>

                                <h1 class="mt-2 text-2xl font-bold text-white sm:text-3xl">
                                    {{ application.job.title }}
                                </h1>

                                <p class="mt-2 text-blue-100">
                                    Application #{{ application.id }}
                                </p>
                            </div>

                            <ApplicationStatusBadge :status="application.status" />
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:p-8">

                        <!-- Company Logo -->
                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img v-if="application.job.company.logo"
                                :src="getCompanyLogoUrl(application.job.company.logo)"
                                :alt="application.job.company.name" class="h-full w-full rounded-full object-cover" />

                            <Building2 v-else class="h-9 w-9 text-slate-400" />
                        </div>

                        <!-- Company Name -->
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xl font-bold text-slate-800">
                                {{ application.job.company.name }}
                            </h2>

                            <div class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                                <BriefcaseBusiness class="h-4 w-4" />

                                <span>
                                    {{ application.job.title }}
                                </span>
                            </div>
                        </div>

                        <!-- Applied Date -->
                        <div class="rounded-xl bg-slate-50 px-4 py-3 sm:min-w-[180px]">
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <CalendarDays class="h-4 w-4" />
                                Applied Date
                            </div>

                            <p class="mt-1 font-semibold text-slate-800">
                                {{ formatDate(application.applied_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Applicant Information -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-800">
                            Applicant Information
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Personal information submitted with this application.
                        </p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">

                        <!-- Applicant Name -->
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100">
                                <UserRound class="h-5 w-5 text-blue-600" />
                            </div>

                            <div>
                                <p class="text-sm text-slate-500">
                                    Full Name
                                </p>

                                <p class="mt-1 font-medium text-slate-800">
                                    {{ application.applicant_name || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <!-- Applicant Phone -->
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100">
                                <Phone class="h-5 w-5 text-green-600" />
                            </div>

                            <div>
                                <p class="text-sm text-slate-500">
                                    Phone Number
                                </p>

                                <p class="mt-1 font-medium text-slate-800">
                                    {{ application.applicant_phone || "Not provided" }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Resume Information -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-800">
                            Submitted Resume
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Resume used when applying for this job.
                        </p>
                    </div>

                    <div v-if="application.resume"
                        class="flex items-center gap-4 rounded-xl border border-slate-200 p-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50">
                            <FileText class="h-6 w-6 text-red-500" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800">
                                {{ application.resume.title }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ application.resume.file_type.toUpperCase() }} File
                            </p>
                        </div>
                    </div>

                    <div v-else class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                        No resume information available.
                    </div>
                </div>

                <!-- Application Answers -->
                <div v-if="application.answers && application.answers.length > 0"
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-800">
                            Application Questions
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Your answers submitted with this application.
                        </p>
                    </div>

                    <div class="space-y-5">
                        <div v-for="(answer, index) in application.answers" :key="answer.id"
                            class="rounded-xl border border-slate-200 p-5">
                            <p class="text-sm font-medium text-slate-500">
                                Question {{ index + 1 }}
                            </p>

                            <h3 class="mt-2 font-semibold text-slate-800">
                                {{ answer.question.question || "Question unavailable" }}
                            </h3>

                            <div class="mt-4 rounded-lg bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">
                                    Your Answer
                                </p>

                                <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700">
                                    {{ answer.answer }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation -->
                <div class="flex justify-end">
                    <RouterLink :to="{ name: 'JobSeekerApplications' }"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                        <ArrowLeft class="h-4 w-4" />
                        Back to Applications
                    </RouterLink>
                </div>

            </div>

        </div>
    </div>
</template>