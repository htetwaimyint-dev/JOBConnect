<script setup lang="ts">
import { computed, ref } from "vue";
import JobSeekerOffers from "../components/JobSeekerOffers.vue";
import { useJobOffers } from "../composables/useJobOffers.ts";

const { getMyOffers, respondToJobOffer } = useJobOffers();
const offersQuery = getMyOffers();
const respondingOfferId = ref<number | null>(null);
const offers = computed(() => offersQuery.data.value ?? []);
const loading = computed(() => offersQuery.isLoading.value);

const loadError = computed(() => {
    const error = offersQuery.error.value as {
        response?: { data?: { message?: string } };
        message?: string;
    } | null;
    return error?.response?.data?.message ?? error?.message ?? null;
});

const responseError = computed(() => {
    const error = respondToJobOffer.error.value as {
        response?: {
            data?: { message?: string; errors?: Record<string, string[]> };
        };
        message?: string;
    } | null;
    const validationMessage = error?.response?.data?.errors
        ? Object.values(error.response.data.errors).flat()[0]
        : null;
    return (
        validationMessage ??
        error?.response?.data?.message ??
        error?.message ??
        null
    );
});

async function respond(offerId: number, status: "accepted" | "rejected") {
    respondingOfferId.value = offerId;
    respondToJobOffer.reset();

    try {
        await respondToJobOffer.mutateAsync({ offerId, payload: { status } });
    } catch {
        // The error is displayed below the inbox; the query remains available for retry.
        await offersQuery.refetch();
    } finally {
        respondingOfferId.value = null;
    }
}
</script>

<template>
    <main class="min-h-screen bg-slate-50 text-slate-900">
        <JobSeekerOffers
            :offers="offers"
            :loading="loading"
            :error="loadError"
            :responding-offer-id="respondingOfferId"
            @accept="respond($event, 'accepted')"
            @reject="respond($event, 'rejected')"
            @retry="offersQuery.refetch()"
        />
        <div
            v-if="responseError"
            class="mx-auto mb-8 max-w-5xl px-4 sm:px-6 lg:px-8"
        >
            <p
                class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
            >
                {{ responseError }}
            </p>
        </div>
    </main>
</template>
