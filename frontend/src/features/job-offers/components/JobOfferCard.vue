<script setup lang="ts">
import { computed } from "vue";
import JobOfferStatusBadge from "./JobOfferStatusBadge.vue";
import type { JobOffer } from "../types/jobOffer.types.ts";
import { RouterLink } from "vue-router";

const props = defineProps<{ offer: JobOffer; responding?: boolean }>();
const emit = defineEmits<{
    (event: "accept", id: number): void;
    (event: "reject", offer: JobOffer): void;
}>();

const companyName = computed(() => props.offer.company?.name || "Employer");
const companyInitials = computed(
    () =>
        companyName.value
            .split(/\s+/)
            .slice(0, 2)
            .map((part) => part[0]?.toUpperCase())
            .join("") || "CO",
);
const salary = computed(() => {
    if (
        props.offer.salary_offered === null ||
        props.offer.salary_offered === undefined ||
        props.offer.salary_offered === ""
    )
        return "Not specified";
    return Number(props.offer.salary_offered).toLocaleString();
});

function formatDate(
    value: string | null | undefined,
    includeTime = false,
): string {
    if (!value) return "Not specified";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return new Intl.DateTimeFormat(
        undefined,
        includeTime
            ? { dateStyle: "medium", timeStyle: "short" }
            : { dateStyle: "medium" },
    ).format(date);
}

function formatEnum(value?: string): string {
    if (!value) return "Not specified";
    return value
        .replaceAll("_", " ")
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

const location = computed(() =>
    [props.offer.job?.city, props.offer.job?.country]
        .filter(Boolean)
        .join(", "),
);
const pending = computed(() => props.offer.status === "pending");
</script>

<template>
    <article
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md"
    >
        <div class="p-5 sm:p-6">
            <div
                class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start"
            >
                <div class="flex min-w-0 items-start gap-3.5">
                    <img
                        v-if="offer.company?.logo"
                        :src="offer.company.logo"
                        :alt="`${companyName} logo`"
                        class="h-12 w-12 shrink-0 rounded-xl border border-slate-100 object-cover"
                    />
                    <div
                        v-else
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-base font-bold text-white shadow-sm"
                    >
                        {{ companyInitials }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-slate-700">{{
                                companyName
                            }}</span>
                            <span class="text-[11px] text-slate-400"
                                >· Received
                                {{ formatDate(offer.created_at) }}</span
                            >
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <h2
                                class="wrap-break-word text-lg font-bold text-slate-900"
                            >
                                {{ offer.job?.title || "Job offer" }}
                            </h2>

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
                        </div>
                    </div>
                </div>
                <JobOfferStatusBadge :status="offer.status" />
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                <span
                    class="rounded-xl border border-emerald-200/80 bg-emerald-50 px-3 py-1.5 font-bold text-emerald-800"
                >
                    Offered salary: {{ salary }}
                </span>
                <span
                    class="rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-700"
                    >{{ formatEnum(offer.job?.employment_type) }}</span
                >
                <span
                    class="rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-700"
                    >{{ formatEnum(offer.job?.workplace_type)
                    }}<template v-if="location">
                        · {{ location }}</template
                    ></span
                >
                <span
                    v-if="offer.expires_at && pending"
                    class="rounded-xl border border-amber-200/80 bg-amber-50 px-3 py-1.5 font-semibold text-amber-800"
                    >Expires {{ formatDate(offer.expires_at, true) }}</span
                >
                <span
                    v-else-if="offer.expires_at"
                    class="rounded-xl bg-slate-100 px-3 py-1.5 font-medium text-slate-600"
                    >Expiry {{ formatDate(offer.expires_at, true) }}</span
                >
            </div>

            <div
                v-if="offer.offer_details"
                class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4"
            >
                <p
                    class="mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Personal note from hiring team
                </p>
                <p
                    class="whitespace-pre-line text-xs leading-relaxed text-slate-700 sm:text-sm"
                >
                    {{ offer.offer_details }}
                </p>
            </div>
        </div>

        <footer
            v-if="pending"
            class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50/80 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:px-6"
        >
            <span class="text-xs text-slate-500"
                >Choose whether to accept or decline this offer.</span
            >
            <div class="flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 disabled:opacity-60"
                    :disabled="responding"
                    @click="emit('reject', offer)"
                >
                    Decline
                </button>
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-xl bg-indigo-600 px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="responding"
                    @click="emit('accept', offer.id)"
                >
                    {{ responding ? "Saving…" : "Accept Offer →" }}
                </button>
            </div>
        </footer>
        <footer
            v-else
            class="flex flex-col gap-1 border-t border-slate-100 bg-slate-50/70 px-5 py-3 text-xs text-slate-500 sm:px-6"
        >
            <span v-if="offer.responded_at"
                >Responded {{ formatDate(offer.responded_at, true) }}</span
            >
            <span v-else>There are no actions available for this offer.</span>
        </footer>
    </article>
</template>
