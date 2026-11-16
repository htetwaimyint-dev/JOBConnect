<script setup lang="ts">
import type { JobFormData } from "../types/job.types";
import BaseTextArea from "@/components/common/BaseTextArea.vue";

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
            Job Description and Requirements
        </h2>

        <div class="space-y-6">
            <!-- Job Description -->
            <BaseTextArea
                v-model="form.description"
                label="Job Description"
                placeholder="Describe the job, its purpose, and what the candidate will do..."
                :error="errors.description?.[0]"
            />

            <!-- Requirements -->
            <BaseTextArea
                v-model="form.requirements"
                label="Requirements"
                placeholder="Enter the qualifications, skills, and experience required..."
                :error="errors.requirements?.[0]"
            />

            <!-- Responsibilities -->
            <BaseTextArea
                v-model="form.responsibilities"
                label="Responsibilities"
                placeholder="Describe the candidate's main responsibilities..."
                :error="errors.responsibilities?.[0]"
            />
        </div>
    </section>
</template>
