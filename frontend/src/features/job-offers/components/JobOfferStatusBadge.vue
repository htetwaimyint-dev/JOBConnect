<script setup lang="ts">
import type { JobOfferStatus } from "../types/jobOffer.types";
import {
    formatStatus,
    statusClass,
    statusDotClass,
} from "../utils/jobOfferFormatters";

defineProps<{
    status: JobOfferStatus;
}>();
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
        :class="statusClass(status)"
    >
        <span
            class="h-1.5 w-1.5 rounded-full"
            :class="statusDotClass(status)"
        />

        {{ formatStatus(status) }}
    </span>
</template>
