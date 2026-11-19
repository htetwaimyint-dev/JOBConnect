<script setup lang="ts">
const props = defineProps<{
    open: boolean;
    offerTitle?: string;
    submitting?: boolean;
}>();
const emit = defineEmits<{
    (event: "close"): void;
    (event: "confirm"): void;
}>();
</script>

<template>
    <Teleport to="body">
        <div
            v-if="props.open"
            class="fixed inset-0 z-60 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
            @click.self="emit('close')"
        >
            <section
                class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
            >
                <header
                    class="flex items-center justify-between border-b border-slate-100 px-6 py-4"
                >
                    <h2 class="text-base font-bold text-slate-900">
                        Decline job offer
                    </h2>
                    <button
                        type="button"
                        class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        ✕
                    </button>
                </header>
                <div class="space-y-2 p-6">
                    <p class="text-sm text-slate-600">
                        Are you sure you want to decline this offer?
                    </p>
                    <p
                        v-if="offerTitle"
                        class="rounded-xl bg-slate-50 p-3 text-xs font-semibold text-slate-800"
                    >
                        {{ offerTitle }}
                    </p>
                    <p class="text-xs text-slate-500">
                        This action will update the offer status to declined.
                    </p>
                </div>
                <footer
                    class="flex justify-end gap-2.5 border-t border-slate-100 bg-slate-50 px-6 py-4"
                >
                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                        :disabled="submitting"
                        @click="emit('close')"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="submitting"
                        @click="emit('confirm')"
                    >
                        {{ submitting ? "Declining…" : "Confirm decline" }}
                    </button>
                </footer>
            </section>
        </div>
    </Teleport>
</template>
