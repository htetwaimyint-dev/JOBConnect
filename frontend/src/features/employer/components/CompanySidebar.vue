<script setup lang="ts">
import type { Company } from "../types/company.types";
import CompanyJobs from "./CompanyJobs.vue";

defineProps<{
    company: Company;
    similarCompanies?: Array<{
        id: number | string;
        name: string;
        location: string;
        logo: string;
    }>;
}>();
</script>

<template>
    <div class="space-y-6">
        <!-- People Also View Section -->
        <div class="rounded-md border border-gray-100 bg-white p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-900">People Also View</h3>

            <div class="space-y-4">
                <div v-for="comp in similarCompanies || [
                    {
                        id: 1,
                        name: 'Traveloka',
                        location: 'Jakarta, Indonesia',
                        logo: '✈️',
                    },
                    {
                        id: 2,
                        name: 'Gojek',
                        location: 'Jakarta, Indonesia',
                        logo: '🟢',
                    },
                    {
                        id: 3,
                        name: 'Slack',
                        location: 'Yogyakarta, Indonesia',
                        logo: '🟣',
                    },
                    {
                        id: 4,
                        name: 'OVO',
                        location: 'Yogyakarta, Indonesia',
                        logo: '🟣',
                    },
                    {
                        id: 5,
                        name: 'Gopay',
                        location: 'Jakarta, Indonesia',
                        logo: '💳',
                    },
                ]" :key="comp.id" class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center text-lg shrink-0">
                            {{ comp.logo }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate">
                                {{ comp.name }}
                            </h4>
                            <p class="text-xs text-gray-500 truncate">
                                {{ comp.location }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="p-2 text-gray-400 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
