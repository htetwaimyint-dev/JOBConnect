<script setup lang="ts">
import { UserRound, Eye } from "lucide-vue-next";

import ApplicationStatusBadge from "@/features/applications/components/ApplicationStatusBadge.vue";
import ApplicationPagination from "@/features/applications/components/ApplicationPagination.vue";

import type {
    EmployerApplication,
} from "@/features/applications/types/employer-application.types";

defineProps<{
    applications: EmployerApplication[];
    loading: boolean;
    pagination: {
        current_page: number;
        last_page: number;
        total: number;
    };
    formatDate: (value: string | null | undefined) => string;
}>();

const emit = defineEmits<{
    "open-applicant": [id: number];
    "change-page": [page: number];
}>();

</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-bold text-slate-900">
                    Applicant list
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Review candidate applications
                </p>
            </div>

            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ pagination.total }} applicants
            </span>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex flex-col items-center py-16">
            <div class="h-9 w-9 animate-spin rounded-full border-4 border-blue-100 border-t-blue-700"></div>

            <p class="mt-4 text-sm text-slate-500">
                Loading applicants...
            </p>
        </div>

        <!-- Empty State -->
        <div v-else-if="applications.length === 0" class="px-6 py-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                <UserRound class="h-7 w-7 text-slate-400" />
            </div>

            <h3 class="mt-4 font-semibold text-slate-800">
                No applicants found
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Applications will appear here when candidates apply for your jobs.
            </p>
        </div>

        <!-- Applicant Table -->
        <div v-else class="overflow-x-auto">
            <table class="w-full min-w-[750px] text-left">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Applicant</th>
                        <th class="px-6 py-4 font-semibold">Job position</th>
                        <th class="px-6 py-4 font-semibold">Applied date</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <tr v-for="application in applications" :key="application.id" class="transition hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">
                                {{ application.applicant_name || "Unknown Applicant" }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                {{ application.applicant_phone || "No phone number" }}
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ application.job?.title || "Unknown Job" }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ formatDate(application.applied_at) }}
                        </td>

                        <td class="px-6 py-4">
                            <ApplicationStatusBadge :status="application.status" />
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="emit('open-applicant', application.id)"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">
                                <Eye class="h-4 w-4" />
                                View details
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && pagination.last_page > 1" class="border-t border-slate-100 p-4">
            <ApplicationPagination :current-page="pagination.current_page" :last-page="pagination.last_page"
                :total="pagination.total" :loading="loading" @change-page="emit('change-page', $event)" />
        </div>
    </section>
</template>
