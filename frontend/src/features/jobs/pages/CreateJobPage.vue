<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import type {
    Job,
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
import { useJobCategories } from "../composables/useJobCategories.ts";
import JobPreviewModal from "../components/JobPreviewModal.vue";

const router = useRouter();
const route = useRoute();

const isEditMode = computed(() => Boolean(route.params.id));

const jobId = computed(() =>
    isEditMode.value ? Number(route.params.id) : null,
);

const pageTitle = computed(() =>
    isEditMode.value ? "Edit Job" : "Create Job",
);

const { createJob, getJobById, updateJob } = useJobs();

const {
    data: existingJob,
    isLoading: jobLoading,
    isError: jobLoadingError,
} = getJobById(computed(() => jobId.value?.toString() ?? ""));
// ----------------------------------------
// Form state
// ----------------------------------------

const form = reactive<JobFormData>({
    title: "",
    category_id: null,
    deadline: "",
    employment_type: "full_time",
    vacancies: 1,
    gender: "any",
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

function populateForm(job: Job) {
    Object.assign(form, {
        title: job.title,
        category_id: job.category?.id ?? null,
        deadline: job.deadline ?? "",
        employment_type: job.employment_type,
        vacancies: job.vacancies,
        gender: job.gender,
        workplace_type: job.workplace_type,

        country: job.country ?? "",
        city: job.city ?? "",

        salary_type: job.salary_type,
        salary_min: job.salary_min ?? undefined,
        salary_max: job.salary_max ?? undefined,

        description: job.description,
        requirements: job.requirements,
        responsibilities: job.responsibilities ?? "",

        submission_status: (job.status === "draft"
            ? "draft"
            : "pending") as SubmissionStatus,
    });
}

watch(
    existingJob,
    (job) => {
        if (job) {
            populateForm(job);
        }
    },
    { immediate: true },
);

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
const {
    data: categories,
    isLoading: categoriesLoading,
    error: categoriesError,
} = useJobCategories();

const categoryOptions = computed(() =>
    (categories.value ?? []).map((category) => ({
        label: category.name,
        value: category.id,
    })),
);
console.log(categoryOptions);
// ----------------------------------------
// Create job mutation
// ----------------------------------------

const loading = computed(
    () => createJob.isPending.value || updateJob.isPending.value,
);

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
        if (isEditMode.value && jobId.value) {
            await updateJob.mutateAsync({
                id: jobId.value,
                data: { ...form },
            });
        } else {
            await createJob.mutateAsync({ ...form });
        }

        router.push({ name: "job-detail" });
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

        generalError.message = "Unable to save the job. Please try again.";

        console.error(error);
    }
}

// ----------------------------------------
// Preview
// ----------------------------------------
const showPreview = ref(false);

const previewCategoryName = computed(() => {
    return (
        categoryOptions.value.find(
            (category) => category.value === form.category_id,
        )?.label ?? "Uncategorized"
    );
});

function openPreview() {
    showPreview.value = true;
}

function closePreview() {
    showPreview.value = false;
}
</script>

<template>
    <div
        class="mx-auto max-w-7xl px-6 space-y-6 min-h-screen bg-gray-50/60 pb-20 font-sans text-slate-900"
    >
        <div
            v-if="isEditMode && jobLoading"
            class="flex min-h-96 items-center justify-center"
        >
            Loading job information...
        </div>

        <div
            v-else-if="isEditMode && (jobLoadingError || !existingJob)"
            class="flex min-h-96 flex-col items-center justify-center gap-3"
        >
            <h2 class="text-xl font-bold">Job Not Found</h2>

            <p class="text-sm text-gray-500">
                This job may have been removed or you may not have permission to
                edit it.
            </p>

            <button
                type="button"
                class="rounded-lg bg-gray-900 px-5 py-2.5 text-white"
                @click="router.back()"
            >
                Go Back
            </button>
        </div>

        <!-- Job form -->
        <form v-else @submit.prevent="submitJob('pending')">
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
                        :page-title="pageTitle"
                        @preview="openPreview"
                        @save-draft="submitJob('draft')"
                        @submit="submitJob('pending')"
                    />
                </aside>
            </div>
        </form>

        <Transition
            enter-active-class="transform transition ease-out duration-300"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transform transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-full opacity-0"
        >
            <JobPreviewModal
                v-if="showPreview"
                :job="form"
                :category-name="previewCategoryName"
                @close="closePreview"
            />
        </Transition>
    </div>
</template>
