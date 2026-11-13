<script setup lang="ts">
import { computed, reactive, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import type {
    JobFormData,
    JobFormField,
    SubmissionStatus,
} from "../types/job.types";

import { useJobs } from "../composables/useJobs";

import JobDetailsSection from "../components/JobDetailsSection.vue";
import JobLocationSection from "../components/JobLocationSection.vue";
import JobSalarySection from "../components/JobSalarySection.vue";
import JobDescriptionSection from "../components/JobDescriptionSection.vue";
import JobFormActions from "../components/JobFormActions.vue";

const router = useRouter();

// ----------------------------------------
// Form state
// ----------------------------------------

const form = reactive<JobFormData>({
    title: "",
    category_id: null,
    deadline: "",
    employment_type: "full_time",
    vacancies: 1,
    workplace_type: "onsite",

    country: "",
    city: "",

    salary_type: "fixed",
    salary_min: undefined,
    salary_max: undefined,

    description: "",
    requirements: "",
    responsibilities: "",

    submission_status: "pending",
});

// ----------------------------------------
// Backend validation errors
// ----------------------------------------

const errors = reactive<Record<string, string[]>>({});

const generalError = reactive({
    message: "",
});

function clearErrors() {
    Object.keys(errors).forEach((field) => {
        delete errors[field];
    });

    generalError.message = "";
}

function clearFieldError(field: JobFormField) {
    delete errors[field];
}

function setErrors(validationErrors: Record<string, string[]>) {
    clearErrors();

    Object.entries(validationErrors).forEach(([field, messages]) => {
        errors[field] = messages;
    });
}

// Clear an individual backend error when its field changes.
const fieldsWithErrors: JobFormField[] = [
    "title",
    "category_id",
    "deadline",
    "employment_type",
    "vacancies",
    "workplace_type",
    "country",
    "city",
    "salary_type",
    "salary_min",
    "salary_max",
    "description",
    "requirements",
    "responsibilities",
];

fieldsWithErrors.forEach((field) => {
    watch(
        () => form[field],
        () => clearFieldError(field),
    );
});

// ----------------------------------------
// Category options
// ----------------------------------------

// Connect this to your categories API using TanStack Query.
const categoryOptions = computed(() => []);

// ----------------------------------------
// Create job mutation
// ----------------------------------------

const { createJob } = useJobs();

const loading = computed(() => createJob.isPending.value);

// ----------------------------------------
// Submit job
// ----------------------------------------

async function submitJob(status: SubmissionStatus) {
    clearErrors();

    form.submission_status = status;

    if (form.salary_type === "negotiable") {
        form.salary_min = undefined;
        form.salary_max = undefined;
    }

    try {
        await createJob.mutateAsync(form);

        //router.push({ name: "employer-jobs" });
    } catch (error) {
        if (axios.isAxiosError(error)) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors ?? {});
                generalError.message =
                    "One or more required fields are missing or invalid";
                return;
            }

            generalError.message =
                error.response?.data?.message ??
                "Something went wrong. Please try again.";

            return;
        }

        generalError.message = "Unable to create the job. Please try again.";

        console.error(error);
    }
}

// ----------------------------------------
// Preview
// ----------------------------------------

function previewJob() {
    // Implement your job preview modal or preview route here.
}
</script>

<template>
    <div
        class="mx-auto max-w-7xl px-6 space-y-6 min-h-screen bg-gray-50/60 pb-20 font-sans text-slate-900"
    >
        <!-- Job form -->
        <form @submit.prevent="submitJob('pending')">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
                <!-- Main form sections -->
                <div class="space-y-6 lg:col-span-9">
                    <JobDetailsSection
                        v-model="form"
                        :errors="errors"
                        :category-options="categoryOptions"
                    />

                    <JobLocationSection v-model="form" :errors="errors" />

                    <JobSalarySection v-model="form" :errors="errors" />

                    <JobDescriptionSection v-model="form" :errors="errors" />
                </div>

                <!-- Form actions -->
                <aside class="lg:col-span-3 sticky top-23 space-y-6 self-start">
                    <JobFormActions
                        :loading="loading"
                        :error-message="generalError.message"
                        @preview="previewJob"
                        @save-draft="submitJob('draft')"
                        @submit="submitJob('pending')"
                    />
                </aside>
            </div>
        </form>
    </div>
</template>
