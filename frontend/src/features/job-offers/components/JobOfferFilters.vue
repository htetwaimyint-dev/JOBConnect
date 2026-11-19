<script setup lang="ts">
import { computed } from "vue";
import type { JobOffer, JobOfferFilter } from "../types/jobOffer.types";

const props = withDefaults(
    defineProps<{
        offers: JobOffer[];
        mode: "employer" | "jobseeker";
        statusFilter: JobOfferFilter;
        searchQuery: string;
        selectedJobFilter?: string;
    }>(),
    {
        selectedJobFilter: "all",
    },
);

const emit = defineEmits<{
    "update:statusFilter": [value: JobOfferFilter];
    "update:searchQuery": [value: string];
    "update:selectedJobFilter": [value: string];
}>();

const statuses: JobOfferFilter[] = [
    "all",
    "pending",
    "accepted",
    "rejected",
    "expired",
    "withdrawn",
];

const tabs = computed(() =>
    statuses
        .filter((status) => props.mode === "employer" || status !== "withdrawn")
        .map((status) => ({
            value: status,
            label:
                status === "all"
                    ? "All"
                    : status === "rejected"
                      ? "Declined"
                      : status.charAt(0).toUpperCase() + status.slice(1),
            count:
                status === "all"
                    ? props.offers.length
                    : props.offers.filter((offer) => offer.status === status)
                          .length,
        })),
);

const jobs = computed(() => {
    const unique = new Map<number, string>();
    props.offers.forEach((offer) => {
        if (offer.job) unique.set(offer.job_id, offer.job.title);
    });
    return [...unique.entries()].map(([id, title]) => ({ id, title }));
});
</script>

<template>
    <section
        class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
    >
        <div
            class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between"
        >
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.value"
                    type="button"
                    class="rounded-lg px-3 py-2 text-xs font-semibold transition"
                    :class="
                        statusFilter === tab.value
                            ? 'bg-indigo-600 text-white'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                    "
                    @click="emit('update:statusFilter', tab.value)"
                >
                    {{ tab.label }}
                    <span class="ml-1 opacity-75">({{ tab.count }})</span>
                </button>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <select
                    v-if="mode === 'employer'"
                    :value="selectedJobFilter"
                    class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm"
                    @change="
                        emit(
                            'update:selectedJobFilter',
                            ($event.target as HTMLSelectElement).value,
                        )
                    "
                >
                    <option value="all">All jobs</option>
                    <option
                        v-for="job in jobs"
                        :key="job.id"
                        :value="String(job.id)"
                    >
                        {{ job.title }}
                    </option>
                </select>

                <input
                    :value="searchQuery"
                    type="search"
                    :placeholder="
                        mode === 'employer'
                            ? 'Search candidate or job…'
                            : 'Search company or job…'
                    "
                    class="min-w-56 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-indigo-500"
                    @input="
                        emit(
                            'update:searchQuery',
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                />
            </div>
        </div>
    </section>
</template>
