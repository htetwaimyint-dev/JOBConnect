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
    <section
        class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs"
    >
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">About Us</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Detailed company description, headcount scale, and location
                info.
            </p>
        </div>

        <div class="space-y-4">
            <div>
                <label
                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                >
                    Description
                </label>
                <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="Tell candidates about your mission, culture, and stack..."
                    class="w-full resize-y rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label
                        class="mb-1.5 block text-xs font-semibold text-slate-700"
                    >
                        Total Employees
                    </label>
                    <select
                        v-model="form.employeeCount"
                        class="h-9 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
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
                    v-model="form.phone"
                    label="Phone"
                    type="tel"
                    placeholder="+1 (555) 000-0000"
                    size="sm"
                />

                <BaseInput
                    v-model="form.city"
                    label="City"
                    placeholder="Enter city"
                    size="sm"
                />

                <BaseInput
                    v-model="form.address"
                    label="Address"
                    placeholder="Enter address"
                    size="sm"
                />
            </div>
        </div>
    </section>
</template>
