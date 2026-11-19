<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import type {
    JobOfferStatus,
    JobOffer,
    EmployerOfferJob,
} from "../types/jobOffer.types";

/**
 * Employer direct job-offer dashboard.
 *
 * Data comes from the parent component's existing Laravel queries.
 * Create and withdraw actions are emitted to the parent.
 */

type OfferStatus =
    | "pending"
    | "accepted"
    | "rejected"
    | "expired"
    | "withdrawn"
    | string;

interface OfferJob {
    id: number;
    title: string;
    employment_type?: string | null;
    city?: string | null;
    country?: string | null;
    default_salary?: number | string | null;
}

interface CandidateProfile {
    professional_title?: string | null;
    experience_years?: number | string | null;
    avatar_bg?: string | null;
}

interface OfferCandidate {
    id: number;
    name: string;
    email?: string | null;
    profile?: CandidateProfile | null;
}

interface EmployerJobOffer {
    id: number;
    job_id: number;
    job_seeker_id?: number;
    salary_offered?: number | string | null;
    offer_details?: string | null;
    status: OfferStatus;
    expires_at?: string | null;
    responded_at?: string | null;
    created_at?: string | null;
    job?: OfferJob | null;
    job_seeker?: OfferCandidate | null;
}

interface PublicJobCandidate extends OfferCandidate {}

interface CreateOfferPayload {
    candidate_id: number;
    job_id: number;
    salary_offered: number | null;
    offer_details: string;
}

const props = withDefaults(
    defineProps<{
        offers: EmployerJobOffer[];
        availableJobs: OfferJob[];
        publicCandidates: PublicJobCandidate[];
        companyName?: string;
        companyId?: number | string | null;
        loading?: boolean;
        creating?: boolean;
        withdrawingOfferId?: number | null;
        currencySymbol?: string;
    }>(),
    {
        companyName: "Your company",
        companyId: null,
        loading: false,
        creating: false,
        withdrawingOfferId: null,
        currencySymbol: "$",
    },
);

const emit = defineEmits<{
    create: [payload: CreateOfferPayload];
    withdraw: [offer: EmployerJobOffer];
}>();

// Filters
const statusFilter = ref("all");
const selectedJobFilter = ref("all");
const searchQuery = ref("");

// Drawer and modal
const selectedOffer = ref<EmployerJobOffer | null>(null);
const withdrawTarget = ref<EmployerJobOffer | null>(null);
const showNewOfferModal = ref(false);
const formError = ref("");

// New offer form
const newOfferForm = reactive<{
    candidate_id: number | null;
    job_id: number | null;
    salary_offered: number | null;
    offer_details: string;
}>({
    candidate_id: null,
    job_id: null,
    salary_offered: null,
    offer_details: "",
});

// Company initials
const companyInitials = computed(() => {
    const initials = props.companyName
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join("");

    return initials || "CO";
});

// Dashboard statistics
const stats = computed(() => {
    const total = props.offers.length;

    const pending = props.offers.filter(
        (offer) => offer.status === "pending",
    ).length;

    const accepted = props.offers.filter(
        (offer) => offer.status === "accepted",
    ).length;

    const rejected = props.offers.filter(
        (offer) => offer.status === "rejected",
    ).length;

    const responded = accepted + rejected;

    return {
        total,
        pending,
        accepted,
        acceptanceRate:
            responded > 0 ? Math.round((accepted / responded) * 100) : 0,
    };
});

// Status tabs
const statusTabs = computed(() => [
    {
        key: "all",
        label: "All",
        count: props.offers.length,
    },
    {
        key: "pending",
        label: "Pending",
        count: stats.value.pending,
    },
    {
        key: "accepted",
        label: "Accepted",
        count: stats.value.accepted,
    },
    {
        key: "rejected",
        label: "Rejected",
        count: props.offers.filter((o) => o.status === "rejected").length,
    },
    {
        key: "expired",
        label: "Expired",
        count: props.offers.filter((o) => o.status === "expired").length,
    },
    {
        key: "withdrawn",
        label: "Withdrawn",
        count: props.offers.filter((o) => o.status === "withdrawn").length,
    },
]);

