<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import { toast } from "vue3-toastify";

import JobOfferStats from "../components/JobOfferStats.vue";
import JobOfferFilters from "../components/JobOfferFilters.vue";
import JobOfferTable from "../components/JobOfferTable.vue";
import JobOfferDetailsDrawer from "../components/JobOfferDetailsDrawer.vue";
import JobOfferResponseModal from "../components/JobOfferResponseModal.vue";

import { useJobOffers } from "../composables/useJobOffers";
import type {
    CreateJobOfferPayload,
    JobOffer,
    JobOfferFilter,
} from "../types/jobOffer.types";

const route = useRoute();

const candidateId = computed(() => {
    const value = route.params.candidateId ?? route.query.candidateId ?? "";
    return Array.isArray(value) ? String(value[0] ?? "") : String(value);
});

const {
    getEmployerOffers,
    getEmployerContext,
    createJobOffer,
    withdrawJobOffer,
} = useJobOffers();

const offersQuery = getEmployerOffers();
const contextQuery = getEmployerContext(candidateId);

const offers = computed<JobOffer[]>(() => offersQuery.data.value ?? []);
const availableJobs = computed(() => contextQuery.data.value?.jobs ?? []);
const candidate = computed(() => contextQuery.data.value?.candidate ?? null);

const loading = computed(
    () =>
        offersQuery.isLoading.value ||
        (Number(candidateId.value) > 0 && contextQuery.isLoading.value),
);

const creating = computed(() => createJobOffer.isPending.value);
const withdrawing = computed(() => withdrawJobOffer.isPending.value);

const statusFilter = ref<JobOfferFilter>("all");
const searchQuery = ref("");
const selectedJobFilter = ref("all");
const selectedOffer = ref<JobOffer | null>(null);
const confirmationOffer = ref<JobOffer | null>(null);
const confirmationAction = ref<"withdraw" | null>(null);

const formVisible = ref(false);
const form = ref({
    job_id: "",
    salary_offered: "",
    expires_in_days: 7,
    offer_details: "",
});

