<script setup lang="ts">
import { ref, watch } from "vue";
import { RouterLink } from "vue-router";

import {
    FileText,
    CalendarDays,
    Building2,
    ArrowRight,
    Ban,
} from "lucide-vue-next";

import ApplicationStatusBadge from "./ApplicationStatusBadge.vue";

import { getCompanyLogoUrl } from "../utils/companyLogo";

import type { Application } from "../types/application.types";

const props = defineProps<{
    application: Application;
    formattedDate: string;
    onWithdraw?: (applicationId: number) => void;
}>();

const logoFailed = ref(false);

watch(
    () => props.application.job.company.logo,
    () => {
        logoFailed.value = false;
    }
);
</script>

<template>
    <article
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:border-blue-200 hover:shadow-lg">
        <!-- Card Content -->
        <div class="p-5 sm:p-6">

            <!-- Job Header -->
            <div class="flex items-start gap-4">

                <!-- Company Logo -->
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                    <img v-if="application.job.company.logo && !logoFailed"
                        :src="getCompanyLogoUrl(application.job.company.logo)"
                        :alt="`${application.job.company.name} logo`" class="h-full w-full rounded-full object-cover"
                        @error="logoFailed = true" />

                    <Building2 v-else class="h-7 w-7 text-slate-400" />
                </div>

                <!-- Job Information -->
                <div class="min-w-0 flex-1">

                    <!-- Job Title -->
                    <h3 class="break-words text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                        {{ application.job.title }}
                    </h3>

                    <!-- Company -->
                    <p class="mt-2 flex items-center gap-2 text-sm font-medium text-slate-600">
                        <Building2 class="h-4 w-4 shrink-0 text-slate-400" />

                        <span class="truncate">
                            {{ application.job.company.name }}
                        </span>
                    </p>

                    <!-- Application ID -->
                    <p class="mt-2 text-xs text-slate-400">
                        Application #{{ application.id }}
                    </p>
                </div>

                <!-- Status Badge (Only One) -->
                <div class="shrink-0">
                    <ApplicationStatusBadge :status="application.status" />
                </div>
            </div>

            <!-- Application Information -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- Resume -->
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <FileText class="h-5 w-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-500">
                            Submitted Resume
                        </p>

                        <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                            {{ application.resume?.title ?? "No resume" }}
                        </p>
                    </div>
                </div>

                <!-- Applied Date -->
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                        <CalendarDays class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Applied Date
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ formattedDate }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Card Footer -->
        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 px-5 py-4 sm:px-6">
            <!-- Withdraw Button (Pending Only) -->
            <button v-if="application.status === 'pending'" type="button" @click="props.onWithdraw?.(application.id)"
                class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                <Ban class="h-4 w-4" />
                Withdraw
            </button>

            <!-- View Application Details -->
            <RouterLink :to="{
                name: 'JobSeekerApplicationDetail',
                params: { id: application.id },
            }"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                View Application Details

                <ArrowRight class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" />
            </RouterLink>
        </div>
    </article>
</template>