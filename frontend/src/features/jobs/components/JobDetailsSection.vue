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

const genderOptions = [
    { label: "Any Gender", value: "any" },
    { label: "Male", value: "male" },
    { label: "Female", value: "female" },
];

const workplaceOptions = [
    { label: "On-site", value: "onsite" },
    { label: "Remote", value: "remote" },
    { label: "Hybrid", value: "hybrid" },
];
</script>

<template>
    <section
        class="rounded-md border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
    >
        <h2
            class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2"
        >
            <!-- Briefcase Icon -->
            <svg
                class="w-5 h-5 text-fuchsia-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
            </svg>
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

        <!-- Available Positions and Gender Preference -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <BaseInput
                v-model.number="form.vacancies"
                type="number"
                label="Available Positions"
                min="1"
                :error="errors.vacancies?.[0]"
            />

            <BaseSelect
                v-model="form.gender"
                label="Gender Preference"
                :options="genderOptions"
                placeholder="Select gender preference"
                :error="errors.gender?.[0]"
            />
        </div>

        <!-- Workplace Type -->
        <div>
            <label class="mb-3 block text-sm font-medium text-gray-900">
                Workplace Type
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label
                    v-for="option in workplaceOptions"
                    :key="option.value"
                    :class="[
                        'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                        form.workplace_type === option.value
                            ? 'border-purple-300 bg-purple-50/20 ring-1 ring-purple-300'
                            : 'border-gray-200 bg-white hover:border-gray-300',
                    ]"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <!-- On-site Icon -->
                            <svg
                                v-if="option.value === 'onsite'"
                                class="w-4 h-4 text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>

                            <!-- Remote Icon -->
                            <svg
                                v-else-if="option.value === 'remote'"
                                class="w-4 h-4 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>

                            <!-- Hybrid Icon -->
                            <svg
                                v-else
                                class="w-4 h-4 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>

                            <span class="text-xs font-bold text-gray-900">
                                {{ option.label }}
                            </span>
                        </div>

                        <input
                            type="radio"
                            :value="option.value"
                            v-model="form.workplace_type"
                            class="text-purple-600 focus:ring-purple-500"
                        />
                    </div>

                    <p class="text-[11px] text-gray-500 leading-normal">
                        <span v-if="option.value === 'onsite'">
                            Employees work from an office
                        </span>
                        <span v-else-if="option.value === 'remote'">
                            Employees work from home
                        </span>
                        <span v-else>
                            Employees work from both office and home
                        </span>
                    </p>
                </label>
            </div>
        </div>
    </section>
</template>
