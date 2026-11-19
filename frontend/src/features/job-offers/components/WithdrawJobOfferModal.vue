<script setup lang="ts">
import type { JobOffer } from "../types/jobOffer.types";

defineProps<{
    offer: JobOffer | null;
    withdrawing: boolean;
}>();

const emit = defineEmits<{
    close: [];
    confirm: [offer: JobOffer];
}>();
</script>

<template>
    <Transition name="fade">
        <div
            v-if="offer"
            class="fixed inset-0 z-60 flex items-center justify-center bg-slate-900/55 p-4"
            @click.self="emit('close')"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            >
                <div class="flex items-start gap-3.5">
                    <div
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-rose-100 bg-rose-50 text-lg text-rose-600"
                    >
                        !
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Withdraw Direct Job Offer?
                        </h2>

                        <p class="mt-1 text-sm leading-relaxed text-slate-600">
                            Are you sure you want to withdraw the offer for

                            <strong class="text-slate-900">
                                {{ offer.job?.title || "Job offer" }}
                            </strong>

                            sent to

                            <strong class="text-slate-900">
                                {{
                                    offer.job_seeker?.name || "Candidate"
                                }} </strong
                            >? The candidate will no longer be able to accept
                            it.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button
                        type="button"
                        :disabled="withdrawing"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60"
                        @click="emit('close')"
                    >
                        Keep Pending
                    </button>

                    <button
                        type="button"
                        :disabled="withdrawing"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 disabled:opacity-60"
                        @click="emit('confirm', offer)"
                    >
                        {{
                            withdrawing ? "Withdrawing…" : "Yes, Withdraw Offer"
                        }}
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
