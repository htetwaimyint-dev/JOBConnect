<script setup lang="ts">
import { computed, ref, watch } from "vue";

import type {
    JobOffer,
    EmployerOfferJob,
    JobOfferCandidate,
    CreateJobOfferPayload,
    JobOfferStatus,
    JobOfferFilter,
} from "../types/jobOffer.types";

const props = withDefaults(
    defineProps<{
        offers: JobOffer[];
        availableJobs: EmployerOfferJob[];
        publicCandidates?: JobOfferCandidate[];
        loading?: boolean;
        creating?: boolean;
        withdrawingId?: number | null;
        currencyLabel?: string;
    }>(),
    {
        publicCandidates: () => [],
        loading: false,
        creating: false,
        withdrawingId: null,
        currencyLabel: "",
    },
);

const emit = defineEmits<{
    create: [payload: CreateJobOfferPayload];
    withdraw: [offer: JobOffer];
    refresh: [];
}>();

const statusFilter = ref<JobOfferFilter>("all");
const selectedJobFilter = ref("all");
const searchQuery = ref("");
const selectedOffer = ref<JobOffer | null>(null);
const withdrawTarget = ref<JobOffer | null>(null);
const showNewOfferModal = ref(false);

const newOfferForm = ref({
    job_seeker_id: "",
    job_id: "",
    salary_offered: "",
    expires_in_days: 7,
    offer_details: "",
});

const statusOptions: Array<{ key: JobOfferFilter; label: string }> = [
    { key: "all", label: "All" },
    { key: "pending", label: "Pending" },
    { key: "accepted", label: "Accepted" },
    { key: "rejected", label: "Rejected" },
    { key: "expired", label: "Expired" },
    { key: "withdrawn", label: "Withdrawn" },
];

const stats = computed(() => {
    const total = props.offers.length;
    const pending = props.offers.filter((o) => o.status === "pending").length;
    const accepted = props.offers.filter((o) => o.status === "accepted").length;
    const responded = props.offers.filter(
        (o) => o.status === "accepted" || o.status === "rejected",
    ).length;
    return {
        total,
        pending,
        accepted,
        acceptanceRate: responded
            ? Math.round((accepted / responded) * 100)
            : 0,
    };
});

const statusTabs = computed(() =>
    statusOptions.map((tab) => ({
        ...tab,
        count:
            tab.key === "all"
                ? props.offers.length
                : props.offers.filter((o) => o.status === tab.key).length,
    })),
);

const filteredOffers = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase();
    return props.offers.filter((offer) => {
        const matchesStatus =
            statusFilter.value === "all" || offer.status === statusFilter.value;
        const matchesJob =
            selectedJobFilter.value === "all" ||
            String(offer.job_id) === selectedJobFilter.value;
        const matchesSearch =
            !query ||
            [
                offer.job_seeker?.name,
                offer.job_seeker?.profile?.professional_title,
                offer.job?.title,
            ].some((value) =>
                (value ?? "").toLocaleLowerCase().includes(query),
            );
        return matchesStatus && matchesJob && matchesSearch;
    });
});

const selectedJob = computed(
    () =>
        props.availableJobs.find(
            (job) => String(job.id) === newOfferForm.value.job_id,
        ) ?? null,
);

const formatStatus = (status: JobOfferStatus) =>
    ({
        pending: "Pending",
        accepted: "Accepted",
        rejected: "Declined",
        expired: "Expired",
        withdrawn: "Withdrawn",
    })[status];
const statusClass = (status: JobOfferStatus) =>
    ({
        pending: "bg-amber-50 text-amber-700 border-amber-200",
        accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
        rejected: "bg-rose-50 text-rose-700 border-rose-200",
        expired: "bg-slate-100 text-slate-600 border-slate-200",
        withdrawn: "bg-slate-100 text-slate-500 border-slate-200",
    })[status];
const statusDotClass = (status: JobOfferStatus) =>
    ({
        pending: "bg-amber-500",
        accepted: "bg-emerald-500",
        rejected: "bg-rose-500",
        expired: "bg-slate-400",
        withdrawn: "bg-slate-400",
    })[status];