// Search and filtering
const filteredOffers = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase();

    return props.offers.filter((offer) => {
        const matchesStatus =
            statusFilter.value === "all" || offer.status === statusFilter.value;

        const matchesJob =
            selectedJobFilter.value === "all" ||
            String(offer.job_id) === selectedJobFilter.value;

        const searchable = [
            offer.job_seeker?.name,
            offer.job_seeker?.profile?.professional_title,
            offer.job?.title,
        ]
            .filter(Boolean)
            .join(" ")
            .toLocaleLowerCase();

        const matchesSearch = !query || searchable.includes(query);

        return matchesStatus && matchesJob && matchesSearch;
    });
});

// Selected candidate and job
const selectedCandidate = computed(
    () =>
        props.publicCandidates.find(
            (candidate) => candidate.id === newOfferForm.candidate_id,
        ) ?? null,
);

const selectedJob = computed(
    () =>
        props.availableJobs.find((job) => job.id === newOfferForm.job_id) ??
        null,
);

// Automatically select first candidate
watch(
    () => props.publicCandidates,
    (candidates) => {
        if (newOfferForm.candidate_id === null && candidates.length) {
            newOfferForm.candidate_id = candidates[0]?.id ?? null;
        }
    },
    { immediate: true },
);

// Automatically select first job
watch(
    () => props.availableJobs,
    (jobs) => {
        if (newOfferForm.job_id === null && jobs.length) {
            newOfferForm.job_id = jobs[0]?.id ?? null;

            newOfferForm.salary_offered = toNullableNumber(
                jobs[0]?.default_salary,
            );
        }
    },
    { immediate: true },
);

// Convert salary to number or null
function toNullableNumber(
    value: number | string | null | undefined,
): number | null {
    if (value === null || value === undefined || value === "") {
        return null;
    }

    const parsed = Number(value);

    return Number.isFinite(parsed) ? parsed : null;
}

// Candidate initials
function getInitials(name?: string | null): string {
    if (!name?.trim()) return "?";

    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join("");
}

// Employment type formatter
function formatEmploymentType(type?: string | null): string {
    if (!type) return "Not specified";

    const labels: Record<string, string> = {
        full_time: "Full-time",
        part_time: "Part-time",
        contract: "Contract",
        internship: "Internship",
        temporary: "Temporary",
        freelance: "Freelance",
    };

    return (
        labels[type] ??
        type
            .replaceAll("_", " ")
            .replace(/\b\w/g, (letter) => letter.toUpperCase())
    );
}

// Status badge colors
function statusBadgeClass(status: string): string | undefined {
    const classes: Record<string, string> = {
        pending: "bg-amber-50 text-amber-700 border-amber-200",
        accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
        rejected: "bg-rose-50 text-rose-700 border-rose-200",
        expired: "bg-slate-100 text-slate-600 border-slate-200",
        withdrawn: "bg-slate-100 text-slate-500 border-slate-200",
    };

    return classes[status] ?? classes.pending;
}

function statusDotClass(status: string): string {
    const classes: Record<string, string> = {
        pending: "bg-amber-500",
        accepted: "bg-emerald-500",
        rejected: "bg-rose-500",
        expired: "bg-slate-400",
        withdrawn: "bg-slate-400",
    };

    return classes[status] ?? "bg-slate-400";
}

// Date formatting
function formatDate(value?: string | null): string {
    if (!value) return "—";

    const date = parseSqlDate(value);

    if (!date) return value;

    return new Intl.DateTimeFormat(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(date);
}

function formatDateTime(value?: string | null): string {
    if (!value) return "—";

    const date = parseSqlDate(value);

    if (!date) return value;

    return new Intl.DateTimeFormat(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
    }).format(date);
}

function parseSqlDate(value: string): Date | null {
    const normalized = value.includes("T") ? value : value.replace(" ", "T");

    const date = new Date(normalized);

    return Number.isNaN(date.getTime()) ? null : date;
}

// Salary formatting
function formatSalary(value?: number | string | null): string {
    const amount = toNullableNumber(value);

    if (amount === null) return "Negotiable";

    return `${props.currencySymbol}${amount.toLocaleString(undefined, {
        maximumFractionDigits: 2,
    })}`;
}

