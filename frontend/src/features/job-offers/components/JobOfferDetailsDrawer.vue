<script setup lang="ts">
import type { JobOffer, JobOfferStatus } from "../types/jobOffer.types";

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

function formatStatus(status: JobOfferStatus) {
    return {
        pending: "Pending",
        accepted: "Accepted",
        rejected: "Declined",
        expired: "Expired",
        withdrawn: "Withdrawn",
    }[status];
}

function statusClass(status: JobOfferStatus) {
    return {
        pending: "bg-amber-50 text-amber-700 border-amber-200",
        accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
        rejected: "bg-rose-50 text-rose-700 border-rose-200",
        expired: "bg-slate-100 text-slate-600 border-slate-200",
        withdrawn: "bg-slate-100 text-slate-500 border-slate-200",
    }[status];
}

function formatDate(value?: string | null) {
    if (!value) return "—";
    const date = new Date(value.replace(" ", "T"));
    return Number.isNaN(date.getTime())
        ? value
        : new Intl.DateTimeFormat(undefined, {
              day: "2-digit",
              month: "short",
              year: "numeric",
          }).format(date);
}

function formatMoney(value: number | string | null, currencyLabel = "") {
    if (value === null || value === "") return "Not specified";
    const amount = Number(value);
    if (!Number.isFinite(amount)) return String(value);
    return `${currencyLabel ? `${currencyLabel} ` : ""}${new Intl.NumberFormat().format(amount)}`;
}

function formatLabel(value?: string | null) {
    if (!value) return "—";
    const labels: Record<string, string> = {
        full_time: "Full-time",
        part_time: "Part-time",
        contract: "Contract",
        internship: "Internship",
        onsite: "On-site",
        remote: "Remote",
        hybrid: "Hybrid",
    };
    return labels[value] ?? value.replaceAll("_", " ");
}

function initials(value?: string | null) {
    return (value ?? "?")
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join("")
        .toUpperCase();
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="offer"
            class="fixed inset-0 z-40 bg-slate-900/35"
            @click.self="emit('close')"
        >
            <Transition name="slide">
                <aside
                    v-if="offer"
                    class="absolute inset-y-0 right-0 flex w-full max-w-xl flex-col bg-white shadow-2xl"
                >
                    <header
                        class="flex items-start justify-between border-b border-slate-200 px-6 py-5"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-indigo-600"
                            >
                                Offer details
                            </p>
                            <h2 class="mt-1 text-lg font-bold text-slate-900">
                                {{ offer.job?.title || "Job offer" }}
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Offer #{{ offer.id }}
                            </p>
                        </div>
                        <button
                            type="button"
                            aria-label="Close details"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-100"
                            @click="emit('close')"
                        >
                            ✕
                        </button>
                    </header>

                    <div class="flex-1 space-y-6 overflow-y-auto p-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-indigo-600 font-bold text-white"
                            >
                                {{
                                    initials(
                                        mode === "employer"
                                            ? offer.job_seeker?.name
                                            : offer.company?.name,
                                    )
                                }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">
                                    {{
                                        mode === "employer"
                                            ? offer.job_seeker?.name ||
                                              "Candidate"
                                            : offer.company?.name || "Company"
                                    }}
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{
                                        mode === "employer"
                                            ? offer.job_seeker?.profile
                                                  ?.professional_title ||
                                              "Job seeker"
                                            : "Employer"
                                    }}
                                </p>
                            </div>
                            <span
                                class="ml-auto rounded-full border px-2.5 py-1 text-xs font-semibold"
                                :class="statusClass(offer.status)"
                            >
                                {{ formatStatus(offer.status) }}
                            </span>
                        </div>

                        <section class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">
                                    Salary offered
                                </p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{
                                        formatMoney(
                                            offer.salary_offered,
                                            currencyLabel,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">
                                    Employment type
                                </p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{
                                        formatLabel(offer.job?.employment_type)
                                    }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">Workplace</p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{ formatLabel(offer.job?.workplace_type) }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">Location</p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{
                                        [offer.job?.city, offer.job?.country]
                                            .filter(Boolean)
                                            .join(", ") || "—"
                                    }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">
                                    {{
                                        mode === "employer"
                                            ? "Sent date"
                                            : "Received date"
                                    }}
                                </p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{ formatDate(offer.created_at) }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-500">Expires</p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{ formatDate(offer.expires_at) }}
                                </p>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-sm font-bold text-slate-900">
                                Offer note
                            </h3>
                            <p
                                class="mt-2 whitespace-pre-wrap rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600"
                            >
                                {{
                                    offer.offer_details ||
                                    "No additional details were provided."
                                }}
                            </p>
                        </section>

                        <section v-if="offer.responded_at">
                            <h3 class="text-sm font-bold text-slate-900">
                                Response date
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ formatDate(offer.responded_at) }}
                            </p>
                        </section>
                    </div>

                    <footer
                        v-if="offer.status === 'pending'"
                        class="border-t border-slate-200 p-5"
                    >
                        <template v-if="mode === 'employer'">
                            <button
                                type="button"
                                :disabled="withdrawing"
                                class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 disabled:opacity-50"
                                @click="emit('withdraw', offer)"
                            >
                                {{
                                    withdrawing
                                        ? "Withdrawing…"
                                        : "Withdraw offer"
                                }}
                            </button>
                        </template>

                        <template v-else>
                            <div class="flex gap-3">
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
                        </template>
                    </footer>
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
