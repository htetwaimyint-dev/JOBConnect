<script setup lang="ts">
import type { CompanyForm } from "../types/company-form.types";
import BaseInput from "@/components/common/BaseInput.vue";

const form = defineModel<CompanyForm>("form", { required: true });

const employeeOptions = [
    "1-10",
    "11-50",
    "51-200",
    "201-500",
    "501-1000",
    "1000+",
];
</script>

<template>
    <section class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="mb-3">
            <h2 class="text-sm font-semibold text-gray-900">
                Company overview
            </h2>
            <p class="mt-0.5 text-xs text-gray-500">
                Tell candidates about your company.
            </p>
        </div>

        <div class="space-y-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Describe your company, culture, and mission..."
                    class="w-full resize-y rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <BaseInput
                    :v-model="form.foundedYear"
                    label="Founded year"
                    type="number"
                    placeholder="e.g. 2015"
                    size="sm"
                />

                <div>
                    <label
                        class="mb-1.5 block text-sm font-medium text-gray-700"
                    >
                        Employee count
                    </label>
                    <select
                        v-model="form.employeeCount"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">Select company size</option>
                        <option
                            v-for="option in employeeOptions"
                            :key="option"
                            :value="option"
                        >
                            {{ option }} employees
                        </option>
                    </select>
                </div>

                <BaseInput
                    v-model="form.city"
                    label="City"
                    placeholder="Enter city"
                    size="sm"
                />
                <BaseInput
                    v-model="form.address"
                    label="Address"
                    placeholder="Enter company address"
                    size="sm"
                />
            </div>
        </div>
    </section>
</template>