// Reset filters
function resetFilters(): void {
    statusFilter.value = "all";
    selectedJobFilter.value = "all";
    searchQuery.value = "";
}

// Open new offer modal
function openNewOfferModal(): void {
    formError.value = "";

    if (newOfferForm.candidate_id === null && props.publicCandidates.length) {
        newOfferForm.candidate_id = props.publicCandidates[0]?.id ?? null;
    }

    if (newOfferForm.job_id === null && props.availableJobs.length) {
        newOfferForm.job_id = props.availableJobs[0]?.id ?? null;
    }

    onSelectNewOfferJob();

    showNewOfferModal.value = true;
}

// Update salary when job changes
function onSelectNewOfferJob(): void {
    const job = selectedJob.value;

    if (job) {
        newOfferForm.salary_offered = toNullableNumber(job.default_salary);
    }
}

// Auto-fill pitch
function fillQuickPitch(): void {
    const candidate = selectedCandidate.value;
    const job = selectedJob.value;

    if (!candidate || !job) return;

    const firstName = candidate.name.trim().split(/\s+/)[0] || candidate.name;

    const years = candidate.profile?.experience_years;

    const title =
        candidate.profile?.professional_title || "your professional background";

    const experience =
        years !== null && years !== undefined && years !== ""
            ? `${years} years of experience as a ${title}`
            : title;

    newOfferForm.offer_details = `Hi ${firstName}, we reviewed your public profile and were impressed by ${experience}. We would love to invite you to join our team as our ${job.title}.`;
}

// Submit offer
function submitNewOffer(): void {
    formError.value = "";

    if (!newOfferForm.candidate_id) {
        formError.value = "Please select a candidate.";
        return;
    }

    if (!newOfferForm.job_id) {
        formError.value = "Please select a job.";
        return;
    }

    if (
        newOfferForm.salary_offered !== null &&
        newOfferForm.salary_offered < 0
    ) {
        formError.value = "Salary cannot be negative.";
        return;
    }

    if (!newOfferForm.offer_details.trim()) {
        formError.value = "Please enter the offer details.";
        return;
    }

    emit("create", {
        candidate_id: newOfferForm.candidate_id,
        job_id: newOfferForm.job_id,
        salary_offered: toNullableNumber(newOfferForm.salary_offered),
        offer_details: newOfferForm.offer_details.trim(),
    });
}

// Confirm withdrawal
function confirmWithdraw(): void {
    if (!withdrawTarget.value) return;

    emit("withdraw", withdrawTarget.value);

    withdrawTarget.value = null;
}

// Close drawer
function closeDrawer(): void {
    selectedOffer.value = null;
}

// Close new offer modal after successful API request
function closeNewOfferModal(): void {
    showNewOfferModal.value = false;
    formError.value = "";
}