const formatEmploymentType = (type?: string | null) => {
    if (!type) return "";
    return (
        (
            {
                full_time: "Full-time",
                part_time: "Part-time",
                contract: "Contract",
                internship: "Internship",
            } as Record<string, string>
        )[type] ?? type.replaceAll("_", " ")
    );
};
const formatWorkplaceType = (type?: string | null) =>
    (
        ({ onsite: "On-site", remote: "Remote", hybrid: "Hybrid" }) as Record<
            string,
            string
        >
    )[type ?? ""] ??
    type ??
    "";
const formatDate = (value?: string | null) => {
    if (!value) return "—";
    const date = new Date(value.replace(" ", "T"));
    return Number.isNaN(date.getTime())
        ? value
        : new Intl.DateTimeFormat(undefined, {
              day: "2-digit",
              month: "short",
              year: "numeric",
          }).format(date);
};
const formatMoney = (value: number | string | null) => {
    if (value === null || value === "") return "Not specified";
    const amount = Number(value);
    if (!Number.isFinite(amount)) return String(value);
    return `${props.currencyLabel ? `${props.currencyLabel} ` : ""}${new Intl.NumberFormat().format(amount)}`;
};
const initials = (name?: string | null) =>
    (name ?? "?")
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join("")
        .toUpperCase();

const resetFilters = () => {
    statusFilter.value = "all";
    selectedJobFilter.value = "all";
    searchQuery.value = "";
};
const openCreateModal = () => {
    newOfferForm.value = {
        job_seeker_id: "",
        job_id: props.availableJobs[0] ? String(props.availableJobs[0].id) : "",
        salary_offered: "",
        expires_in_days: 7,
        offer_details: "",
    };
    showNewOfferModal.value = true;
};
const submitNewOffer = () => {
    const candidateId = Number(newOfferForm.value.job_seeker_id);
    const jobId = Number(newOfferForm.value.job_id);
    const salary = Number(newOfferForm.value.salary_offered);
    if (
        !Number.isInteger(candidateId) ||
        candidateId <= 0 ||
        !Number.isInteger(jobId) ||
        jobId <= 0 ||
        !newOfferForm.value.salary_offered.trim() ||
        !Number.isFinite(salary) ||
        salary < 0 ||
        !newOfferForm.value.offer_details.trim()
    )
        return;
    emit("create", {
        job_seeker_id: candidateId,
        job_id: jobId,
        salary_offered: salary,
        offer_details: newOfferForm.value.offer_details.trim(),
        expires_at: new Date(
            Date.now() + newOfferForm.value.expires_in_days * 86400000,
        ).toISOString(),
    });
    showNewOfferModal.value = false;
};
const confirmWithdraw = () => {
    if (withdrawTarget.value) emit("withdraw", withdrawTarget.value);
};
watch(
    () => props.offers,
    () => {
        if (selectedOffer.value)
            selectedOffer.value =
                props.offers.find((o) => o.id === selectedOffer.value?.id) ??
                null;
    },
    { deep: true },
);
</script>

