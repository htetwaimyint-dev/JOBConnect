<script setup lang="ts">
import type { JobFormData } from "../types/job.types";
import BaseInput from "@/components/common/BaseInput.vue";
import BaseSelect from "@/components/common/BaseSelect.vue";

defineProps<{
    errors: Partial<Record<keyof JobFormData, string[]>>;
    categoryOptions: Array<{
        label: string;
        value: number;
    }>;
}>();

const form = defineModel<JobFormData>({ required: true });

const employmentOptions = [
    { label: "Full Time", value: "full_time" },
    { label: "Part Time", value: "part_time" },
    { label: "Contract", value: "contract" },
    { label: "Internship", value: "internship" },
];
</script>

<template>
    <section
        class="rounded-md border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
    >
        <h2
            class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
        >
            Job Details
        </h2>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <BaseInput
                v-model="form.title"
                label="Job Title"
                placeholder="Enter job title"
                :error="errors.title?.[0]"
            />

            <BaseSelect
                v-model="form.category_id"
                label="Job Category"
                :options="categoryOptions"
                placeholder="Select category"
                :error="errors.category_id?.[0]"
            />

            <BaseInput
                v-model="form.deadline"
                type="date"
                label="Closing Date"
                :error="errors.deadline?.[0]"
            />

            <BaseSelect
                v-model="form.employment_type"
                label="Employment Type"
                :options="employmentOptions"
                placeholder="Select employment type"
                :error="errors.employment_type?.[0]"
            />
        </div>
        <BaseInput
            v-model.number="form.vacancies"
            type="number"
            label="Available Positions"
            min="1"
            :error="errors.vacancies?.[0]"
        />

        <div>
            <label class="mb-3 block text-sm font-medium">
                Workplace Type
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label
                    v-for="option in [
                        { label: 'On-site', value: 'onsite' },
                        { label: 'Remote', value: 'remote' },
                        { label: 'Hybrid', value: 'hybrid' },
                    ]"
                    :key="option.value"
                    :class="[
                        'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                        form.workplace_type === option.value
                            ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                            : 'border-gray-200 bg-white hover:border-gray-300',
                    ]"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-900">{{
                            option.label
                        }}</span>
                        <input
                            type="radio"
                            :value="option.value"
                            v-model="form.workplace_type"
                            class="text-purple-600 focus:ring-purple-500"
                        />
                    </div>
                    <p class="text-[11px] text-gray-500 leading-normal">
                        <span v-if="option.value === 'onsite'"
                            >Employees work from an office</span
                        >
                        <span v-else-if="option.value === 'remote'"
                            >Employees work from home</span
                        >
                        <span v-else-if="option.value === 'hybrid'">
                            Employees work from both office and home</span
                        >
                    </p>
                </label>
            </div>
        </div>
    </section>
</template>
