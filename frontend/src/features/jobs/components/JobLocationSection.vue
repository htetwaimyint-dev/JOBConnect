<script setup lang="ts">
import type { JobFormData } from "../types/job.types";
import BaseInput from "@/components/common/BaseInput.vue";

defineProps<{
    errors: Partial<Record<keyof JobFormData, string[]>>;
}>();

const form = defineModel<JobFormData>({ required: true });
</script>

<template>
    <section class="rounded-md border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5">
        <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
            <!-- Map Pin Icon -->
            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Location
        </h2>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <BaseInput v-model="form.country" label="Country" placeholder="Enter country"
                :error="errors.country?.[0]" />

            <BaseInput v-model="form.city" label="City" placeholder="Enter city" :error="errors.city?.[0]" />
        </div>
    </section>
</template>
