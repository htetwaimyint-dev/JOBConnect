<script setup lang="ts">
import { computed, reactive, watch } from "vue";
import type {
    CreateJobOfferPayload,
    EmployerOfferJob,
    JobOfferCandidate,
} from "../types/jobOffer.types";
import {
    formatEmploymentType,
    formatWorkplaceType,
} from "../utils/jobOfferFormatters";

const props = withDefaults(
    defineProps<{
        open: boolean;
        candidates: JobOfferCandidate[];
        availableJobs: EmployerOfferJob[];
        creating?: boolean;
    }>(),
    {
        creating: false,
    },
);

const emit = defineEmits<{
    "update:open": [value: boolean];
    create: [payload: CreateJobOfferPayload];
}>();

const form = reactive({
    job_seeker_id: "",
    job_id: "",
    salary_offered: "",
    expires_in_days: 7,
    offer_details: "",
});

const selectedJob = computed(
    () =>
        props.availableJobs.find((job) => String(job.id) === form.job_id) ??
        null,
);

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;

        form.job_seeker_id = props.candidates[0]
            ? String(props.candidates[0].id)
            : "";

        form.job_id = props.availableJobs[0]
            ? String(props.availableJobs[0].id)
            : "";

        form.salary_offered = "";
        form.expires_in_days = 7;
        form.offer_details = "";
    },
);

function close() {
    if (props.creating) return;

    emit("update:open", false);
}

function submit() {
    const candidateId = Number(form.job_seeker_id);
    const jobId = Number(form.job_id);
    const salary = Number(form.salary_offered);

    if (
        !Number.isInteger(candidateId) ||
        candidateId <= 0 ||
        !Number.isInteger(jobId) ||
        jobId <= 0 ||
        !form.salary_offered.trim() ||
        !Number.isFinite(salary) ||
        salary <= 0
    ) {
        return;
    }

    const payload: CreateJobOfferPayload = {
        job_seeker_id: candidateId,
        job_id: jobId,
        salary_offered: salary,
        offer_details: form.offer_details.trim() || null,
        expires_at: new Date(
            Date.now() + form.expires_in_days * 86400000,
        ).toISOString(),
    };

    emit("create", payload);
}
</script>

<template>
    <Transition name="fade">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/55 p-4 backdrop-blur-sm"
            @click.self="close"
        >
            <div
                class="my-8 w-full max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4"
                >
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Send New Direct Offer
                        </h2>

                        <p class="text-xs text-slate-500">
                            Invite a public job seeker directly to one of your
                            active jobs.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-200"
                        :disabled="creating"
                        @click="close"
                    >
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form class="space-y-4 p-6" @submit.prevent="submit">
                    <!-- Candidate -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-700"
                        >
                            Public candidate
                        </label>

                        <select
                            v-model="form.job_seeker_id"
                            required
                            :disabled="!candidates.length || creating"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="" disabled>
                                Select a candidate
                            </option>

                            <option
                                v-for="candidate in candidates"
                                :key="candidate.id"
                                :value="String(candidate.id)"
                            >
                                {{ candidate.name }}

                                <template
                                    v-if="candidate.profile?.professional_title"
                                >
                                    — {{ candidate.profile.professional_title }}
                                </template>
                            </option>
                        </select>

                        <p
                            v-if="!candidates.length"
                            class="mt-1 text-[11px] text-amber-600"
                        >
                            No public candidates were provided.
                        </p>
                    </div>

                    <!-- Job -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-700"
                        >
                            Target job requisition
                        </label>

                        <select
                            v-model="form.job_id"
                            required
                            :disabled="!availableJobs.length || creating"
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
                                {{ job.title }}

                                <template v-if="job.city">
                                    — {{ job.city }}
                                </template>
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
                            }}

                            <span v-if="selectedJob.workplace_type">
                                ·
                                {{
                                    formatWorkplaceType(
                                        selectedJob.workplace_type,
                                    )
                                }}
                            </span>

                            <span
                                v-if="selectedJob.city || selectedJob.country"
                            >
                                ·
                                {{
                                    [selectedJob.city, selectedJob.country]
                                        .filter(Boolean)
                                        .join(", ")
                                }}
                            </span>
                        </p>
                    </div>

                    <!-- Salary and expiration -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Salary offered
                            </label>

                            <input
                                v-model="form.salary_offered"
                                type="number"
                                min="0.01"
                                step="0.01"
                                required
                                placeholder="e.g. 2500"
                                :disabled="creating"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Offer valid for
                            </label>

                            <select
                                v-model.number="form.expires_in_days"
                                :disabled="creating"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option :value="5">5 days</option>
                                <option :value="7">7 days</option>
                                <option :value="14">14 days</option>
                            </select>
                        </div>
                    </div>

                    <!-- Offer note -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-700"
                        >
                            Personal pitch
                            <span class="font-normal text-slate-400">
                                (offer_details)
                            </span>
                        </label>

                        <textarea
                            v-model="form.offer_details"
                            rows="4"
                            maxlength="5000"
                            placeholder="Write a personalized note explaining why this candidate is a great fit…"
                            :disabled="creating"
                            class="w-full rounded-xl border border-slate-300 p-3.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex justify-end gap-2.5 border-t border-slate-100 pt-3"
                    >
                        <button
                            type="button"
                            :disabled="creating"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60"
                            @click="close"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="
                                creating ||
                                !candidates.length ||
                                !availableJobs.length
                            "
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ creating ? "Sending…" : "Send Direct Offer" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
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
