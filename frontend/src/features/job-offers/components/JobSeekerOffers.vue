<script setup lang="ts">
import { computed, ref } from "vue";
import DeclineOfferModal from "../components/DeclineOfferModal.vue";
import JobOfferCard from "../components/JobOfferCard.vue";
import type { JobOffer, JobOfferFilter } from "../types/jobOffer.types";

const props = withDefaults(
    defineProps<{
        offers: JobOffer[];
        loading?: boolean;
        error?: string | null;
        respondingOfferId?: number | null;
    }>(),
    {
        loading: false,
        error: null,
        respondingOfferId: null,
    },
);

const emit = defineEmits<{
    (event: "accept", id: number): void;
    (event: "reject", id: number): void;
    (event: "retry"): void;
}>();

const activeFilter = ref<JobOfferFilter>("all");
const declineTarget = ref<JobOffer | null>(null);

const filters: { value: JobOfferFilter; label: string }[] = [
    { value: "all", label: "All offers" },
    { value: "pending", label: "Pending" },
    { value: "accepted", label: "Accepted" },
    { value: "rejected", label: "Declined" },
    { value: "expired", label: "Expired" },
];

const counts = computed(() => ({
    all: props.offers.length,
    pending: props.offers.filter((offer) => offer.status === "pending").length,
    accepted: props.offers.filter((offer) => offer.status === "accepted")
        .length,
    rejected: props.offers.filter((offer) => offer.status === "rejected")
        .length,
    expired: props.offers.filter((offer) => offer.status === "expired").length,
}));

const filteredOffers = computed(() =>
    activeFilter.value === "all"
        ? props.offers
        : props.offers.filter((offer) => offer.status === activeFilter.value),
);

function requestDecline(offer: JobOffer) {
    declineTarget.value = offer;
}

function confirmDecline() {
    if (!declineTarget.value) return;
    emit("reject", declineTarget.value.id);
    declineTarget.value = null;
}
</script>

<template>
    <main class="mx-auto w-full max-w-5xl flex-1 px-4 sm:px-6 lg:px-8">
        <div
            class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1
                        class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl"
                    >
                        Direct Job Offers
                    </h1>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
                        Offers inbox
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                    Review offers sent directly by employers and respond before
                    they expire.
                </p>
            </div>
            <div
                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm"
            >
                <p class="text-xs font-semibold text-slate-700">
                    Pending responses
                </p>
                <p class="mt-0.5 text-lg font-bold text-indigo-600">
                    {{ counts.pending }}
                </p>
            </div>
        </div>

        <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
            <button
                v-for="filter in filters"
                :key="filter.value"
                type="button"
                class="shrink-0 rounded-xl border px-4 py-2 text-xs font-semibold transition sm:text-sm"
                :class="
                    activeFilter === filter.value
                        ? 'border-slate-900 bg-slate-900 text-white shadow-sm'
                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                "
                @click="activeFilter = filter.value"
            >
                {{ filter.label }} ({{ counts[filter.value] }})
            </button>
        </div>

        <div v-if="loading" class="space-y-4">
            <div
                v-for="item in 3"
                :key="item"
                class="h-48 animate-pulse rounded-2xl border border-slate-200 bg-white"
            />
        </div>

        <div
            v-else-if="error"
            class="rounded-2xl border border-rose-200 bg-white p-8 text-center"
        >
            <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"
            >
                !
            </div>
            <h2 class="text-sm font-bold text-slate-900">
                Couldn't load your offers
            </h2>
            <p class="mt-1 text-xs text-slate-500">{{ error }}</p>
            <button
                type="button"
                class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                @click="emit('retry')"
            >
                Try again
            </button>
        </div>

        <div v-else-if="filteredOffers.length" class="space-y-4">
            <JobOfferCard
                v-for="offer in filteredOffers"
                :key="offer.id"
                :offer="offer"
                :responding="respondingOfferId === offer.id"
                @accept="emit('accept', $event)"
                @reject="requestDecline"
            />
        </div>

        <div
            v-else
            class="rounded-2xl border border-slate-200 bg-white p-10 text-center"
        >
            <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
            >
                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5m16 0h-2.586a1 1 0 0 0-.707.293l-2.414 2.414a1 1 0 0 1-.707.293h-3.172a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 0 6.586 13H4"
                    />
                </svg>
            </div>
            <h2 class="text-sm font-bold text-slate-900">
                No offers in this category
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Try another filter. New direct offers from employers will appear
                here.
            </p>
        </div>

        <DeclineOfferModal
            :open="Boolean(declineTarget)"
            :offer-title="declineTarget?.job?.title"
            :submitting="
                declineTarget ? respondingOfferId === declineTarget.id : false
            "
            @close="declineTarget = null"
            @confirm="confirmDecline"
        />
    </main>
</template>
