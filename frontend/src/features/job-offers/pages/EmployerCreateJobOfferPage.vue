<script setup lang="ts">
import { computed, ref } from "vue";
import { useJobOffers } from "../composables/useJobOffers.ts";
import CreateJobOffer from "../components/CreateJobOffer.vue";
import type { CreateJobOfferPayload } from "../types/jobOffer.types.ts";
import { toast } from "vue3-toastify";

const props = defineProps<{ candidateId: number }>();
const emit = defineEmits<{ (event: "sent"): void }>();

const { getEmployerContext, createJobOffer } = useJobOffers();
const contextQuery = getEmployerContext(computed(() => props.candidateId));
const successNotice = ref("");

const candidate = computed(() => contextQuery.data.value?.candidate ?? null);
const company = computed(() => contextQuery.data.value?.company ?? null);
const jobs = computed(() => contextQuery.data.value?.jobs ?? []);
const loading = computed(() => contextQuery.isLoading.value);
const loadingError = computed(() => contextQuery.isError.value);
const submitting = computed(() => createJobOffer.isPending.value);

const serverErrors = computed<Record<string, string | string[]>>(() => {
    const error = createJobOffer.error.value as {
        response?: { data?: { errors?: Record<string, string | string[]> } };
    } | null;
    return error?.response?.data?.errors ?? {};
});

const errorMessage = computed(() => {
    const error = contextQuery.error.value as {
        response?: { data?: { message?: string } };
        message?: string;
    } | null;
    return (
        error?.response?.data?.message ??
        error?.message ??
        "Unable to load the candidate and published jobs."
    );
});

const submitError = computed(() => {
    const error = createJobOffer.error.value as {
        response?: { data?: { message?: string } };
        message?: string;
    } | null;
    return error?.response?.data?.message ?? error?.message ?? null;
});

async function submitOffer(payload: CreateJobOfferPayload) {
    successNotice.value = "";
    createJobOffer.reset();

    const toastId = toast.loading("Sending job offer...");

    try {
        await createJobOffer.mutateAsync({
            ...payload,
            job_seeker_id: props.candidateId,
        });
        successNotice.value =
            "The direct job offer has been sent successfully.";

        toast.update(toastId, {
            render: successNotice.value,
            type: "success",
            isLoading: false,
            autoClose: 3000,
        });

        emit("sent");
    } catch (error: any) {
        const message =
            error.response?.data?.message ?? "Unable to send offer.";

        toast.update(toastId, {
            render: message,
            type: "error",
            isLoading: false,
            autoClose: 4000,
        });
    }
}
</script>

<template>
    <main class="min-h-screen bg-slate-50 text-slate-900">
        <div
            v-if="loading"
            class="mx-auto max-w-7xl space-y-4 px-4 py-8 sm:px-6 lg:px-8"
        >
            <div class="h-8 w-64 animate-pulse rounded-lg bg-slate-200" />
            <div
                class="h-96 animate-pulse rounded-2xl border border-slate-200 bg-white"
            />
        </div>

        <div
            v-else-if="loadingError || !candidate"
            class="mx-auto max-w-3xl px-4 py-16 text-center"
        >
            <div class="rounded-2xl border border-rose-200 bg-white p-8">
                <h1 class="text-lg font-bold text-slate-900">
                    Unable to open direct offer
                </h1>
                <p class="mt-2 text-sm text-slate-600">{{ errorMessage }}</p>
                <button
                    type="button"
                    class="mt-5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"
                    @click="contextQuery.refetch()"
                >
                    Try again
                </button>
            </div>
        </div>

        <template v-else>
            <!-- <div
                v-if="successNotice"
                class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8"
            >
                <p
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                >
                    {{ successNotice }}
                </p>
            </div> -->
            <CreateJobOffer
                :candidate="candidate"
                :jobs="jobs"
                :company="company"
                :loading-jobs="loading"
                :submitting="submitting"
                :errors="serverErrors"
                @submit="submitOffer"
            />
        </template>
    </main>
</template>