const filteredOffers = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase();

    return offers.value.filter((offer) => {
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
        availableJobs.value.find(
            (job) => String(job.id) === form.value.job_id,
        ) ?? null,
);

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

function openCreateForm() {
    form.value = {
        job_id: availableJobs.value[0] ? String(availableJobs.value[0].id) : "",
        salary_offered: "",
        expires_in_days: 7,
        offer_details: "",
    };
    formVisible.value = true;
}

async function submitCreate() {
    if (!candidate.value) {
        toast.error(
            "Open this page from a candidate profile to create an offer.",
        );
        return;
    }

    const jobId = Number(form.value.job_id);
    const salary = Number(form.value.salary_offered);

    if (
        !Number.isInteger(jobId) ||
        jobId <= 0 ||
        !Number.isFinite(salary) ||
        salary <= 0 ||
        !form.value.salary_offered.trim() ||
        !form.value.offer_details.trim()
    ) {
        toast.error("Please complete all required offer fields.");
        return;
    }

    const payload: CreateJobOfferPayload = {
        job_seeker_id: candidate.value.id,
        job_id: jobId,
        salary_offered: salary,
        offer_details: form.value.offer_details.trim(),
        expires_at: new Date(
            Date.now() + form.value.expires_in_days * 86400000,
        ).toISOString(),
    };

    try {
        await createJobOffer.mutateAsync(payload);
        formVisible.value = false;
        toast.success("Direct job offer sent successfully.");
    } catch (error) {
        toast.error(apiErrorMessage(error));
    }
}

function askWithdraw(offer: JobOffer) {
    selectedOffer.value = null;
    confirmationOffer.value = offer;
    confirmationAction.value = "withdraw";
}

async function confirmWithdraw() {
    if (!confirmationOffer.value) return;

    try {
        await withdrawJobOffer.mutateAsync(confirmationOffer.value.id);
        confirmationOffer.value = null;
        confirmationAction.value = null;
        toast.success("Job offer withdrawn successfully.");
    } catch (error) {
        toast.error(apiErrorMessage(error));
    }
}

async function refreshOffers() {
    await Promise.all([
        offersQuery.refetch(),
        Number(candidateId.value) > 0
            ? contextQuery.refetch()
            : Promise.resolve(),
    ]);
}
</script>

<template>
    <main class="min-h-full bg-slate-50 text-slate-800">
        <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <header
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <nav
                        class="mb-1 flex items-center gap-1.5 text-xs font-medium text-slate-500"
                    >
                        <span>Employer Dashboard</span>
                        <span>/</span>
                        <span>Recruitment Pipeline</span>
                        <span>/</span>
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
                        Track invitations sent to job seekers and manage their
                        responses.
                    </p>
                </div>

                <button
                    type="button"
                    :disabled="!candidate || !availableJobs.length"
                    class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="openCreateForm"
                >
                    + New Direct Offer
                </button>
            </header>

            <div
                v-if="!Number(candidateId)"
                class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
            >
                You can review your sent offers here. To create a new offer,
                open this page from a candidate's profile.
            </div>

            <div
                v-if="offersQuery.isError.value"
                class="mt-5 flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
            >
                <span>{{ apiErrorMessage(offersQuery.error.value) }}</span>
                <button
                    class="font-semibold underline"
                    @click="offersQuery.refetch()"
                >
                    Retry
                </button>
            </div>

            <div
                v-if="Number(candidateId) > 0 && contextQuery.isError.value"
                class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
            >
                {{ apiErrorMessage(contextQuery.error.value) }}
                <button
                    class="ml-2 font-semibold underline"
                    @click="contextQuery.refetch()"
                >
                    Retry candidate lookup
                </button>
            </div>

            <JobOfferStats :offers="offers" mode="employer" />

            <JobOfferFilters
                v-model:status-filter="statusFilter"
                v-model:search-query="searchQuery"
                v-model:selected-job-filter="selectedJobFilter"
                :offers="offers"
                mode="employer"
            />

            <JobOfferTable
                :offers="filteredOffers"
                mode="employer"
                :loading="loading"
                @view="selectedOffer = $event"
                @withdraw="askWithdraw"
                @refresh="refreshOffers"
            />
        </div>

        <JobOfferDetailsDrawer
            :offer="selectedOffer"
            mode="employer"
            :withdrawing="withdrawing"
            @close="selectedOffer = null"
            @withdraw="askWithdraw"
        />

        <JobOfferResponseModal
            :offer="confirmationOffer"
            :action="confirmationAction"
            :loading="withdrawing"
            @close="
                confirmationOffer = null;
                confirmationAction = null;
            "
            @confirm="confirmWithdraw"
        />

        <!-- Employer-only create offer form -->
        <Transition name="fade">
            <div
                v-if="formVisible"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/55 p-4"
                @click.self="formVisible = false"
            >
                <div
                    class="my-8 w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-6 py-4"
                    >
                        <div>
                            <h2 class="font-bold text-slate-900">
                                Send New Direct Offer
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Create an offer for {{ candidate?.name }}.
                            </p>
                        </div>
                        <button
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-100"
                            @click="formVisible = false"
                        >
                            ✕
                        </button>
                    </div>

                    <form class="space-y-4 p-6" @submit.prevent="submitCreate">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Candidate</label
                            >
                            <input
                                :value="candidate?.name || ''"
                                disabled
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Target job</label
                            >
                            <select
                                v-model="form.job_id"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm"
                            >
                                <option value="" disabled>
                                    Select a published job
                                </option>
                                <option
                                    v-for="job in availableJobs"
                                    :key="job.id"
                                    :value="String(job.id)"
                                >
                                    {{ job.title }}
                                </option>
                            </select>
                            <p
                                v-if="selectedJob"
                                class="mt-1 text-xs text-slate-500"
                            >
                                {{ selectedJob.employment_type }} ·
                                {{ selectedJob.workplace_type }}
                                <span v-if="selectedJob.city">
                                    · {{ selectedJob.city }}</span
                                >
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                    >Salary offered</label
                                >
                                <input
                                    v-model="form.salary_offered"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="e.g. 2500"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                    >Offer valid for</label
                                >
                                <select
                                    v-model.number="form.expires_in_days"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
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
                                >Personal offer note</label
                            >
                            <textarea
                                v-model="form.offer_details"
                                required
                                rows="4"
                                class="w-full rounded-xl border border-slate-300 p-3.5 text-sm"
                                placeholder="Write a personalized note for the candidate…"
                            />
                        </div>

                        <div
                            class="flex justify-end gap-2 border-t border-slate-100 pt-3"
                        >
                            <button
                                type="button"
                                class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold"
                                @click="formVisible = false"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="creating"
                                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white disabled:opacity-60"
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
    </main>
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
</style>
