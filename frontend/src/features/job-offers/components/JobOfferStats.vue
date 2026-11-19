<script setup lang="ts">
import { computed } from "vue";
import type { JobOffer } from "../types/jobOffer.types";

const props = defineProps<{
    offers: JobOffer[];
    mode: "employer" | "jobseeker";
}>();

const stats = computed(() => {
    const total = props.offers.length;
    const pending = props.offers.filter((o) => o.status === "pending").length;
    const accepted = props.offers.filter((o) => o.status === "accepted").length;
    const rejected = props.offers.filter((o) => o.status === "rejected").length;

    if (props.mode === "employer") {
        const responded = accepted + rejected;

        return [
            {
                label: "Total sent",
                value: total,
                description: "All direct invitations",
                color: "slate",
            },
            {
                label: "Awaiting response",
                value: pending,
                description: "Pending offers",
                color: "amber",
            },
            {
                label: "Accepted offers",
                value: accepted,
                description: "Offers accepted",
                color: "emerald",
            },
            {
                label: "Acceptance rate",
                value: `${responded ? Math.round((accepted / responded) * 100) : 0}%`,
                description: "Of responded offers",
                color: "indigo",
            },
        ];
    }

    return [
        {
            label: "Offers received",
            value: total,
            description: "All offers received",
            color: "slate",
        },
        {
            label: "Awaiting response",
            value: pending,
            description: "Offers to review",
            color: "amber",
        },
        {
            label: "Accepted offers",
            value: accepted,
            description: "Offers you accepted",
            color: "emerald",
        },
        {
            label: "Declined offers",
            value: rejected,
            description: "Offers you declined",
            color: "rose",
        },
    ];
});
</script>

<template>
    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article
            v-for="stat in stats"
            :key="stat.label"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
            <p
                class="text-xs font-semibold uppercase tracking-wider text-slate-500"
            >
                {{ stat.label }}
            </p>
            <p class="mt-1.5 text-3xl font-bold text-slate-900">
                {{ stat.value }}
            </p>
            <p class="mt-1 text-xs text-slate-400">{{ stat.description }}</p>
        </article>
    </section>
</template>
