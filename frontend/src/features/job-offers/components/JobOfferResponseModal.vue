<script setup lang="ts">
import type { JobOffer } from "../types/jobOffer.types";

const props = defineProps<{
    offer: JobOffer | null;
    action: "withdraw" | "accept" | "reject" | null;
    loading?: boolean;
}>();

const emit = defineEmits<{
    close: [];
    confirm: [];
}>();

const content = {
    withdraw: {
        title: "Withdraw this offer?",
        description:
            "The candidate will no longer be able to accept this offer.",
        confirm: "Yes, withdraw offer",
        buttonClass: "bg-rose-600 hover:bg-rose-700",
    },
    accept: {
        title: "Accept this job offer?",
        description: "Your response will be submitted to the employer.",
        confirm: "Yes, accept offer",
        buttonClass: "bg-emerald-600 hover:bg-emerald-700",
    },
    reject: {
        title: "Decline this job offer?",
        description:
            "The employer will be notified that you declined this offer.",
        confirm: "Yes, decline offer",
        buttonClass: "bg-rose-600 hover:bg-rose-700",
    },
};
</script>

<template>
    <Transition name="fade">
        <div
            v-if="offer && action"
            class="fixed inset-0 z-60 flex items-center justify-center bg-slate-900/55 p-4"
            @click.self="emit('close')"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            >
                <div class="flex items-start gap-3.5">
                    <div
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-amber-100 bg-amber-50 text-lg text-amber-600"
                    >
                        !
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            {{ content[action].title }}
                        </h2>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            {{ content[action].description }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ offer.job?.title || "Job offer" }}
                            <span v-if="action === 'withdraw'">
                                — {{ offer.job_seeker?.name || "Candidate" }}
                            </span>
                            <span v-else>
                                — {{ offer.company?.name || "Company" }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button
                        type="button"
                        :disabled="loading"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                        @click="emit('close')"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="loading"
                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white disabled:opacity-60"
                        :class="content[action].buttonClass"
                        @click="emit('confirm')"
                    >
                        {{ loading ? "Processing…" : content[action].confirm }}
                    </button>
                </div>
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
