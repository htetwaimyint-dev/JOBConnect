<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import type { Company } from "../types/company.types";
defineProps<{
    company: Company;
}>();

const emit = defineEmits<{
    viewJobs: [];
}>();
</script>

<template>
    <div class="space-y-5">
        <!-- Company Information -->
        <section class="rounded-none border border-gray-100 bg-white p-6">
            <img
                :src="company.logo ?? ''"
                :alt="`${company.name} logo`"
                class="h-20 w-20 rounded-xl border border-gray-100 object-cover"
            />

            <div class="flex items-center gap-3 pt-3">
                <div class="min-w-0">
                    <h5 class="truncate font-bold text-gray-900">
                        {{ company.name }}
                    </h5>

                    <p class="mt-1 text-xs text-gray-500">
                        {{ company.industry }}
                    </p>
                </div>
            </div>

            <div class="my-5 border-t border-gray-100"></div>

            <h3 class="text-sm font-bold text-gray-900">Company Information</h3>

            <div class="mt-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-xs text-gray-400">Industry</span>
                    <span class="text-right text-xs font-medium text-gray-700">
                        {{ company.industry }}
                    </span>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <span class="text-xs text-gray-400">Location</span>
                    <span class="text-right text-xs font-medium text-gray-700">
                        {{ company.address }}
                    </span>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <span class="text-xs text-gray-400">Founded</span>
                    <span class="text-right text-xs font-medium text-gray-700">
                        {{ company.foundedYear }}
                    </span>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <span class="text-xs text-gray-400">Company Size</span>
                    <span class="text-right text-xs font-medium text-gray-700">
                        {{ company.employeeCount }}
                    </span>
                </div>
            </div>

            <div class="my-5 border-t border-gray-100"></div>

            <h3 class="text-sm font-bold text-gray-900">Website</h3>

            <a
                :href="company.website ?? ''"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-2 block break-all text-sm font-medium text-violet-600 hover:text-violet-700"
            >
                {{ company.website }}
            </a>
        </section>

        <!-- Job Summary -->
        <section class="rounded-none border border-gray-100 bg-white p-6">
            <h3 class="text-sm font-bold text-gray-900">
                Career Opportunities
            </h3>

            <div class="mt-4 rounded-sm bg-violet-100 p-4">
                <p class="text-3xl font-bold text-violet-700">
                    {{ company.openJobsCount }}
                </p>
                <p class="mt-1 text-xs text-violet-600">
                    Currently open positions
                </p>
            </div>

            <BaseButton
                type="button"
                class="mt-4 w-full rounded-none! p-3!"
                @click="emit('viewJobs')"
            >
                Explore All Jobs
            </BaseButton>
        </section>
    </div>
</template>
