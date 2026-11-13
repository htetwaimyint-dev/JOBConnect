<script setup lang="ts">
import type { JobFormData } from "../types/job.types";
import BaseInput from "@/components/common/BaseInput.vue";

defineProps<{
    errors: Partial<Record<keyof JobFormData, string[]>>;
}>();

const form = defineModel<JobFormData>({ required: true });
</script>

<template>
    <section
        class="rounded-md border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
    >
        <h2
            class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
        >
            Location
        </h2>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <BaseInput
                v-model="form.country"
                label="Country"
                placeholder="Enter country"
                :error="errors.country?.[0]"
            />

            <BaseInput
                v-model="form.city"
                label="City"
                placeholder="Enter city"
                :error="errors.city?.[0]"
            />
        </div>
    </section>
</template>
