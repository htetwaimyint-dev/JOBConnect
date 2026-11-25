<script setup lang="ts">
import { Briefcase, CalendarDays, Phone, UserRound, X } from "lucide-vue-next";

import ApplicationStatusBadge from "@/features/applications/components/ApplicationStatusBadge.vue";

import ApplicantProfileSection from "./ApplicantProfileSection.vue";
import ApplicantAiEvaluation from "./ApplicantAiEvaluation.vue";
import ApplicantStatusHistory from "./ApplicantStatusHistory.vue";
import ApplicantStatusActions from "./ApplicantStatusActions.vue";

import type {
    ApplicationStatus,
    EmployerApplication,
} from "@/features/applications/types/employer-application.types";

type NextApplicationStatus = Exclude<
    ApplicationStatus,
    "pending" | "withdrawn"
>;

defineProps<{
    open: boolean;
    detailLoading: boolean;
    selectedApplication: EmployerApplication | null;
    errorMessage: string | null;
    profilePhotoUrl: string | null;
    professionalTitle: string | null;
    previewLoading: boolean;
    evaluatingApplicant: boolean;
    canEvaluateApplicant: boolean;
    evaluationError: string | null;
    nextStatusOptions: NextApplicationStatus[];
    updatingStatus: boolean;
    formatDate: (date: string | null | undefined) => string;
}>();

const statusNote = defineModel<string>("statusNote", {
    required: true,
});

const emit = defineEmits<{
    close: [];
    "preview-resume": [];
    "run-evaluation": [];
    "change-status": [status: NextApplicationStatus];
}>();
</script>

<template>
    <!-- Applicant Drawer -->
    <Transition name="drawer">
        <div v-if="open" class="fixed inset-0 z-50 flex justify-end bg-slate-950/40" @click.self="emit('close')">
            <aside class="flex h-full w-full max-w-2xl flex-col bg-white shadow-2xl">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Applicant details
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Candidate application profile
                        </p>
                    </div>

                    <button type="button" @click="emit('close')"
                        class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Drawer Loading -->
                <div v-if="detailLoading" class="flex flex-1 flex-col items-center justify-center">
                    <div class="h-9 w-9 animate-spin rounded-full border-4 border-blue-100 border-t-blue-700"></div>

                    <p class="mt-4 text-sm text-slate-500">
                        Loading applicant details...
                    </p>
                </div>

                <!-- Empty / Error State -->
                <div v-else-if="!selectedApplication"
                    class="flex flex-1 flex-col items-center justify-center p-6 text-center">
                    <p class="text-sm text-slate-500">
                        {{ errorMessage || "Unable to load applicant details." }}
                    </p>

                    <button type="button" @click="emit('close')"
                        class="mt-4 rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        Close
                    </button>
                </div>

                <!-- Drawer Content -->
                <div v-else class="flex-1 space-y-6 overflow-y-auto p-6">
                    <!-- Applicant Information -->
                    <section class="rounded-2xl border border-slate-200 p-5">
                        <div class="flex items-start gap-4">
                            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" alt="Applicant profile photo"
                                class="h-16 w-16 shrink-0 rounded-full border border-slate-200 object-cover" />

                            <div v-else
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                                <UserRound class="h-7 w-7" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{
                                        selectedApplication.job_seeker?.name ||
                                        selectedApplication.applicant_name ||
                                        "Unknown Applicant"
                                    }}
                                </h3>

                                <p class="mt-1 break-all text-sm text-slate-500">
                                    {{
                                        selectedApplication.job_seeker?.email ||
                                        "No email provided"
                                    }}
                                </p>

                                <p v-if="professionalTitle" class="mt-1 text-sm font-medium text-blue-700">
                                    {{ professionalTitle }}
                                </p>

                                <div class="mt-3">
                                    <ApplicationStatusBadge :status="selectedApplication.status" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <Phone class="h-4 w-4 text-slate-400" />

                                {{
                                    selectedApplication.applicant_phone ||
                                    "No phone"
                                }}
                            </div>

                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <CalendarDays class="h-4 w-4 text-slate-400" />

                                {{ formatDate(selectedApplication.applied_at) }}
                            </div>

                            <div class="col-span-2 flex items-center gap-2 text-sm text-slate-600">
                                <Briefcase class="h-4 w-4 text-slate-400" />

                                {{
                                    selectedApplication.job?.title ||
                                    "Unknown Job"
                                }}
                            </div>
                        </div>
                    </section>

                    <!-- Applicant Profile -->
                    <ApplicantProfileSection :application="selectedApplication" :preview-loading="previewLoading"
                        @preview-resume="emit('preview-resume')" />

                    <!-- AI Evaluation -->
                    <ApplicantAiEvaluation :application="selectedApplication" :evaluating="evaluatingApplicant"
                        :can-evaluate="canEvaluateApplicant" :evaluation-error="evaluationError"
                        @run-evaluation="emit('run-evaluation')" />

                    <!-- Status History -->
                    <ApplicantStatusHistory :histories="selectedApplication.status_histories ?? []"
                        :format-date="formatDate" />

                    <!-- Status Actions -->
                    <ApplicantStatusActions v-model:status-note="statusNote" :next-status-options="nextStatusOptions"
                        :updating-status="updatingStatus" :error-message="errorMessage"
                        @change-status="emit('change-status', $event)" />
                </div>
            </aside>
        </div>
    </Transition>
</template>
