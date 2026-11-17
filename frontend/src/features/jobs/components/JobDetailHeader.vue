<script setup lang="ts">
import { computed } from "vue";
import type { Job, JobFormData } from "../types/job.types";
import { formatJobLabel } from "@/utils/jobs.utils";

const props = defineProps<{
    job: Job | JobFormData;
}>();

const company = computed(() => {
    if ("company" in props.job) {
        return props.job.company;
    }

    return null;
});
</script>

<template>
    <header class="space-y-5 border-b border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ job.title || "Job Title" }}
        </h1>

        <div class="flex items-center gap-4">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-lg font-bold text-white"
            >
                {{
                    ("company" in job && job.company?.name?.[0]
                        ? job.company.name[0]
                        : "C"
                    ).toUpperCase()
                }}
            </div>

            <div>
                <p class="text-sm font-bold text-gray-900">
                    {{ company?.name || "Company Name" }}
                </p>

                <p class="text-sm text-gray-500">
                    {{
                        [job.city, job.country].filter(Boolean).join(", ") ||
                        "Location not specified"
                    }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <span
                class="rounded-md bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
            >
                {{ formatJobLabel(job.employment_type) }}
            </span>

            <span
                class="rounded-md bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
            >
                {{ formatJobLabel(job.workplace_type) }}
            </span>

            <span
                class="rounded-md bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600"
            >
                {{ job.vacancies }} Position(s)
            </span>
        </div>
    </header>
</template>
