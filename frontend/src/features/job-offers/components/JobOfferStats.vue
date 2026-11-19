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

    // Text color mapping to ensure Tailwind safely detects the full classes
    const textColors = {
        slate: "text-slate-600 bg-slate-50 border-slate-100",
        amber: "text-amber-600 bg-amber-50 border-amber-100",
        emerald: "text-emerald-600 bg-emerald-50 border-emerald-100",
        indigo: "text-indigo-600 bg-indigo-50 border-indigo-100",
        rose: "text-rose-600 bg-rose-50 border-rose-100",
    };

    if (props.mode === "employer") {
        const responded = accepted + rejected;
        return [
            {
                label: "Total sent",
                value: total,
                description: "All direct invitations",
                colorClass: textColors.slate,
                icon: "↗",
            },
            {
                label: "Awaiting response",
                value: pending,
                description: "Pending offers",
                colorClass: textColors.amber,
                icon: "◷",
            },
            {
                label: "Accepted offers",
                value: accepted,
                description: "Offers accepted",
                colorClass: textColors.emerald,
                icon: "✓",
            },
            {
                label: "Acceptance rate",
                value: `${responded ? Math.round((accepted / responded) * 100) : 0}%`,
                description: "Of responded offers",
                colorClass: textColors.indigo,
                icon: "%",
            },
        ];
    }

    return [
        {
            label: "Offers received",
            value: total,
            description: "All offers received",
            colorClass: textColors.slate,
            icon: "↗",
        },
        {
            label: "Awaiting response",
            value: pending,
            description: "Offers to review",
            colorClass: textColors.amber,
            icon: "◷",
        },
        {
            label: "Accepted offers",
            value: accepted,
            description: "Offers you accepted",
            colorClass: textColors.emerald,
            icon: "✓",
        },
        {
            label: "Declined offers",
            value: rejected,
            description: "Offers you declined",
            colorClass: textColors.rose,
            icon: "✕",
        },
    ];
});
</script>

<template>
    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article
            v-for="stat in stats"
            :key="stat.label"
            class="flex items-start justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
            <div>
                <p
                    :class="stat.colorClass"
                    class="text-xs font-semibold uppercase tracking-wider bg-transparent"
                >
                    {{ stat.label }}
                </p>
                <p class="mt-1.5 text-3xl font-bold text-slate-900">
                    {{ stat.value }}
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    {{ stat.description }}
                </p>
            </div>

            <span
                class="grid h-10 w-10 place-items-center rounded-xl text-lg font-bold border"
                :class="stat.colorClass"
            >
                {{ stat.icon }}
            </span>
        </article>
    </section>
</template>
