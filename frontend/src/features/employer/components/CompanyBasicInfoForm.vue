<script setup lang="ts">
import type { CompanyForm } from "../types/company-form.types";
import BaseInput from "@/components/common/BaseInput.vue";
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
    logoPreview: string | null;
}>();

const emit = defineEmits<{
    selectLogo: [file: File];
}>();

const form = defineModel<CompanyForm>("form", { required: true });

function onLogoChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) emit("selectLogo", file);
}
</script>

<template>
    <section class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="mb-3">
            <h2 class="text-sm font-semibold text-gray-900">
                Basic information
            </h2>
            <p class="mt-0.5 text-xs text-gray-500">
                Your company's name and contact details.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <div class="shrink-0">
                <div
                    class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50"
                >
                    <img
                        v-if="logoPreview"
                        :src="logoPreview"
                        alt="Company logo"
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-xs text-gray-400">Logo</span>
                </div>

                <label
                    class="mt-1.5 block cursor-pointer text-center text-xs font-medium text-blue-600 hover:text-blue-700"
                >
                    Upload
                    <input
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        class="hidden"
                        @change="onLogoChange"
                    />
                </label>
            </div>

            <div class="grid min-w-0 flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                <BaseInput
                    v-model="form.name"
                    label="Company name"
                    placeholder="Enter company name"
                    size="sm"
                    required
                />
                <BaseInput
                    v-model="form.tagline"
                    label="Tagline"
                    placeholder="A short company tagline"
                    size="sm"
                />
                <BaseInput
                    v-model="form.industry"
                    label="Industry"
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
                    label="Company email"
                    type="email"
                    placeholder="hello@example.com"
                    size="sm"
                />
                <BaseInput
                    v-model="form.phone"
                    label="Phone"
                    type="tel"
                    placeholder="+95 ..."
                    size="sm"
                />
            </div>
        </div>
    </section>
</template>