defineExpose({
    closeNewOfferModal,
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 antialiased">
        <!-- Employer Portal Header -->
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-200"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span
                                class="truncate text-base font-bold tracking-tight text-slate-900 sm:text-lg"
                            >
                                TalentBridge
                            </span>

                            <span
                                class="hidden rounded-md border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 sm:inline-flex"
                            >
                                Employer Portal
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p
                            class="max-w-56 truncate text-xs font-semibold text-slate-800"
                        >
                            {{ companyName }}
                        </p>

                        <p class="text-[11px] text-slate-500">
                            Hiring Team
                            <span v-if="companyId !== null">
                                · Company ID #{{ companyId }}
                            </span>
                        </p>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white shadow-sm"
                    >
                        {{ companyInitials }}
                    </div>
                </div>
            </div>
        </header>

        <main
            class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8"
        >
            <!-- Page Header -->
            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <nav
                        class="mb-1 flex flex-wrap items-center gap-1.5 text-xs font-medium text-slate-500"
                        aria-label="Breadcrumb"
                    >
                        <span>Employer Dashboard</span>
                        <span>/</span>
                        <span>Recruitment Pipeline</span>
                        <span>/</span>
                        <span class="font-semibold text-indigo-600">
                            Direct Job Offers
                        </span>
                    </nav>

                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        Direct Job Offers
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Track invitations sent to public job seekers and manage
                        candidate responses
                    </p>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 self-start rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 active:bg-indigo-800 sm:self-auto disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="
                        !availableJobs.length || !publicCandidates.length
                    "
                    @click="openNewOfferModal"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.4"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    New Direct Offer
                </button>
            </div>

            <!-- KPI Statistics -->
            <section
                class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                aria-label="Offer statistics"
            >
                <!-- Total Sent -->
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-slate-500"
                        >
                            Total Sent
                        </p>

                        <p
                            class="mt-1.5 text-2xl font-bold text-slate-900 sm:text-3xl"
                        >
                            {{ stats.total }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            All direct invitations
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                            />
                        </svg>
                    </div>
                </article>

                <!-- Pending -->
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                        >
                            Awaiting Response
                        </p>

                        <p
                            class="mt-1.5 text-2xl font-bold text-slate-900 sm:text-3xl"
                        >
                            {{ stats.pending }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Active pending offers
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-100 bg-amber-50 text-amber-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </article>

                <!-- Accepted -->
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                        >
                            Accepted Offers
                        </p>

                        <p
                            class="mt-1.5 text-2xl font-bold text-slate-900 sm:text-3xl"
                        >
                            {{ stats.accepted }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Ready for onboarding
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </article>

                <!-- Acceptance Rate -->
                <article
                    class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-indigo-600"
                        >
                            Acceptance Rate
                        </p>

                        <p
                            class="mt-1.5 text-2xl font-bold text-slate-900 sm:text-3xl"
                        >
                            {{ stats.acceptanceRate }}%
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Of accepted or rejected offers
                        </p>
                    </div>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-100 bg-indigo-50 text-indigo-600"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                            />
                        </svg>
                    </div>
                </article>
            </section>

            <!-- Search and Filters -->
            <section
                class="mt-6 flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm xl:flex-row xl:items-center"
            >
                <!-- Status Tabs -->
                <div
                    class="flex items-center gap-1.5 overflow-x-auto pb-1 xl:pb-0"
                >
                    <button
                        v-for="tab in statusTabs"
                        :key="tab.key"
                        type="button"
                        class="flex shrink-0 items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-semibold capitalize transition"
                        :class="
                            statusFilter === tab.key
                                ? 'bg-slate-900 text-white shadow-sm'
                                : 'bg-slate-100/80 text-slate-600 hover:bg-slate-200/70 hover:text-slate-900'
                        "
                        @click="statusFilter = tab.key"
                    >
                        <span>{{ tab.label }}</span>

                        <span
                            class="rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none"
                            :class="
                                statusFilter === tab.key
                                    ? 'bg-white/20 text-white'
                                    : 'border border-slate-200/80 bg-white text-slate-600'
                            "
                        >
                            {{ tab.count }}
                        </span>
                    </button>
                </div>

                <!-- Job Filter and Search -->
                <div
                    class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center"
                >
                    <select
                        v-model="selectedJobFilter"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-medium text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 sm:w-56"
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
                        <svg
                            class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>

                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search candidate, title, or job..."
                            class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-8 text-xs text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        />

                        <button
                            v-if="searchQuery"
                            type="button"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-slate-600"
                            aria-label="Clear search"
                            @click="searchQuery = ''"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </section>

            <!-- Offers Table -->
            <section
                class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <!-- Loading State -->
                <div
                    v-if="loading"
                    class="px-6 py-16 text-center text-sm text-slate-500"
                    role="status"
                >
                    Loading direct job offers…
                </div>

                <!-- Empty State -->
                <div
                    v-else-if="filteredOffers.length === 0"
                    class="px-6 py-14 text-center"
                >
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                            />
                        </svg>
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
                        class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                        @click="resetFilters"
                    >
                        Reset Filters
                    </button>
                </div>

                <!-- Data Table -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50/90 text-[11px] font-bold uppercase tracking-wider text-slate-500"
                            >
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
                                <!-- Candidate -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white shadow-sm"
                                            :class="
                                                offer.job_seeker?.profile
                                                    ?.avatar_bg ||
                                                'bg-indigo-600'
                                            "
                                        >
                                            {{
                                                getInitials(
                                                    offer.job_seeker?.name,
                                                )
                                            }}
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate font-semibold text-slate-900"
                                            >
                                                {{
                                                    offer.job_seeker?.name ||
                                                    `Candidate #${offer.job_seeker_id ?? "—"}`
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
                                                            ?.experience_years !==
                                                            null &&
                                                        offer.job_seeker
                                                            ?.profile
                                                            ?.experience_years !==
                                                            undefined
                                                    "
                                                    class="mx-1 text-slate-400"
                                                >
                                                    ·
                                                </span>

                                                <span
                                                    v-if="
                                                        offer.job_seeker
                                                            ?.profile
                                                            ?.experience_years !==
                                                            null &&
                                                        offer.job_seeker
                                                            ?.profile
                                                            ?.experience_years !==
                                                            undefined
                                                    "
                                                    class="font-medium text-slate-600"
                                                >
                                                    {{
                                                        offer.job_seeker.profile
                                                            .experience_years
                                                    }}
                                                    yrs exp
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Target Role -->
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{
                                            offer.job?.title ||
                                            `Job #${offer.job_id}`
                                        }}
                                    </p>

                                    <div class="mt-1 flex items-center gap-2">
                                        <span
                                            class="inline-flex rounded-md border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700"
                                        >
                                            {{
                                                formatEmploymentType(
                                                    offer.job?.employment_type,
                                                )
                                            }}
                                        </span>

                                        <span
                                            v-if="offer.job?.city"
                                            class="flex items-center gap-1 text-xs text-slate-500"
                                        >
                                            <svg
                                                class="h-3.5 w-3.5 text-slate-400"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>

                                            {{ offer.job.city }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Salary -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        v-if="
                                            offer.salary_offered !== null &&
                                            offer.salary_offered !==
                                                undefined &&
                                            offer.salary_offered !== ''
                                        "
                                        class="font-bold text-slate-900"
                                    >
                                        {{ formatSalary(offer.salary_offered) }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-xs font-medium italic text-slate-400"
                                    >
                                        Negotiable
                                    </span>

                                    <span
                                        v-if="
                                            offer.salary_offered !== null &&
                                            offer.salary_offered !==
                                                undefined &&
                                            offer.salary_offered !== ''
                                        "
                                        class="text-xs text-slate-500"
                                    >
                                        / mo
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold capitalize"
                                        :class="statusBadgeClass(offer.status)"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="
                                                statusDotClass(offer.status)
                                            "
                                        ></span>

                                        {{ offer.status }}
                                    </span>
                                </td>

                                <!-- Dates -->
                                <td class="whitespace-nowrap px-5 py-4 text-xs">
                                    <p class="text-slate-600">
                                        Sent:
                                        <span
                                            class="font-medium text-slate-800"
                                        >
                                            {{ formatDate(offer.created_at) }}
                                        </span>
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
                                        {{
                                            offer.status === "withdrawn"
                                                ? "Withdrawn"
                                                : "Responded"
                                        }}:
                                        {{ formatDate(offer.responded_at) }}
                                    </p>

                                    <p
                                        v-else-if="offer.expires_at"
                                        class="mt-0.5 text-slate-400"
                                    >
                                        Expired:
                                        {{ formatDate(offer.expires_at) }}
                                    </p>
                                </td>

                                <!-- Actions -->
                                <td
                                    class="whitespace-nowrap px-5 py-4 text-right"
                                >
                                    <div
                                        class="inline-flex items-center justify-end gap-2"
                                    >
                                        <button
                                            type="button"
                                            class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100"
                                            @click="selectedOffer = offer"
                                        >
                                            View Pitch
                                        </button>

                                        <button
                                            v-if="offer.status === 'pending'"
                                            type="button"
                                            class="rounded-xl border border-rose-200 bg-rose-50/50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100/80 disabled:cursor-not-allowed disabled:opacity-50"
                                            :disabled="
                                                withdrawingOfferId === offer.id
                                            "
                                            @click="withdrawTarget = offer"
                                        >
                                            {{
                                                withdrawingOfferId === offer.id
                                                    ? "Withdrawing…"
                                                    : "Withdraw"
                                            }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- View Pitch Drawer -->
        <Transition name="drawer">
            <div
                v-if="selectedOffer"
                class="fixed inset-0 z-50 flex justify-end bg-slate-900/50 backdrop-blur-[2px]"
                @click.self="closeDrawer"
            >
                <Transition name="drawer-panel" appear>
                    <aside
                        class="flex h-full w-full max-w-lg flex-col justify-between overflow-y-auto border-l border-slate-200 bg-white shadow-2xl"
                        aria-label="Offer details"
                    >
                        <div class="p-6 sm:p-7">
                            <!-- Drawer Header -->
                            <div
                                class="flex items-center justify-between border-b border-slate-100 pb-4"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-md border border-indigo-100 bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-indigo-700"
                                    >
                                        Offer #{{ selectedOffer.id }}
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        Direct Invitation
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-xl p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="Close offer details"
                                    @click="closeDrawer"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Candidate Summary -->
                            <div
                                class="mt-5 flex items-center gap-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4"
                            >
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white shadow-sm"
                                    :class="
                                        selectedOffer.job_seeker?.profile
                                            ?.avatar_bg || 'bg-indigo-600'
                                    "
                                >
                                    {{
                                        getInitials(
                                            selectedOffer.job_seeker?.name,
                                        )
                                    }}
                                </div>

                                <div class="min-w-0">
                                    <h2
                                        class="truncate text-base font-bold text-slate-900"
                                    >
                                        {{
                                            selectedOffer.job_seeker?.name ||
                                            `Candidate #${selectedOffer.job_seeker_id ?? "—"}`
                                        }}
                                    </h2>

                                    <p class="truncate text-xs text-slate-600">
                                        {{
                                            selectedOffer.job_seeker?.profile
                                                ?.professional_title ||
                                            "Candidate"
                                        }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-[11px] text-slate-400"
                                    >
                                        {{
                                            selectedOffer.job_seeker?.profile
                                                ?.experience_years ?? "—"
                                        }}
                                        yrs experience

                                        <span
                                            v-if="
                                                selectedOffer.job_seeker?.email
                                            "
                                        >
                                            ·
                                            {{ selectedOffer.job_seeker.email }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Offer Information -->
                            <div class="mt-6 space-y-3">
                                <h3
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Offer Terms &amp; Timeline
                                </h3>

                                <div
                                    class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white text-sm"
                                >
                                    <div
                                        class="flex items-center justify-between px-4 py-3"
                                    >
                                        <span
                                            class="text-xs font-medium text-slate-500"
                                        >
                                            Current Status
                                        </span>

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize"
                                            :class="
                                                statusBadgeClass(
                                                    selectedOffer.status,
                                                )
                                            "
                                        >
                                            <span
                                                class="h-1.5 w-1.5 rounded-full"
                                                :class="
                                                    statusDotClass(
                                                        selectedOffer.status,
                                                    )
                                                "
                                            ></span>

                                            {{ selectedOffer.status }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 px-4 py-3"
                                    >
                                        <span class="text-xs text-slate-500">
                                            Target Position
                                        </span>

                                        <span
                                            class="text-right font-semibold text-slate-900"
                                        >
                                            {{
                                                selectedOffer.job?.title ||
                                                `Job #${selectedOffer.job_id}`
                                            }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 px-4 py-3"
                                    >
                                        <span class="text-xs text-slate-500">
                                            Work Arrangement
                                        </span>

                                        <span
                                            class="text-right text-xs font-medium text-slate-700"
                                        >
                                            {{
                                                formatEmploymentType(
                                                    selectedOffer.job
                                                        ?.employment_type,
                                                )
                                            }}

                                            <span
                                                v-if="selectedOffer.job?.city"
                                            >
                                                · {{ selectedOffer.job.city }}
                                            </span>
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 px-4 py-3"
                                    >
                                        <span class="text-xs text-slate-500">
                                            Salary Offered
                                        </span>

                                        <span
                                            class="text-right font-bold text-emerald-700"
                                        >
                                            {{
                                                formatSalary(
                                                    selectedOffer.salary_offered,
                                                )
                                            }}

                                            <span
                                                v-if="
                                                    selectedOffer.salary_offered !==
                                                        null &&
                                                    selectedOffer.salary_offered !==
                                                        undefined &&
                                                    selectedOffer.salary_offered !==
                                                        ''
                                                "
                                            >
                                                / month
                                            </span>
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 px-4 py-3"
                                    >
                                        <span class="text-xs text-slate-500">
                                            Sent Timestamp
                                        </span>

                                        <span
                                            class="text-right text-xs font-medium text-slate-700"
                                        >
                                            {{
                                                formatDateTime(
                                                    selectedOffer.created_at,
                                                )
                                            }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-4 px-4 py-3"
                                    >
                                        <span class="text-xs text-slate-500">
                                            {{
                                                selectedOffer.status ===
                                                "pending"
                                                    ? "Expires At"
                                                    : "Responded / Closed At"
                                            }}
                                        </span>

                                        <span
                                            class="text-right text-xs font-medium"
                                            :class="
                                                selectedOffer.status ===
                                                'pending'
                                                    ? 'font-semibold text-amber-600'
                                                    : 'text-slate-700'
                                            "
                                        >
                                            {{
                                                formatDateTime(
                                                    selectedOffer.status ===
                                                        "pending"
                                                        ? selectedOffer.expires_at
                                                        : selectedOffer.responded_at ||
                                                              selectedOffer.expires_at,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pitch Message -->
                            <div class="mt-6">
                                <h3
                                    class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Pitch Message
                                </h3>

                                <div
                                    class="whitespace-pre-line rounded-2xl border border-slate-200/80 bg-slate-50 p-4 text-sm leading-relaxed text-slate-700"
                                >
                                    {{
                                        selectedOffer.offer_details ||
                                        "No custom pitch message was attached to this offer."
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- Drawer Footer -->
                        <div
                            class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-5 sm:px-7"
                        >
                            <button
                                v-if="selectedOffer.status === 'pending'"
                                type="button"
                                class="rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 disabled:opacity-50"
                                :disabled="
                                    withdrawingOfferId === selectedOffer.id
                                "
                                @click="withdrawTarget = selectedOffer"
                            >
                                Withdraw Offer
                            </button>

                            <a
                                v-else-if="
                                    selectedOffer.status === 'accepted' &&
                                    selectedOffer.job_seeker?.email
                                "
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                                :href="`mailto:${selectedOffer.job_seeker.email}?subject=${encodeURIComponent(`Next Steps: ${selectedOffer.job?.title || 'Job offer'}`)}`"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>

                                Contact Candidate via Email
                            </a>

                            <button
                                type="button"
                                class="ml-auto rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800"
                                @click="closeDrawer"
                            >
                                Close
                            </button>
                        </div>
                    </aside>
                </Transition>
            </div>
        </Transition>

        <!-- New Direct Offer Modal -->
        <Transition name="fade">
            <div
                v-if="showNewOfferModal"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/55 p-4 backdrop-blur-[2px]"
                @click.self="closeNewOfferModal"
            >
                <section
                    class="my-8 w-full max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="new-offer-title"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4"
                    >
                        <div>
                            <h2
                                id="new-offer-title"
                                class="text-base font-bold text-slate-900"
                            >
                                Send New Direct Offer
                            </h2>

                            <p class="text-xs text-slate-500">
                                Invite a public job seeker directly to one of
                                your active jobs
                            </p>
                        </div>

                        <button
                            type="button"
                            class="rounded-xl p-1.5 text-slate-400 transition hover:bg-slate-200/60 hover:text-slate-700"
                            aria-label="Close new offer form"
                            @click="closeNewOfferModal"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <form
                        class="space-y-4 p-6"
                        @submit.prevent="submitNewOffer"
                    >
                        <!-- Candidate -->
                        <div>
                            <label
                                for="offer-candidate"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Public Candidate
                            </label>

                            <select
                                id="offer-candidate"
                                v-model.number="newOfferForm.candidate_id"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option :value="null" disabled>
                                    Select a candidate
                                </option>

                                <option
                                    v-for="candidate in publicCandidates"
                                    :key="candidate.id"
                                    :value="candidate.id"
                                >
                                    {{
                                        candidate.profile?.professional_title
                                            ? `${candidate.name} — ${candidate.profile.professional_title}`
                                            : candidate.name
                                    }}
                                </option>
                            </select>
                        </div>

                        <!-- Job -->
                        <div>
                            <label
                                for="offer-job"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Target Job
                            </label>

                            <select
                                id="offer-job"
                                v-model.number="newOfferForm.job_id"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                @change="onSelectNewOfferJob"
                            >
                                <option :value="null" disabled>
                                    Select a job
                                </option>

                                <option
                                    v-for="job in availableJobs"
                                    :key="job.id"
                                    :value="job.id"
                                >
                                    {{
                                        `${job.title} (${formatEmploymentType(job.employment_type)}${job.city ? ` · ${job.city}` : ""})`
                                    }}
                                </option>
                            </select>
                        </div>

                        <!-- Salary -->
                        <div>
                            <label
                                for="offer-salary"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Salary Offered
                            </label>

                            <input
                                id="offer-salary"
                                v-model.number="newOfferForm.salary_offered"
                                type="number"
                                min="0"
                                step="any"
                                placeholder="Leave blank for negotiable"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>

                        <!-- Pitch -->
                        <div>
                            <div
                                class="mb-1.5 flex items-center justify-between gap-3"
                            >
                                <label
                                    for="offer-pitch"
                                    class="block text-xs font-semibold text-slate-700"
                                >
                                    Personal Pitch
                                </label>

                                <button
                                    type="button"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-700"
                                    @click="fillQuickPitch"
                                >
                                    Auto-fill Pitch
                                </button>
                            </div>

                            <textarea
                                id="offer-pitch"
                                v-model="newOfferForm.offer_details"
                                rows="4"
                                required
                                placeholder="Write a personalized note explaining why this candidate is a great fit…"
                                class="w-full rounded-xl border border-slate-300 p-3.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            ></textarea>
                        </div>

                        <!-- Validation Error -->
                        <p
                            v-if="formError"
                            class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700"
                            role="alert"
                        >
                            {{ formError }}
                        </p>

                        <p class="text-[11px] text-slate-400">
                            Offer expiry is managed by the existing offer API.
                            The dashboard displays the returned expires_at
                            value.
                        </p>

                        <!-- Buttons -->
                        <div
                            class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-3"
                        >
                            <button
                                type="button"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="closeNewOfferModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="
                                    creating ||
                                    !availableJobs.length ||
                                    !publicCandidates.length
                                "
                            >
                                {{
                                    creating ? "Sending…" : "Send Direct Offer"
                                }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </Transition>

        <!-- Withdraw Confirmation Modal -->
        <Transition name="fade">
            <div
                v-if="withdrawTarget"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/55 p-4 backdrop-blur-[2px]"
                @click.self="withdrawTarget = null"
            >
                <section
                    class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="withdraw-title"
                >
                    <div class="flex items-start gap-3.5">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-rose-100 bg-rose-50 text-rose-600"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.667c-.77 1.333.192 2.333 1.732 2.333z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h2
                                id="withdraw-title"
                                class="text-base font-bold text-slate-900"
                            >
                                Withdraw Direct Job Offer?
                            </h2>

                            <p
                                class="mt-1 text-xs leading-relaxed text-slate-600"
                            >
                                Are you sure you want to withdraw the offer for
                                <strong class="text-slate-900">
                                    {{
                                        withdrawTarget.job?.title ||
                                        `Job #${withdrawTarget.job_id}`
                                    }}
                                </strong>
                                sent to
                                <strong class="text-slate-900">
                                    {{
                                        withdrawTarget.job_seeker?.name ||
                                        "this candidate"
                                    }} </strong
                                >? The candidate will no longer be able to
                                accept it.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2.5">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            @click="withdrawTarget = null"
                        >
                            Keep Pending
                        </button>

                        <button
                            type="button"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700 disabled:opacity-60"
                            :disabled="withdrawingOfferId === withdrawTarget.id"
                            @click="confirmWithdraw"
                        >
                            {{
                                withdrawingOfferId === withdrawTarget.id
                                    ? "Withdrawing…"
                                    : "Yes, Withdraw Offer"
                            }}
                        </button>
                    </div>
                </section>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.drawer-enter-active,
.drawer-leave-active,
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.drawer-enter-from,
.drawer-leave-to,
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.drawer-panel-enter-active,
.drawer-panel-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.drawer-panel-enter-from,
.drawer-panel-leave-to {
    transform: translateX(100%);
}
</style>
