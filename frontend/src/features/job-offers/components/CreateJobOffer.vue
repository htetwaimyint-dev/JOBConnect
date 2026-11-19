<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import CandidateProfileCard from "../components/CandidateProfileCard.vue";
import OfferPreviewModal from "../components/OfferPreviewModal.vue";
import type {
    CreateJobOfferPayload,
    EmployerOfferJob,
    JobOfferCandidate,
    JobOfferCompany,
} from "../types/jobOffer.types.ts";

const props = withDefaults(
    defineProps<{
        candidate: JobOfferCandidate;
        jobs: EmployerOfferJob[];
        company?: JobOfferCompany | null;
        loadingJobs?: boolean;
        submitting?: boolean;
        errors?: Record<string, string | string[]>;
    }>(),
    {
        company: null,
        loadingJobs: false,
        submitting: false,
        errors: () => ({}),
    },
);

const emit = defineEmits<{
    (event: "submit", payload: CreateJobOfferPayload): void;
}>();

const selectedJobId = ref<number | null>(props.jobs[0]?.id ?? null);
const previewOpen = ref(false);
const submitAttempted = ref(false);

function defaultExpiryDate(): string {
    const date = new Date();
    date.setDate(date.getDate() + 14);
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return date.toISOString().slice(0, 16);
}

const form = reactive({
    salary_offered: "",
    offer_details: "",
    expires_at: defaultExpiryDate(),
});

const selectedJob = computed(
    () => props.jobs.find((job) => job.id === selectedJobId.value) ?? null,
);

const validationErrors = computed(() => {
    const errors: Record<string, string> = {};
    const salary = Number(form.salary_offered);

    if (!selectedJobId.value)
        errors.job_id = "Select a published job for this offer.";
    if (!form.salary_offered || !Number.isFinite(salary) || salary <= 0) {
        errors.salary_offered = "Enter an offered salary greater than zero.";
    }
    if (!form.expires_at) errors.expires_at = "Choose when this offer expires.";
    else if (new Date(form.expires_at).getTime() <= Date.now()) {
        errors.expires_at = "The expiration must be in the future.";
    }

    return errors;
});

const canPreview = computed(
    () => Object.keys(validationErrors.value).length === 0,
);
const pitchLength = computed(() => form.offer_details.length);

watch(
    () => props.jobs,
    (jobs) => {
        if (!jobs.some((job) => job.id === selectedJobId.value)) {
            selectedJobId.value = jobs[0]?.id ?? null;
        }
    },
    { deep: true },
);

function buildPayload(): CreateJobOfferPayload {
    if (!selectedJobId.value)
        throw new Error("A published job must be selected.");

    return {
        job_seeker_id: props.candidate.id,
        job_id: selectedJobId.value,
        salary_offered: Number(form.salary_offered),
        offer_details: form.offer_details.trim() || null,
        expires_at: new Date(form.expires_at).toISOString(),
    };
}

function submitOffer() {
    submitAttempted.value = true;
    if (!canPreview.value || props.submitting) return;
    emit("submit", buildPayload());
}

function openPreview() {
    submitAttempted.value = true;
    if (!canPreview.value) return;
    previewOpen.value = true;
}

function confirmPreview() {
    previewOpen.value = false;
    submitOffer();
}

function fieldError(field: string): string | null {
    const serverError = props.errors[field];
    if (Array.isArray(serverError)) return serverError[0] ?? null;
    return (
        serverError ??
        (submitAttempted.value ? (validationErrors.value[field] ?? null) : null)
    );
}

function formatEnum(value: string): string {
    return value
        .replaceAll("_", " ")
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}
</script>

