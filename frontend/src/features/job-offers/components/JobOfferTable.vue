<script setup lang="ts">
import type { JobOffer, JobOfferStatus } from "../types/jobOffer.types";
import {
    formatEmploymentType,
    formatWorkplaceType,
    getInitials,
    formatMoney,
    formatDate,
    formatStatus,
} from "../utils/jobOfferFormatters";

import { RefreshCw } from "lucide-vue-next";

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
                :disabled="loading"
                @click="emit('refresh')"
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:opacity-50"
            >
                <RefreshCw
                    class="h-4 w-4"
                    :class="{ 'animate-spin': loading }"
                />
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
                            <div class="flex items-center gap-3.5">
                                <div v-if="mode === 'jobseeker'">
                                    <img
                                        v-if="offer.company?.logo"
                                        :src="offer.company?.logo"
                                        :alt="offer.company?.name || 'Company'"
                                        class="h-10 w-10 rounded-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-indigo-600 text-xs font-bold text-white"
                                    >
                                        {{
                                            getInitials(
                                                offer.company?.name ||
                                                    "Company",
                                            )
                                        }}
                                    </div>
                                </div>
                                <div v-else>
                                    <img
                                        v-if="
                                            offer.job_seeker?.profile
                                                ?.profile_photo
                                        "
                                        :src="
                                            offer.job_seeker.profile
                                                .profile_photo
                                        "
                                        :alt="
                                            offer.job_seeker?.name ||
                                            'Candidate'
                                        "
                                        class="h-10 w-10 rounded-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-indigo-600 text-xs font-bold text-white"
                                    >
                                        {{
                                            getInitials(
                                                offer.job_seeker?.name ||
                                                    "Candidate",
                                            )
                                        }}
                                    </div>
                                </div>
                                <div class="mt-0.5">
                                    <p class="font-semibold text-slate-900">
                                        {{
                                            mode === "employer"
                                                ? offer.job_seeker?.name ||
                                                  "Candidate"
                                                : offer.company?.name ||
                                                  "Company"
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
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-800">
                                {{ offer.job?.title || "Job offer" }}
                            </p>

                            <div class="mt-1 flex items-center gap-2">
                                <span
                                    v-if="offer.job?.employment_type"
                                    class="rounded-md border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700"
                                >
                                    {{
                                        formatEmploymentType(
                                            offer.job.employment_type,
                                        )
                                    }}
                                </span>

                                <span
                                    v-if="offer.job?.workplace_type"
                                    class="text-xs text-slate-500"
                                >
                                    {{
                                        formatWorkplaceType(
                                            offer.job.workplace_type,
                                        )
                                    }}
                                </span>

                                <span
                                    v-if="offer.job?.city || offer.job?.country"
                                    class="text-xs text-slate-500"
                                >
                                    ⌖
                                    {{
                                        [offer.job?.city, offer.job?.country]
                                            .filter(Boolean)
                                            .join(", ")
                                    }}
                                </span>
                            </div>
                        </td>

                        <td
                            class="font-semibold whitespace-nowrap px-5 py-4 text-slate-700"
                        >
                            {{ formatMoney(offer.salary_offered, "$") }}
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
                            <p>
                                Sent:
                                <span class="font-semibold">{{
                                    formatDate(offer.created_at)
                                }}</span>
                            </p>
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
                                <button
                                    type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:text-indigo-700"
                                    @click="emit('view', offer)"
                                >
                                    View
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
