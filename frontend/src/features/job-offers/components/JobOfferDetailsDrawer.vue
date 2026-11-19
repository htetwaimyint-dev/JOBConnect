<script setup lang="ts">
import type { JobOffer, JobOfferStatus } from "../types/jobOffer.types";
import {
    formatDate,
    formatEmploymentType,
    formatWorkplaceType,
    formatStatus,
    formatMoney,
    getInitials,
} from "../utils/jobOfferFormatters";

defineProps<{
    offer: JobOffer | null;
    mode: "employer" | "jobseeker";
    responding?: boolean;
    withdrawing?: boolean;
    currencyLabel?: string;
}>();

const emit = defineEmits<{
    close: [];
    withdraw: [offer: JobOffer];
    accept: [offer: JobOffer];
    reject: [offer: JobOffer];
}>();

function statusClass(status: JobOfferStatus) {
    return {
        pending: "bg-amber-50 text-amber-700 border-amber-200",
        accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
        rejected: "bg-rose-50 text-rose-700 border-rose-200",
        expired: "bg-slate-100 text-slate-600 border-slate-200",
        withdrawn: "bg-slate-100 text-slate-500 border-slate-200",
    }[status];
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="offer"
            class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex justify-end"
            @click.self="emit('close')"
        >
            <Transition name="slide">
                <aside
                    v-if="offer"
                    class="bg-white w-full max-w-lg h-full shadow-2xl border-l border-slate-200 flex flex-col justify-between overflow-y-auto"
                >
                    <div
                        class="flex-1 overflow-y-auto scrollbar-none [&::-webkit-scrollbar]:hidden"
                    >
                        <div
                            class="flex flex-col flex-1 space-y-4 overflow-y-auto p-6 pb-0"
                        >
                            <div
                                class="flex items-center justify-between border-b border-slate-100 pb-4"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        v-if="mode === 'employer'"
                                        class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100"
                                    >
                                        Offer #205</span
                                    ><span class="text-xs text-slate-400"
                                        >Direct Invitation</span
                                    >
                                </div>
                                <div class="flex items-center gap-1">
                                    <RouterLink
                                        v-if="offer.job?.id"
                                        :to="`/jobs/${offer.job.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-1 text-xs font-semibold text-indigo-700 transition hover:border-indigo-300 hover:bg-indigo-100"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 10l4.5-4.5M19.5 5.5H15m4.5 0V10M10 5H6.5A1.5 1.5 0 005 6.5v11A1.5 1.5 0 006.5 19h11a1.5 1.5 0 001.5-1.5V14"
                                            />
                                        </svg>

                                        View Job Details
                                    </RouterLink>
                                    <button
                                        type="button"
                                        aria-label="Close details"
                                        class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 transition-colors"
                                        @click="emit('close')"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between gap-4 bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80"
                            >
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div
                                        class="w-12 h-12 rounded-full text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-xs bg-indigo-600"
                                    >
                                        {{
                                            getInitials(
                                                mode === "employer"
                                                    ? offer.job_seeker?.name
                                                    : offer.company?.name,
                                            )
                                        }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3
                                            class="font-bold text-base text-slate-900 truncate"
                                        >
                                            {{
                                                mode === "employer"
                                                    ? offer.job_seeker?.name ||
                                                      "Candidate"
                                                    : offer.company?.name ||
                                                      "Company"
                                            }}
                                        </h3>
                                        <p
                                            class="text-xs text-slate-600 truncate"
                                        >
                                            {{
                                                mode === "employer"
                                                    ? offer.job_seeker?.profile
                                                          ?.professional_title ||
                                                      "Job seeker"
                                                    : "Employer"
                                            }}
                                        </p>
                                        <p
                                            class="text-[11px] text-slate-400 mt-0.5"
                                        >
                                            {{
                                                mode === "employer"
                                                    ? offer.job_seeker?.email ||
                                                      ""
                                                    : ""
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <section>
                                <div class="mt-6 space-y-3">
                                    <h4
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Offer Terms &amp; Timeline
                                    </h4>
                                    <div
                                        class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 text-sm"
                                    >
                                        <div
                                            class="px-4 py-3 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >Current Status</span
                                            ><span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border capitalize bg-amber-50 text-amber-700 border-amber-200"
                                                ><span
                                                    class="w-1.5 h-1.5 rounded-full bg-amber-500"
                                                ></span>
                                                {{
                                                    formatStatus(offer.status)
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="px-4 py-3 flex items-center justify-between gap-4"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >Target Position</span
                                            ><span
                                                class="font-semibold text-slate-900 text-right"
                                                >{{ offer.job?.title }}</span
                                            >
                                        </div>
                                        <div
                                            class="px-4 py-3 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >Work Arrangement</span
                                            ><span
                                                class="text-slate-700 text-xs font-medium"
                                                >{{
                                                    formatEmploymentType(
                                                        offer.job
                                                            ?.employment_type,
                                                    )
                                                }}
                                                •
                                                {{
                                                    formatWorkplaceType(
                                                        offer.job
                                                            ?.workplace_type,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="px-4 py-3 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >Salary Offered</span
                                            ><span
                                                class="font-bold text-emerald-700"
                                                >{{
                                                    formatMoney(
                                                        offer.salary_offered,
                                                        "$",
                                                    )
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="px-4 py-3 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >{{
                                                    mode === "employer"
                                                        ? "Sent date"
                                                        : "Received date"
                                                }}</span
                                            ><span
                                                class="text-slate-700 text-xs font-medium"
                                                >{{
                                                    formatDate(offer.created_at)
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="px-4 py-3 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-slate-500 text-xs font-medium"
                                                >Expires At</span
                                            ><span
                                                class="text-xs font-medium text-amber-600"
                                                >{{
                                                    formatDate(offer.expires_at)
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <div class="mt-6">
                                    <h4
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2"
                                    >
                                        Pitch Message (`offer_details`)
                                    </h4>
                                    <div
                                        class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-sm text-slate-700 whitespace-pre-line leading-relaxed"
                                    >
                                        {{
                                            offer.offer_details ||
                                            "No additional details were provided."
                                        }}
                                    </div>
                                </div>
                            </section>

                            <!-- <section v-if="offer.responded_at">
                                <h3 class="text-sm font-bold text-slate-900">
                                    Response date
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ formatDate(offer.responded_at) }}
                                </p>
                            </section> -->
                        </div>
                        <div
                            v-if="mode === 'employer'"
                            class="mt-5 p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3"
                        >
                            <button
                                :disabled="withdrawing"
                                class="px-4 py-2.5 rounded-xl border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-semibold transition"
                                @click="emit('withdraw', offer)"
                            >
                                {{
                                    withdrawing
                                        ? "Withdrawing…"
                                        : "Withdraw offer"
                                }}</button
                            ><button
                                class="ml-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition"
                                @click="emit('close')"
                            >
                                Close
                            </button>
                        </div>

                        <div v-else class="flex gap-3 py-6 px-6">
                            <button
                                type="button"
                                :disabled="responding"
                                class="flex-1 rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 disabled:opacity-50"
                                @click="emit('reject', offer)"
                            >
                                Decline
                            </button>
                            <button
                                type="button"
                                :disabled="responding"
                                class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                                @click="emit('accept', offer)"
                            >
                                Accept offer
                            </button>
                        </div>
                    </div>
                </aside>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}
</style>