<template>
    <main class="mx-auto w-full max-w-7xl flex-1 px-4 sm:px-6 lg:px-8">
        <div
            class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <!-- <nav
                    class="mb-1 flex items-center gap-1.5 text-xs font-medium text-slate-500"
                >
                    <span>Talent Search</span><span>/</span
                    ><span>Public Profiles</span><span>/</span>
                    <span class="font-semibold text-indigo-600"
                        >Direct Job Offer</span
                    >
                </nav> -->
                <h1
                    class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl"
                >
                    Extend a Direct Job Offer
                </h1>
            </div>
            <div
                class="flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600 shadow-sm sm:self-auto"
            >
                <span class="h-2 w-2 rounded-full bg-emerald-500" />
                <span
                    >Review the candidate profile before sending an offer</span
                >
            </div>
        </div>

        <div
            class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8"
        >
            <CandidateProfileCard :candidate="candidate" />

            <section class="space-y-6 lg:col-span-8">
                <form
                    class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white shadow-sm"
                    @submit.prevent="submitOffer"
                >
                    <div class="space-y-5 p-5 sm:p-7">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-indigo-600"
                                    >Step 1 of 3</span
                                >
                                <h2
                                    class="text-base font-bold text-slate-900 sm:text-lg"
                                >
                                    Target Position Details
                                </h2>
                            </div>
                            <span class="text-xs text-slate-400"
                                >Published job required</span
                            >
                        </div>

                        <div>
                            <label
                                for="offer-job"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Select an active requisition</label
                            >
                            <select
                                id="offer-job"
                                v-model.number="selectedJobId"
                                :disabled="loadingJobs || jobs.length === 0"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-slate-50"
                            >
                                <option :value="null" disabled>
                                    Select a published job
                                </option>
                                <option
                                    v-for="job in jobs"
                                    :key="job.id"
                                    :value="job.id"
                                >
                                    {{ job.title }}
                                </option>
                            </select>
                            <p
                                v-if="loadingJobs"
                                class="mt-1.5 text-xs text-slate-500"
                            >
                                Loading your published jobs…
                            </p>
                            <p
                                v-else-if="jobs.length === 0"
                                class="mt-1.5 text-xs text-amber-700"
                            >
                                You don't have any published jobs available for
                                direct offers.
                            </p>
                            <!-- <p
                                v-if="fieldError('job_id')"
                                class="mt-1.5 text-xs text-rose-600"
                            >
                                {{ fieldError("job_id") }}
                            </p> -->
                        </div>

                        <div
                            v-if="selectedJob"
                            class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-4"
                        >
                            <p
                                class="mb-2 text-[11px] font-bold uppercase tracking-wider text-indigo-600"
                            >
                                Selected position
                            </p>
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ selectedJob.title }}
                            </h3>
                            <div
                                class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-slate-700"
                            >
                                <span
                                    class="rounded-lg bg-white px-2.5 py-1.5"
                                    >{{
                                        formatEnum(selectedJob.employment_type)
                                    }}</span
                                >
                                <span
                                    class="rounded-lg bg-white px-2.5 py-1.5"
                                    >{{
                                        formatEnum(selectedJob.workplace_type)
                                    }}</span
                                >
                                <span
                                    class="rounded-lg bg-white px-2.5 py-1.5"
                                    >{{
                                        [selectedJob.city, selectedJob.country]
                                            .filter(Boolean)
                                            .join(", ") ||
                                        "Location not specified"
                                    }}</span
                                >
                            </div>
                            <p
                                v-if="selectedJob.salary_type === 'negotiable'"
                                class="mt-2 text-xs text-slate-500"
                            >
                                The job's advertised salary is negotiable.
                            </p>
                            <p
                                v-else-if="
                                    selectedJob.salary_min ||
                                    selectedJob.salary_max
                                "
                                class="mt-2 text-xs text-slate-500"
                            >
                                Advertised range:
                                {{ selectedJob.salary_min ?? "—" }} –
                                {{ selectedJob.salary_max ?? "—" }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-7">
                        <div>
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-indigo-600"
                                >Step 2 of 3</span
                            >
                            <h2
                                class="text-base font-bold text-slate-900 sm:text-lg"
                            >
                                Compensation
                            </h2>
                        </div>

                        <div>
                            <label
                                for="salary-offered"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Offered salary</label
                            >
                            <input
                                id="salary-offered"
                                v-model="form.salary_offered"
                                type="number"
                                step="0.01"
                                placeholder="e.g. 3500"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            <p class="mt-1.5 text-xs text-slate-500">
                                Enter the amount using the currency agreed by
                                your company.
                            </p>
                            <p
                                v-if="fieldError('salary_offered')"
                                class="mt-1.5 text-xs text-rose-600"
                            >
                                {{ fieldError("salary_offered") }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-7">
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-indigo-600"
                                    >Step 3 of 3</span
                                >
                                <h2
                                    class="text-base font-bold text-slate-900 sm:text-lg"
                                >
                                    Offer Expiration & Personal Note
                                </h2>
                            </div>
                        </div>

                        <div>
                            <label
                                for="offer-expiration"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >Offer expires at</label
                            >
                            <input
                                id="offer-expiration"
                                v-model="form.expires_at"
                                type="datetime-local"
                                required
                                :min="
                                    new Date(
                                        Date.now() -
                                            new Date().getTimezoneOffset() *
                                                60000,
                                    )
                                        .toISOString()
                                        .slice(0, 16)
                                "
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            <p
                                v-if="fieldError('expires_at')"
                                class="mt-1.5 text-xs text-rose-600"
                            >
                                {{ fieldError("expires_at") }}
                            </p>
                        </div>

                        <div>
                            <div
                                class="mb-1.5 flex items-center justify-between gap-2"
                            >
                                <label
                                    for="offer-details"
                                    class="block text-xs font-semibold text-slate-700"
                                    >Personal note from the hiring team</label
                                >
                                <span class="text-[11px] text-slate-400"
                                    >{{ pitchLength }} characters</span
                                >
                            </div>
                            <textarea
                                id="offer-details"
                                v-model="form.offer_details"
                                rows="5"
                                maxlength="5000"
                                placeholder="Explain why the candidate's experience stood out and share any important details about the opportunity…"
                                class="w-full rounded-xl border border-slate-300 p-3.5 text-sm leading-relaxed text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            <p
                                v-if="fieldError('offer_details')"
                                class="mt-1.5 text-xs text-rose-600"
                            >
                                {{ fieldError("offer_details") }}
                            </p>
                        </div>
                    </div>

                    <footer
                        class="flex flex-col-reverse justify-between gap-3 rounded-b-2xl bg-slate-50/80 p-5 sm:flex-row sm:items-center sm:px-7"
                    >
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!canPreview"
                            @click="openPreview"
                        >
                            Preview candidate view
                        </button>
                        <button
                            type="submit"
                            class="flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="submitting || jobs.length === 0"
                        >
                            <span>{{
                                submitting
                                    ? "Sending offer…"
                                    : "Send Direct Offer"
                            }}</span>
                            <svg
                                v-if="!submitting"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 19 21 21 12 3 3 21l9-2Zm0 0v-8"
                                />
                            </svg>
                        </button>
                    </footer>
                </form>
            </section>
        </div>

        <OfferPreviewModal
            :open="previewOpen"
            :candidate="candidate"
            :company="company"
            :job="selectedJob"
            :offer="
                selectedJob
                    ? {
                          job_seeker_id: candidate.id,
                          job_id: selectedJob.id,
                          salary_offered: Number(form.salary_offered),
                          offer_details: form.offer_details.trim() || null,
                          expires_at: form.expires_at,
                      }
                    : {
                          job_seeker_id: candidate.id,
                          job_id: 0,
                          salary_offered: Number(form.salary_offered),
                          offer_details: form.offer_details.trim() || null,
                          expires_at: form.expires_at,
                      }
            "
            @close="previewOpen = false"
            @confirm="confirmPreview"
        />
    </main>
</template>
