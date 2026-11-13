<script setup lang="ts">
import type { JobFormData } from "../types/job.types";

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

        <div class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-medium">
                    Job Description
                </label>

                <textarea
                    v-model="form.description"
                    rows="5"
                    class="w-full rounded-xl border border-gray-300 bg-white p-3.5 text-xs text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition resize-y"
                    placeholder="Describe the job..."
                />

                <p
                    v-if="errors.description?.[0]"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ errors.description[0] }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">
                    Requirements
                </label>

                <textarea
                    v-model="form.requirements"
                    rows="4"
                    class="w-full rounded-xl border border-gray-300 bg-white p-3.5 text-xs text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition resize-y"
                    placeholder="Enter job requirements..."
                />
                <p
                    v-if="errors.description?.[0]"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ errors.description[0] }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">
                    Responsibilities
                </label>

                <textarea
                    v-model="form.responsibilities"
                    rows="4"
                    class="w-full rounded-xl border border-gray-300 bg-white p-3.5 text-xs text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition resize-y"
                    placeholder="Enter job responsibilities..."
                />
                <p
                    v-if="errors.description?.[0]"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ errors.description[0] }}
                </p>
            </div>
        </div>
    </section>
</template>
