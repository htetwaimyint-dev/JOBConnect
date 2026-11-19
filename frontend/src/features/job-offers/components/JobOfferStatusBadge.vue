<script setup lang="ts">
import { computed } from "vue";
import type { JobOfferStatus } from "../types/jobOffer.types";

const props = defineProps<{ status: JobOfferStatus }>();

const config = computed(() => {
    const values: Record<JobOfferStatus, { label: string; classes: string }> = {
        pending: {
            label: "Action Required",
            classes: "border-amber-200 bg-amber-50 text-amber-700",
        },
        accepted: {
            label: "Offer Accepted",
            classes: "border-emerald-200 bg-emerald-50 text-emerald-700",
        },
        rejected: {
            label: "Declined",
            classes: "border-slate-200 bg-slate-100 text-slate-600",
        },
        expired: {
            label: "Expired",
            classes: "border-rose-200 bg-rose-50 text-rose-700",
        },
        withdrawn: {
            label: "Withdrawn",
            classes: "border-slate-200 bg-slate-100 text-slate-600",
        },
    };

    return values[props.status];
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap"
        :class="config.classes"
    >
        <span
            v-if="status === 'pending'"
            class="mr-1.5 h-1.5 w-1.5 rounded-full bg-amber-500"
        />
        <span v-else-if="status === 'accepted'" class="mr-1.5">✓</span>
        {{ config.label }}
    </span>
</template>
