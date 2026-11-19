<script setup lang="ts">
import { computed } from "vue";
import type {
    CreateJobOfferPayload,
    EmployerOfferJob,
    JobOfferCandidate,
    JobOfferCompany,
} from "../types/jobOffer.types";

const props = defineProps<{
    open: boolean;
    candidate: JobOfferCandidate;
    company?: JobOfferCompany | null;
    job: EmployerOfferJob | null;
    offer: CreateJobOfferPayload;
}>();

const emit = defineEmits<{
    (event: "close"): void;
    (event: "confirm"): void;
}>();

const salaryText = computed(() => {
    if (!props.offer.salary_offered) return "Not set";
    return Number(props.offer.salary_offered).toLocaleString();
});

const expiresText = computed(() => {
    if (!props.offer.expires_at) return "Not set";
    const date = new Date(props.offer.expires_at);
    return Number.isNaN(date.getTime())
        ? props.offer.expires_at
        : new Intl.DateTimeFormat(undefined, {
              dateStyle: "medium",
              timeStyle: "short",
          }).format(date);
});
</script>

<template>
    <Teleport to="body">
        <Transition name="offer-modal">
            <div
                v-if="open"
                class="fixed inset-0 z-60 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
                @keydown.esc="emit('close')"
            >
                <section
                    class="my-auto w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                >
                    <header
                        class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-md bg-indigo-100 px-2 py-0.5 text-[11px] font-bold uppercase text-indigo-700"
                                >Preview</span
                            >
                            <h2 class="text-sm font-bold text-slate-800">
                                Candidate offer preview
                            </h2>
                        </div>
                        <button
                            type="button"
                            class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-200 hover:text-slate-700"
                            aria-label="Close preview"
                            @click="emit('close')"
                        >
                            ✕
                        </button>
                    </header>

                    <div class="p-6">
                        <div
                            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                        >
                            <div class="flex justify-between">
                                <div class="flex items-start gap-3.5">
                                    <img
                                        v-if="company?.logo"
                                        :src="company.logo"
                                        :alt="`${company.name} logo`"
                                        class="h-12 w-12 rounded-xl border border-slate-100 object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-base font-bold text-white"
                                    >
                                        {{
                                            (company?.name || "Company")
                                                .slice(0, 2)
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-xs font-semibold text-indigo-600"
                                        >
                                            {{
                                                company?.name || "Your company"
                                            }}
                                        </p>
                                        <h3
                                            class="mt-0.5 text-lg font-bold text-slate-900"
                                        >
                                            {{
                                                job?.title || "No job selected"
                                            }}
                                        </h3>
                                        <p
                                            class="mt-0.5 text-xs text-slate-500"
                                        >
                                            Offer for {{ candidate.name }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="items-start rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-xs text-slate-700"
                                >
                                    <p
                                        class="mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Offer expires
                                    </p>
                                    {{ expiresText }}
                                </div>
                            </div>

                            <div
                                class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-700"
                            >
                                <span
                                    class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 font-bold text-emerald-800"
                                >
                                    Offered salary: {{ salaryText }}
                                </span>
                                <span
                                    v-if="job"
                                    class="rounded-lg bg-slate-100 px-3 py-1.5"
                                    >{{
                                        job.employment_type.replaceAll("_", " ")
                                    }}</span
                                >
                                <span
                                    v-if="job"
                                    class="rounded-lg bg-slate-100 px-3 py-1.5"
                                    >{{ job.workplace_type }} ·
                                    {{
                                        [job.city, job.country]
                                            .filter(Boolean)
                                            .join(", ") ||
                                        "Location not specified"
                                    }}</span
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
                                    class="whitespace-pre-line text-xs leading-relaxed text-slate-700"
                                >
                                    {{ offer.offer_details }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <footer
                        class="flex flex-col-reverse justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row"
                    >
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            @click="emit('close')"
                        >
                            Back to editor
                        </button>
                        <button
                            type="button"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                            @click="emit('confirm')"
                        >
                            Looks good, send offer
                        </button>
                    </footer>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.offer-modal-enter-active,
.offer-modal-leave-active {
    transition: opacity 160ms ease;
}
.offer-modal-enter-from,
.offer-modal-leave-to {
    opacity: 0;
}
</style>
