<script setup lang="ts">
import { computed, ref } from "vue";
import { toast } from "vue3-toastify";

import JobOfferStats from "../components/JobOfferStats.vue";
import JobOfferFilters from "../components/JobOfferFilters.vue";
import JobOfferTable from "../components/JobOfferTable.vue";
import JobOfferDetailsDrawer from "../components/JobOfferDetailsDrawer.vue";
import JobOfferResponseModal from "../components/JobOfferResponseModal.vue";

import { useJobOffers } from "../composables/useJobOffers";
import type {
    JobOffer,
    JobOfferFilter,
    JobOfferResponsePayload,
} from "../types/jobOffer.types";

const { getMyOffers, respondToJobOffer } = useJobOffers();

const offersQuery = getMyOffers();
const offers = computed<JobOffer[]>(() => offersQuery.data.value ?? []);
const loading = computed(() => offersQuery.isFetching.value);
const responding = computed(() => respondToJobOffer.isPending.value);

const statusFilter = ref<JobOfferFilter>("all");
const searchQuery = ref("");
const selectedOffer = ref<JobOffer | null>(null);
const confirmationOffer = ref<JobOffer | null>(null);
const confirmationAction = ref<"accept" | "reject" | null>(null);

const filteredOffers = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase();

    return offers.value.filter((offer) => {
        const matchesStatus =
            statusFilter.value === "all" || offer.status === statusFilter.value;

        const matchesSearch =
            !query ||
            [offer.company?.name, offer.job?.title, offer.offer_details].some(
                (value) => (value ?? "").toLocaleLowerCase().includes(query),
            );

        return matchesStatus && matchesSearch;
    });
});

function apiErrorMessage(error: unknown): string {
    const response = (
        error as {
            response?: {
                data?: {
                    message?: string;
                    errors?: Record<string, string[] | string>;
                };
            };
        }
    )?.response?.data;

    const firstError = response?.errors
        ? Object.values(response.errors).flat()[0]
        : null;

    return (
        (typeof firstError === "string" ? firstError : null) ||
        response?.message ||
        "Something went wrong. Please try again."
    );
}

function askResponse(offer: JobOffer, action: "accept" | "reject") {
    selectedOffer.value = null;
    confirmationOffer.value = offer;
    confirmationAction.value = action;
}

async function confirmResponse() {
    if (!confirmationOffer.value || !confirmationAction.value) return;

    const offer = confirmationOffer.value;
    const action = confirmationAction.value;

    const payload: JobOfferResponsePayload = {
        status: action === "accept" ? "accepted" : "rejected",
    };

    try {
        await respondToJobOffer.mutateAsync({
            offerId: offer.id,
            payload,
        });

        confirmationOffer.value = null;
        confirmationAction.value = null;

        toast.success(
            action === "accept"
                ? "You accepted the job offer."
                : "You declined the job offer.",
        );
    } catch (error) {
        toast.error(apiErrorMessage(error));
    }
}

async function refreshOffers() {
    await offersQuery.refetch();
}
</script>

<template>
    <main class="min-h-full bg-slate-50 text-slate-800">
        <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <header
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <!-- <nav
                        class="mb-1 flex items-center gap-1.5 text-xs font-medium text-slate-500"
                    >
                        <span>Job Seeker Dashboard</span>
                        <span>/</span>
                        <span class="font-semibold text-indigo-600"
                            >Job Offers</span
                        >
                    </nav> -->
                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        My Job Offers
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Review offers from employers and manage your responses.
                    </p>
                </div>

                <button
                    type="button"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    @click="refreshOffers"
                >
                    Refresh offers
                </button>
            </header>

            <div
                v-if="offersQuery.isError.value"
                class="mt-5 flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
            >
                <span>{{ apiErrorMessage(offersQuery.error.value) }}</span>
                <button class="font-semibold underline" @click="refreshOffers">
                    Retry
                </button>
            </div>

            <JobOfferStats :offers="offers" mode="jobseeker" />

            <JobOfferFilters
                v-model:status-filter="statusFilter"
                v-model:search-query="searchQuery"
                :offers="offers"
                mode="jobseeker"
            />

            <JobOfferTable
                :offers="filteredOffers"
                mode="jobseeker"
                :loading="loading"
                @view="selectedOffer = $event"
                @refresh="refreshOffers"
            />
        </div>

        <JobOfferDetailsDrawer
            :offer="selectedOffer"
            mode="jobseeker"
            :responding="responding"
            @close="selectedOffer = null"
            @accept="askResponse($event, 'accept')"
            @reject="askResponse($event, 'reject')"
        />

        <JobOfferResponseModal
            :offer="confirmationOffer"
            :action="confirmationAction"
            :loading="responding"
            @close="
                confirmationOffer = null;
                confirmationAction = null;
            "
            @confirm="confirmResponse"
        />
    </main>
</template>
