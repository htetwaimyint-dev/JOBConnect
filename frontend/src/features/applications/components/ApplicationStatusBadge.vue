<script setup lang="ts">
import type { ApplicationStatus } from "../types/application.types";

defineProps<{
    status: ApplicationStatus;
}>();

const statusConfig: Record<
    ApplicationStatus,
    { label: string; className: string }
> = {
    pending: {
        label: "Pending",
        className: "bg-yellow-100 text-yellow-800",
    },
    reviewed: {
        label: "Reviewed",
        className: "bg-blue-100 text-blue-800",
    },
    shortlisted: {
        label: "Shortlisted",
        className: "bg-purple-100 text-purple-800",
    },
    interview: {
        label: "Interview",
        className: "bg-indigo-100 text-indigo-800",
    },
    accepted: {
        label: "Accepted",
        className: "bg-green-100 text-green-800",
    },
    rejected: {
        label: "Rejected",
        className: "bg-red-100 text-red-800",
    },
    withdrawn: {
        label: "Withdrawn",
        className: "bg-gray-100 text-gray-600",
    },
};
</script>

<template>
    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
        :class="statusConfig[status].className">
        {{ statusConfig[status].label }}
    </span>
</template>
