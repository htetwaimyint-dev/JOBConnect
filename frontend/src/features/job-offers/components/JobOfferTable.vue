<script setup lang="ts">
import type { JobOffer, JobOfferStatus } from "../types/jobOffer.types";

defineProps<{
    offers: JobOffer[];
    mode: "employer" | "jobseeker";
    loading?: boolean;
    currencyLabel?: string;
}>();
const emit = defineEmits<{
    view: [offer: JobOffer];
    withdraw: [offer: JobOffer];
    refresh: [];
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
</script>

<template>
    <section
        class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        <div
            class="flex items-center justify-between border-b border-slate-100 px-5 py-4"
        >
            <div>
                <h2 class="font-bold text-slate-900">
                    {{
                        mode === "employer" ? "Offers sent" : "Offers received"
                    }}
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{
                        mode === "employer"
                            ? "Manage your direct job invitations."
                            : "Review and respond to your job offers."
                    }}
                </p>
            </div>
            <button
                type="button"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                @click="emit('refresh')"
            >
                Refresh
            </button>
        </div>

        <div v-if="loading" class="p-10 text-center text-sm text-slate-500">
            Loading offers…
        </div>

        <div v-else-if="!offers.length" class="p-12 text-center">
            <p class="font-semibold text-slate-800">No offers found</p>
            <p class="mt-1 text-sm text-slate-500">
                {{
                    mode === "employer"
                        ? "Your sent offers will appear here."
                        : "Offers sent to you will appear here."
                }}
            </p>
        </div>

        <div v-else class="overflow-x-auto">
            <table class="w-full min-w-190 text-left">
                <thead
                    class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500"
                >
                    <tr>
                        <th class="px-5 py-3 font-semibold">
                            {{ mode === "employer" ? "Candidate" : "Company" }}
                        </th>
                        <th class="px-5 py-3 font-semibold">Position</th>
                        <th class="px-5 py-3 font-semibold">Salary offered</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">
                            {{
                                mode === "employer"
                                    ? "Sent / expiry"
                                    : "Received / expiry"
                            }}
                        </th>
                        <th class="px-5 py-3 text-right font-semibold">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">
                    <tr
                        v-for="offer in offers"
                        :key="offer.id"
                        class="hover:bg-slate-50/70"
                    >
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">
                                {{
                                    mode === "employer"
                                        ? offer.job_seeker?.name || "Candidate"
                                        : offer.company?.name || "Company"
                                }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{
                                    mode === "employer"
                                        ? offer.job_seeker?.profile
                                              ?.professional_title ||
                                          "Job seeker"
                                        : "Employer"
                                }}
                            </p>
                        </td>
                        <td class="px-5 py-4 font-medium text-slate-700">
                            {{ offer.job?.title || "Job offer" }}
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-700">
                            {{
                                formatMoney(offer.salary_offered, currencyLabel)
                            }}
                        </td>
                        <td class="px-5 py-4">
                            <span
                                class="rounded-full border px-2.5 py-1 text-xs font-semibold"
                                :class="statusClass(offer.status)"
                            >
                                {{ formatStatus(offer.status) }}
                            </span>
                        </td>
                        <td
                            class="whitespace-nowrap px-5 py-4 text-xs text-slate-600"
                        >
                            <p>{{ formatDate(offer.created_at) }}</p>
                            <p class="mt-1 text-slate-400">
                                {{
                                    offer.status === "pending"
                                        ? `Expires: ${formatDate(offer.expires_at)}`
                                        : offer.responded_at
                                          ? `Responded: ${formatDate(offer.responded_at)}`
                                          : `Expires: ${formatDate(offer.expires_at)}`
                                }}
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:text-indigo-700"
                                    @click="emit('view', offer)"
                                >
                                    View
                                </button>
                                <button
                                    v-if="
                                        mode === 'employer' &&
                                        offer.status === 'pending'
                                    "
                                    type="button"
                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                    @click="emit('withdraw', offer)"
                                >
                                    Withdraw
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer
            v-if="!loading && offers.length"
            class="border-t border-slate-100 px-5 py-3 text-xs text-slate-500"
        >
            Showing {{ offers.length }} offer(s)
        </footer>
    </section>
</template>