<template>
    <div class="min-h-full bg-slate-50 text-slate-800">
        <main
            class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8"
        >
            <header
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <nav
                        class="mb-1 flex items-center gap-1.5 text-xs font-medium text-slate-500"
                    >
                        <span>Employer Dashboard</span><span>/</span
                        ><span>Recruitment Pipeline</span><span>/</span>
                        <span class="font-semibold text-indigo-600"
                            >Direct Job Offers</span
                        >
                    </nav>
                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        Direct Job Offers
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Track direct invitations sent to public job seekers and
                        manage candidate responses.
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 self-start rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 sm:self-auto"
                    @click="openCreateModal"
                >
                    <span class="text-lg leading-none">+</span> New Direct Offer
                </button>
            </header>

            <section
                class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
            >
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Total Sent
                        </p>
                        <p class="mt-1.5 text-3xl font-bold text-slate-900">
                            {{ stats.total }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            All direct invitations
                        </p>
                    </div>
                    <span
                        class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-lg text-slate-600"
                        >↗</span
                    >
                </article>
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                        >
                            Awaiting Response
                        </p>
                        <p class="mt-1.5 text-3xl font-bold text-slate-900">
                            {{ stats.pending }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Active pending offers
                        </p>
                    </div>
                    <span
                        class="grid h-10 w-10 place-items-center rounded-xl border border-amber-100 bg-amber-50 text-lg text-amber-600"
                        >◷</span
                    >
                </article>
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                        >
                            Accepted Offers
                        </p>
                        <p class="mt-1.5 text-3xl font-bold text-slate-900">
                            {{ stats.accepted }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Ready for onboarding
                        </p>
                    </div>
                    <span
                        class="grid h-10 w-10 place-items-center rounded-xl border border-emerald-100 bg-emerald-50 text-lg text-emerald-600"
                        >✓</span
                    >
                </article>
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-indigo-600"
                        >
                            Acceptance Rate
                        </p>
                        <p class="mt-1.5 text-3xl font-bold text-slate-900">
                            {{ stats.acceptanceRate }}%
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Of responded offers
                        </p>
                    </div>
                    <span
                        class="grid h-10 w-10 place-items-center rounded-xl border border-indigo-100 bg-indigo-50 text-lg text-indigo-600"
                        >↗</span
                    >
                </article>
            </section>

            <section
                class="mt-6 flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm xl:flex-row xl:items-center"
            >
                <div
                    class="flex items-center gap-1.5 overflow-x-auto pb-1 xl:pb-0"
                >
                    <button
                        v-for="tab in statusTabs"
                        :key="tab.key"
                        type="button"
                        class="flex shrink-0 items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-semibold transition"
                        :class="
                            statusFilter === tab.key
                                ? 'bg-slate-900 text-white shadow-sm'
                                : 'bg-slate-100/80 text-slate-600 hover:bg-slate-200'
                        "
                        @click="statusFilter = tab.key"
                    >
                        {{ tab.label }}
                        <span
                            class="rounded-full px-1.5 py-0.5 text-[10px] font-bold"
                            :class="
                                statusFilter === tab.key
                                    ? 'bg-white/20 text-white'
                                    : 'border border-slate-200 bg-white text-slate-600'
                            "
                            >{{ tab.count }}</span
                        >
                    </button>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <select
                        v-model="selectedJobFilter"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:w-56"
                    >
                        <option value="all">
                            All Job Positions ({{ offers.length }})
                        </option>
                        <option
                            v-for="job in availableJobs"
                            :key="job.id"
                            :value="String(job.id)"
                        >
                            {{ job.title }}
                        </option>
                    </select>
                    <div class="relative sm:w-64">
                        <span
                            class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"
                            >⌕</span
                        >
                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search candidate, title, or job..."
                            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-xs text-slate-800 outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>
                </div>
            </section>

            <section
                class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    v-if="loading"
                    class="p-12 text-center text-sm text-slate-500"
                >
                    Loading direct offers…
                </div>
                <div
                    v-else-if="filteredOffers.length === 0"
                    class="px-6 py-14 text-center"
                >
                    <div
                        class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-xl text-slate-400"
                    >
                        ▤
                    </div>
                    <h2 class="text-sm font-bold text-slate-900">
                        No direct offers match your filters
                    </h2>
                    <p class="mx-auto mt-1 max-w-sm text-xs text-slate-500">
                        Try resetting your status tab or search term, or send a
                        new direct invitation to a public candidate.
                    </p>
                    <button
                        type="button"
                        class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="resetFilters"
                    >
                        Reset Filters
                    </button>
                </div>
                <div v-else class="overflow-x-auto">
                    <table
                        class="w-full min-w-[900px] border-collapse text-left"
                    >
                        <thead
                            class="bg-slate-50/90 text-[11px] font-bold uppercase tracking-wider text-slate-500"
                        >
                            <tr class="border-b border-slate-200">
                                <th class="px-5 py-3.5">Candidate</th>
                                <th class="px-5 py-3.5">Target Role</th>
                                <th class="px-5 py-3.5">Salary Offered</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Sent / Expires</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <tr
                                v-for="offer in filteredOffers"
                                :key="offer.id"
                                class="group transition-colors hover:bg-slate-50/80"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <img
                                            v-if="
                                                offer.job_seeker?.profile
                                                    ?.profile_photo
                                            "
                                            :src="
                                                offer.job_seeker?.profile
                                                    ?.profile_photo
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
                                                initials(
                                                    offer.job_seeker?.name ||
                                                        "Candidate",
                                                )
                                            }}
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="truncate font-semibold text-slate-900"
                                            >
                                                {{
                                                    offer.job_seeker?.name ||
                                                    "Candidate"
                                                }}
                                            </p>
                                            <p
                                                class="mt-0.5 truncate text-xs text-slate-500"
                                            >
                                                {{
                                                    offer.job_seeker?.profile
                                                        ?.professional_title ||
                                                    "Candidate"
                                                }}
                                                <span
                                                    v-if="
                                                        offer.job_seeker
                                                            ?.profile
                                                            ?.experience_years !=
                                                        null
                                                    "
                                                    class="text-slate-400"
                                                    >·
                                                    {{
                                                        offer.job_seeker
                                                            ?.profile
                                                            ?.experience_years
                                                    }}
                                                    yrs exp</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ offer.job?.title || "Job offer" }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span
                                            v-if="offer.job?.employment_type"
                                            class="rounded-md border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700"
                                            >{{
                                                formatEmploymentType(
                                                    offer.job?.employment_type,
                                                )
                                            }}</span
                                        ><span
                                            v-if="offer.job?.workplace_type"
                                            class="text-xs text-slate-500"
                                            >{{
                                                formatWorkplaceType(
                                                    offer.job.workplace_type,
                                                )
                                            }}</span
                                        ><span
                                            v-if="
                                                offer.job?.city ||
                                                offer.job?.country
                                            "
                                            class="text-xs text-slate-500"
                                            >⌖
                                            {{
                                                [
                                                    offer.job?.city,
                                                    offer.job?.country,
                                                ]
                                                    .filter(Boolean)
                                                    .join(", ")
                                            }}</span
                                        >
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="font-bold text-slate-900">{{
                                        formatMoney(offer.salary_offered)
                                    }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
                                        :class="statusClass(offer.status)"
                                        ><span
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="
                                                statusDotClass(offer.status)
                                            "
                                        ></span
                                        >{{ formatStatus(offer.status) }}</span
                                    >
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs">
                                    <p class="text-slate-600">
                                        Sent:
                                        <span
                                            class="font-medium text-slate-800"
                                            >{{
                                                formatDate(offer.created_at)
                                            }}</span
                                        >
                                    </p>
                                    <p
                                        v-if="offer.status === 'pending'"
                                        class="mt-0.5 font-medium text-amber-600"
                                    >
                                        Expires:
                                        {{ formatDate(offer.expires_at) }}
                                    </p>
                                    <p
                                        v-else-if="offer.responded_at"
                                        class="mt-0.5 text-slate-400"
                                    >
                                        Responded:
                                        {{ formatDate(offer.responded_at) }}
                                    </p>
                                    <p v-else class="mt-0.5 text-slate-400">
                                        Expires:
                                        {{ formatDate(offer.expires_at) }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <button
                                            type="button"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:text-indigo-700"
                                            @click="selectedOffer = offer"
                                        >
                                            View</button
                                        ><button
                                            v-if="offer.status === 'pending'"
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                            @click="withdrawTarget = offer"
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
                    v-if="!loading && filteredOffers.length"
                    class="border-t border-slate-100 px-5 py-3 text-xs text-slate-500"
                >
                    Showing
                    <span class="font-semibold text-slate-700">{{
                        filteredOffers.length
                    }}</span>
                    of
                    <span class="font-semibold text-slate-700">{{
                        offers.length
                    }}</span>
                    offers
                </footer>
            </section>
        </main>

        <!-- Offer details slide-over -->
        <Transition name="fade">
            <div
                v-if="selectedOffer"
                class="fixed inset-0 z-40 bg-slate-900/35"
                @click.self="selectedOffer = null"
            >
                <Transition name="slide">
                    <aside
                        v-if="selectedOffer"
                        class="absolute inset-y-0 right-0 flex w-full max-w-xl flex-col bg-white shadow-2xl"
                    >
                        <div
                            class="flex items-start justify-between border-b border-slate-200 px-6 py-5"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-indigo-600"
                                >
                                    Offer details
                                </p>
                                <h2
                                    class="mt-1 text-lg font-bold text-slate-900"
                                >
                                    {{
                                        selectedOffer.job?.title || "Job offer"
                                    }}
                                </h2>
                                <p class="mt-1 text-xs text-slate-500">
                                    Offer #{{ selectedOffer.id }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                aria-label="Close details"
                                @click="selectedOffer = null"
                            >
                                ✕
                            </button>
                        </div>
                        <div class="flex-1 space-y-6 overflow-y-auto p-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="grid h-12 w-12 place-items-center rounded-full bg-indigo-600 font-bold text-white"
                                >
                                    {{
                                        initials(
                                            selectedOffer.job_seeker?.name ||
                                                "Candidate",
                                        )
                                    }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{
                                            selectedOffer.job_seeker?.name ||
                                            "Candidate"
                                        }}
                                    </p>
                                    <p class="text-sm text-slate-500">
                                        {{
                                            selectedOffer.job_seeker?.profile
                                                ?.professional_title ||
                                            "Candidate"
                                        }}
                                    </p>
                                </div>
                                <span
                                    class="ml-auto rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="statusClass(selectedOffer.status)"
                                    >{{
                                        formatStatus(selectedOffer.status)
                                    }}</span
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p class="text-xs text-slate-500">
                                        Salary offered
                                    </p>
                                    <p class="mt-1 font-bold text-slate-900">
                                        {{
                                            formatMoney(
                                                selectedOffer.salary_offered,
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
                                            formatEmploymentType(
                                                selectedOffer.job
                                                    ?.employment_type,
                                            ) || "—"
                                        }}
                                    </p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p class="text-xs text-slate-500">
                                        Sent date
                                    </p>
                                    <p class="mt-1 font-bold text-slate-900">
                                        {{
                                            formatDate(selectedOffer.created_at)
                                        }}
                                    </p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p class="text-xs text-slate-500">
                                        Expires
                                    </p>
                                    <p class="mt-1 font-bold text-slate-900">
                                        {{
                                            formatDate(selectedOffer.expires_at)
                                        }}
                                    </p>
                                </div>
                            </div>
                            <section>
                                <h3 class="text-sm font-bold text-slate-900">
                                    Personal offer note
                                </h3>
                                <p
                                    class="mt-2 whitespace-pre-wrap rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600"
                                >
                                    {{
                                        selectedOffer.offer_details ||
                                        "No additional details were provided."
                                    }}
                                </p>
                            </section>
                            <section v-if="selectedOffer.responded_at">
                                <h3 class="text-sm font-bold text-slate-900">
                                    Response date
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ formatDate(selectedOffer.responded_at) }}
                                </p>
                            </section>
                        </div>
                        <div
                            v-if="selectedOffer.status === 'pending'"
                            class="border-t border-slate-200 p-5"
                        >
                            <button
                                type="button"
                                class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50"
                                @click="
                                    withdrawTarget = selectedOffer;
                                    selectedOffer = null;
                                "
                            >
                                Withdraw offer
                            </button>
                        </div>
                    </aside>
                </Transition>
            </div>
        </Transition>

        <!-- New offer modal. Candidate options are supplied by the parent page. -->
        <Transition name="fade">
            <div
                v-if="showNewOfferModal"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/55 p-4 backdrop-blur-sm"
                @click.self="showNewOfferModal = false"
            >
                <div
                    class="my-8 w-full max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4"
                    >
                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Send New Direct Offer
                            </h2>
                            <p class="text-xs text-slate-500">
                                Invite a public job seeker directly to one of
                                your active jobs.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-200"
                            @click="showNewOfferModal = false"
                        >
                            ✕
                        </button>
                    </div>
                    <form
                        class="space-y-4 p-6"
                        @submit.prevent="submitNewOffer"
                    >
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Public candidate</label
                            ><select
                                v-model="newOfferForm.job_seeker_id"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option value="" disabled>
                                    Select a candidate
                                </option>
                                <option
                                    v-for="candidate in publicCandidates"
                                    :key="candidate.id"
                                    :value="String(candidate.id)"
                                >
                                    {{ candidate.name
                                    }}<template
                                        v-if="
                                            candidate.profile
                                                ?.professional_title
                                        "
                                    >
                                        —
                                        {{
                                            candidate.profile.professional_title
                                        }}</template
                                    >
                                </option>
                            </select>
                            <p
                                v-if="!publicCandidates.length"
                                class="mt-1 text-[11px] text-amber-600"
                            >
                                No public candidates were provided.
                            </p>
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Target job requisition</label
                            ><select
                                v-model="newOfferForm.job_id"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option value="" disabled>
                                    Select a published job
                                </option>
                                <option
                                    v-for="job in availableJobs"
                                    :key="job.id"
                                    :value="String(job.id)"
                                >
                                    {{ job.title
                                    }}<template v-if="job.city">
                                        — {{ job.city }}</template
                                    >
                                </option>
                            </select>
                            <p
                                v-if="selectedJob"
                                class="mt-1 text-xs text-slate-500"
                            >
                                {{
                                    formatEmploymentType(
                                        selectedJob.employment_type,
                                    )
                                }}<span v-if="selectedJob.workplace_type">
                                    ·
                                    {{
                                        formatWorkplaceType(
                                            selectedJob.workplace_type,
                                        )
                                    }}</span
                                ><span
                                    v-if="
                                        selectedJob.city || selectedJob.country
                                    "
                                >
                                    ·
                                    {{
                                        [selectedJob.city, selectedJob.country]
                                            .filter(Boolean)
                                            .join(", ")
                                    }}</span
                                >
                            </p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                    >Salary offered</label
                                ><input
                                    v-model="newOfferForm.salary_offered"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="e.g. 2500"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                    >Offer valid for</label
                                ><select
                                    v-model.number="
                                        newOfferForm.expires_in_days
                                    "
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                >
                                    <option :value="5">5 days</option>
                                    <option :value="7">7 days</option>
                                    <option :value="14">14 days</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Personal pitch
                                <span class="font-normal text-slate-400"
                                    >(offer_details)</span
                                ></label
                            ><textarea
                                v-model="newOfferForm.offer_details"
                                required
                                rows="4"
                                placeholder="Write a personalized note explaining why this candidate is a great fit…"
                                class="w-full rounded-xl border border-slate-300 p-3.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            ></textarea>
                        </div>
                        <div
                            class="flex justify-end gap-2.5 border-t border-slate-100 pt-3"
                        >
                            <button
                                type="button"
                                class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="showNewOfferModal = false"
                            >
                                Cancel</button
                            ><button
                                type="submit"
                                :disabled="creating"
                                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{
                                    creating ? "Sending…" : "Send Direct Offer"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Withdraw confirmation -->
        <Transition name="fade">
            <div
                v-if="withdrawTarget"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/55 p-4"
                @click.self="withdrawTarget = null"
            >
                <div
                    class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
                >
                    <div class="flex items-start gap-3.5">
                        <div
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-rose-100 bg-rose-50 text-lg text-rose-600"
                        >
                            !
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Withdraw Direct Job Offer?
                            </h2>
                            <p
                                class="mt-1 text-sm leading-relaxed text-slate-600"
                            >
                                Are you sure you want to withdraw the offer for
                                <strong class="text-slate-900">{{
                                    withdrawTarget.job?.title || "Job offer"
                                }}</strong>
                                sent to
                                <strong class="text-slate-900">{{
                                    withdrawTarget.job_seeker?.name ||
                                    "Candidate"
                                }}</strong
                                >? The candidate will no longer be able to
                                accept it.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2.5">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            @click="withdrawTarget = null"
                        >
                            Keep Pending</button
                        ><button
                            type="button"
                            :disabled="withdrawingId === withdrawTarget.id"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 disabled:opacity-60"
                            @click="confirmWithdraw"
                        >
                            {{
                                withdrawingId === withdrawTarget.id
                                    ? "Withdrawing…"
                                    : "Yes, Withdraw Offer"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
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
