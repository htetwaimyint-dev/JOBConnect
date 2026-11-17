<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";

import type { JobFormData, Job } from "../types/job.types.ts";

import JobDetailContent from "../components/JobDetailContent.vue";

interface PreviewPayload {
    job: JobFormData;
    categoryName?: string;
}

const router = useRouter();

const preview = ref<PreviewPayload | null>(null);
const loading = ref(true);

const job = computed<JobFormData | null>(() => {
    if (!preview.value) return null;

    return preview.value.job;
});

onMounted(() => {
    try {
        const storedPreview = sessionStorage.getItem("job-preview");

        if (!storedPreview) {
            router.replace({ name: "employer-create-job" });
            return;
        }

        preview.value = JSON.parse(storedPreview);
    } catch {
        router.replace({ name: "employer-create-job" });
    } finally {
        loading.value = false;
    }
});

function backToEditing() {
    router.push({ name: "employer-create-job" });
}
</script>

<template>
    <div class="min-h-screen bg-[#f7f7fb]">
        <div
            v-if="loading"
            class="flex min-h-screen items-center justify-center"
        >
            Loading preview...
        </div>

        <template v-else-if="job">
            <div class="border-b border-gray-200 bg-white">
                <div
                    class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
                >
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">
                            Job Preview
                        </h1>

                        <p class="text-sm text-gray-500">
                            This is how your job listing will appear.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <JobDetailContent
                    :job="job"
                    mode="preview"
                    :category-name="preview?.categoryName"
                />
            </div>
        </template>
    </div>
</template>
