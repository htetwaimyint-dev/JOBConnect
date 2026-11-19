<script setup lang="ts">
import type { JobDetailMode, Job, JobFormData } from "../types/job.types.ts";

import JobDetailHeader from "../components/JobDetailHeader.vue";
import JobOverview from "../components/JobOverview.vue";
import JobRichTextSection from "../components/JobRichTextSection.vue";
import JobSummarySidebar from "../components/JobSummarySidebar.vue";
// HWM add import for application form
const props = withDefaults(
    defineProps<{
        job: Job | JobFormData;
        mode: JobDetailMode;
        categoryName?: string;
        showApplicationForm?: boolean;
        applyError?: string;
        applicationSuccess?: boolean;
    }>(),
    {
        showApplicationForm: false,
        applyError: "",
        applicationSuccess: false,
    },
);

const emit = defineEmits<{
    apply: [];
    edit: [];
    submitted: [];
    cancel: [];
}>();

function handleEdit() {
    emit("edit");
}
</script>

<template>
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <main class="space-y-6 lg:col-span-8">
            <JobDetailHeader :job="job" />

            <JobOverview :job="job" />

            <JobRichTextSection title="About This Role" :content="job.description"
                empty-message="No job description provided." />

            <JobRichTextSection title="Requirements" :content="job.requirements"
                empty-message="No requirements provided." />

            <JobRichTextSection title="Responsibilities" :content="job.responsibilities"
                empty-message="No responsibilities provided." />
        </main>

        <JobSummarySidebar :job="job" :mode="mode" :category-name="categoryName"
            :show-application-form="showApplicationForm" :apply-error="applyError"
            :application-success="applicationSuccess" @apply="emit('apply')" @edit="handleEdit"
            @submitted="emit('submitted')" @cancel="emit('cancel')" />
    </div>
</template>
