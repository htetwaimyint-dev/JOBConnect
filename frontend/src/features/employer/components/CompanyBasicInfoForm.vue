<script setup lang="ts">
import type { CompanyForm } from "../types/company-form.types";
import BaseInput from "@/components/common/BaseInput.vue";

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
    <section
        class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs"
    >
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">Who We Are</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Primary identity settings and contact metadata.
            </p>
        </div>

        <!-- Inputs styled with Built In 2-column grid layout -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <BaseInput
                v-model="form.name"
                label="Name *"
                placeholder="Company name"
                size="sm"
                required
            />
            <BaseInput
                :v-model="form.foundedYear"
                label="Year Founded"
                type="number"
                placeholder="e.g. 2016"
                size="sm"
            />
            <BaseInput
                v-model="form.industry"
                label="Industry Type"
                placeholder="e.g. Software, Fintech"
                size="sm"
            />
            <BaseInput
                v-model="form.tagline"
                label="Tagline"
                placeholder="Short company slogan"
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
                placeholder="hello@example.com"
                size="sm"
            />
        </div>
    </section>
</template>
