<script setup lang="ts">
import type { CompanyForm } from "../types/company-form.types";
import BaseInput from "@/components/common/BaseInput.vue";

defineProps<{ logoPreview: string | null }>();
const emit = defineEmits<{ selectLogo: [file: File] }>();
const form = defineModel<CompanyForm>("form", { required: true });

const employeeOptions = [
    "1-10",
    "11-50",
    "51-200",
    "201-500",
    "501-1000",
    "1000+",
];

function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) emit("selectLogo", file);
}
</script>

<template>
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Who We Are</h2>

        <div
            class="mb-6 flex items-center gap-4 border-b border-slate-100 pb-6"
        >
            <div
                class="relative h-16 w-16 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-inner flex items-center justify-center shrink-0"
            >
                <img
                    v-if="logoPreview"
                    :src="logoPreview"
                    alt="Company Logo"
                    class="h-full w-full object-cover"
                />
                <span v-else class="text-[10px] font-bold text-slate-400"
                    >LOGO</span
                >
            </div>
            <label
                class="cursor-pointer text-xs font-semibold text-[#0052FF] hover:underline"
            >
                <span>{{ logoPreview ? "Change Logo" : "Upload Logo" }}</span>
                <input
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    class="hidden"
                    @change="onLogoChange"
                />
            </label>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <BaseInput
                v-model="form.name"
                label="Name *"
                placeholder="Company name"
                size="sm"
                required
            />
            <BaseInput
                v-model="form.tagline"
                label="Tagline"
                placeholder="Short tagline"
                size="sm"
            />
            <BaseInput
                v-model="form.industry"
                label="Industry Type"
                placeholder="e.g. Technology"
                size="sm"
            />
            <BaseInput
                v-model="form.website"
                label="Website"
                placeholder="https://example.com"
                size="sm"
            />
            <BaseInput
                v-model="form.company_email"
                label="Company Email"
                type="email"
                placeholder="contact@example.com"
                size="sm"
            />
            <BaseInput
                v-model="form.phone"
                label="Phone Number"
                type="tel"
                placeholder="+1 (555) 000-0000"
                size="sm"
            />

            <!-- Moved from Description Form -->
            <BaseInput
                :v-model="form.foundedYear"
                label="Year Founded"
                type="number"
                placeholder="Year"
                size="sm"
            />
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700"
                    >Total Employees</label
                >
                <select
                    v-model="form.employeeCount"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                    <option value="">Select count</option>
                    <option
                        v-for="opt in employeeOptions"
                        :key="opt"
                        :value="opt"
                    >
                        {{ opt }} employees
                    </option>
                </select>
            </div>
            <BaseInput
                v-model="form.city"
                label="City"
                placeholder="City"
                size="sm"
            />
            <BaseInput
                v-model="form.address"
                label="Address"
                placeholder="Address"
                size="sm"
            />
        </div>
    </section>
</template>
