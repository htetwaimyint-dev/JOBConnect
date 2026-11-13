<script setup lang="ts">
import type { JobFormData } from "../types/job.types";
import BaseInput from "@/components/common/BaseInput.vue";
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
    errors: Partial<Record<keyof JobFormData, string[]>>;
}>();

const form = defineModel<JobFormData>({ required: true });

function selectSalaryType(type: "fixed" | "negotiable") {
    form.value.salary_type = type;

    if (type === "negotiable") {
        form.value.salary_min = undefined;
        form.value.salary_max = undefined;
    }
}
</script>

<template>
    <section
        class="rounded-md border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
    >
        <h2
            class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
        >
            Salary
        </h2>

        <div class="mb-5 flex gap-3">
            <BaseButton
                type="button"
                variant="ghost"
                color="neutral"
                :class="[
                    'rounded! py-2! px-3!',
                    form.salary_type === 'fixed'
                        ? 'rounded! border-purple-600! bg-purple-100!'
                        : 'border-gray-200!',
                ]"
                @click="selectSalaryType('fixed')"
            >
                Fixed Salary
            </BaseButton>

            <BaseButton
                type="button"
                variant="ghost"
                color="neutral"
                :class="[
                    'rounded! py-2! px-3!',
                    form.salary_type === 'negotiable'
                        ? 'rounded! border-purple-600! bg-purple-100!'
                        : 'border-gray-200!',
                ]"
                @click="selectSalaryType('negotiable')"
            >
                Negotiable
            </BaseButton>
        </div>

        <div
            v-if="form.salary_type === 'fixed'"
            class="grid grid-cols-1 gap-5 md:grid-cols-2"
        >
            <BaseInput
                v-model="form.salary_min"
                type="number"
                label="Minimum Salary"
                min="0"
                :error="errors.salary_min?.[0]"
            />

            <BaseInput
                v-model.number="form.salary_max"
                type="number"
                label="Maximum Salary"
                min="0"
                :error="errors.salary_max?.[0]"
            />
        </div>

        <p v-else class="text-sm text-gray-500">
            Salary will be negotiated with the candidate.
        </p>
    </section>
</template>
