<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

import {
  ArrowLeft,
  BriefcaseBusiness,
  Building2,
  CheckCircle2,
  ClipboardList,
  Eye,
  FileText,
  LoaderCircle,
  MapPin,
  Save,
  Send,
  Sparkles,
  TriangleAlert,
} from "lucide-vue-next";

import type { JobFormField, SubmissionStatus } from "../types/job.types";

import { useJobs } from "../composables/useJobs";
import { useJobCategories } from "../composables/useJobCategories";

import JobDetailsSection from "../components/JobDetailsSection.vue";
import JobLocationSection from "../components/JobLocationSection.vue";
import JobSalarySection from "../components/JobSalarySection.vue";
import JobDescriptionSection from "../components/JobDescriptionSection.vue";
import JobFormActions from "../components/JobFormActions.vue";
import JobQuestionSection from "../components/JobQuestionSection.vue";
import JobPreviewModal from "../components/JobPreviewModal.vue";

import { form, populateForm } from "@/utils/jobs.utils";

const router = useRouter();
const route = useRoute();

const isEditMode = computed(() => Boolean(route.params.id));

const jobId = computed(() =>
  isEditMode.value ? Number(route.params.id) : null,
);

const pageTitle = computed(() =>
  isEditMode.value ? "Edit Job" : "Create New Job",
);

const { createJob, getJobById, updateJob } = useJobs();

const {
  data: existingJob,
  isLoading: jobLoading,
  isError: jobLoadingError,
} = getJobById(computed(() => jobId.value?.toString() ?? ""));

const isEditingLocked = computed(
  () =>
    existingJob.value?.status === "published" ||
    existingJob.value?.status === "closed",
);

// ----------------------------------------
// Navigation
// ----------------------------------------

function goBack() {
  if (isEditMode.value) {
    router.push({ name: "jobdetail", params: { id: jobId.value } });
    return;
  }

  router.push({ name: "EmployerJobs" });
}

// ----------------------------------------
// Prevent editing published or closed jobs
// ----------------------------------------

watch(
  existingJob,
  (job) => {
    if (
      job &&
      isEditMode.value &&
      (job.status === "published" || job.status === "closed")
    ) {
      window.alert(
        "This job cannot be edited because it is published or closed.",
      );

      router.replace({
        name: "EmployerJobDetail",
        params: { id: job.id },
      });
    } else if (job && isEditMode.value) {
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

// ----------------------------------------
// Mutations
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
    let createdJobId: number | string = "";
    if (isEditMode.value && jobId.value) {
      await updateJob.mutateAsync({
        id: jobId.value,
        data: { ...form },
      });

      router.push({ name: "EmployerJobDetail", params: { id: jobId.value } });
    } else {
      const response = await createJob.mutateAsync({ ...form });
      createdJobId = response?.data?.id || response?.id;

      router.push({ name: "EmployerJobDetail", params: { id: createdJobId } });
    }
  } catch (error) {
    if (axios.isAxiosError(error)) {
      if (error.response?.status === 422) {
        setErrors(error.response.data.errors ?? {});

        generalError.message =
          "One or more required fields are missing or invalid.";

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
  <main class="min-h-screen bg-[#f4f7fb] pb-16">
    <!-- Loading -->
    <div v-if="isEditMode && jobLoading" class="flex min-h-screen flex-col items-center justify-center gap-4">
      <LoaderCircle class="h-10 w-10 animate-spin text-blue-600" />
      <p class="text-sm font-semibold text-slate-500">
        Loading job information...
      </p>
    </div>

    <!-- Error -->
    <div v-else-if="isEditMode && (jobLoadingError || !existingJob)"
      class="flex min-h-screen items-center justify-center px-4">
      <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50">
          <TriangleAlert class="h-7 w-7 text-red-500" />
        </div>

        <h2 class="mt-5 text-xl font-extrabold text-slate-900">
          Job Not Found
        </h2>

        <p class="mt-2 text-sm leading-6 text-slate-500">
          This job may have been removed or you may not have permission to edit
          it.
        </p>

        <button type="button"
          class="mt-6 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
          @click="goBack">
          <ArrowLeft class="h-4 w-4" />
          Back
        </button>
      </div>
    </div>

    <!-- Locked Job -->
    <div v-else-if="isEditMode && isEditingLocked" class="flex min-h-screen flex-col items-center justify-center gap-3">
      <TriangleAlert class="h-10 w-10 text-amber-500" />

      <h2 class="text-xl font-bold text-slate-900">Editing Not Allowed</h2>

      <p class="text-sm text-slate-500">
        Published or closed jobs cannot be edited.
      </p>

      <button type="button" class="mt-3 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white" @click="goBack">
        Back to Job Details
      </button>
    </div>

    <!-- Main Form -->
    <div v-else class="mx-auto max-w-360 px-4 py-6 sm:px-6 lg:px-10 lg:py-9">
      <!-- Top Navigation -->
      <div class="mb-7 flex flex-wrap items-center justify-between gap-4">
        <button type="button" @click="goBack"
          class="group inline-flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-700 hover:shadow-md">
          <span
            class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 transition group-hover:bg-blue-50">
            <ArrowLeft class="h-4 w-4 transition-transform group-hover:-translate-x-1" />
          </span>

          Back to Jobs
        </button>

        <div class="flex items-center gap-2 text-xs font-medium text-slate-400 sm:text-sm">
          <!-- <span>Employer</span>
          <span>/</span>
          <span>Jobs</span>
          <span>/</span>
          <span class="font-bold text-slate-700">
            {{ isEditMode ? "Edit Job" : "Create Job" }}
          </span> -->
        </div>
      </div>

      <!-- Page Header -->
      <section
        class="relative mb-8 overflow-hidden rounded-[28px] bg-linear-to-br from-[#111d3b] via-[#172b52] to-[#244b83] p-6 shadow-xl shadow-blue-950/10 sm:p-8 lg:p-10">
        <div class="pointer-events-none absolute -right-20 -top-32 h-80 w-80 rounded-full bg-blue-400/10 blur-3xl">
        </div>

        <div class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-indigo-400/10 blur-3xl">
        </div>

        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
          <div>
            <div
              class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-blue-100">
              <Sparkles class="h-3.5 w-3.5" />
              Employer Workspace
            </div>

            <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
              {{ pageTitle }}
            </h1>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-100/75 sm:text-base">
              {{
                isEditMode
                  ? "Update your job information and manage your listing."
                  : "Create a new opportunity and find the right candidates for your company."
              }}
            </p>
          </div>

          <div
            class="hidden h-20 w-20 items-center justify-center rounded-3xl border border-white/15 bg-white/10 text-white shadow-lg backdrop-blur sm:flex">
            <BriefcaseBusiness class="h-10 w-10" />
          </div>
        </div>
      </section>

      <!-- Form Content -->
      <form @submit.prevent="submitJob('pending')">
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
          <!-- Main Form -->
          <div class="min-w-0 space-y-6 lg:col-span-9">
            <!-- General Error -->
            <div v-if="generalError.message"
              class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
              <TriangleAlert class="mt-0.5 h-5 w-5 shrink-0" />

              <p>{{ generalError.message }}</p>
            </div>

            <!-- Job Details -->
            <section
              class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
              <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex items-center gap-3">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <BriefcaseBusiness class="h-5 w-5" />
                  </div>

                  <div>
                    <h2 class="text-base font-extrabold text-slate-900">
                      Job Details
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                      Basic information about the position
                    </p>
                  </div>
                </div>

                <span class="hidden text-xs font-semibold text-blue-600 sm:block">
                  Step 01
                </span>
              </div>

              <div class="p-5 sm:p-7">
                <JobDetailsSection v-model="form" :errors="errors" :category-options="categoryOptions" />
              </div>
            </section>

            <!-- Location -->
            <section
              class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
              <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex items-center gap-3">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <MapPin class="h-5 w-5" />
                  </div>

                  <div>
                    <h2 class="text-base font-extrabold text-slate-900">
                      Workplace & Location
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                      Set the work arrangement and location
                    </p>
                  </div>
                </div>

                <span class="hidden text-xs font-semibold text-indigo-600 sm:block">
                  Step 02
                </span>
              </div>

              <div class="p-5 sm:p-7">
                <JobLocationSection v-model="form" :errors="errors" />
              </div>
            </section>

            <!-- Salary -->
            <section
              class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
              <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <Building2 class="h-5 w-5" />
                </div>

                <div>
                  <h2 class="text-base font-extrabold text-slate-900">
                    Compensation
                  </h2>

                  <p class="mt-1 text-xs text-slate-400">
                    Provide salary information
                  </p>
                </div>
              </div>

              <div class="p-5 sm:p-7">
                <JobSalarySection v-model="form" :errors="errors" />
              </div>
            </section>

            <!-- Description -->
            <section
              class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
              <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                  <FileText class="h-5 w-5" />
                </div>

                <div>
                  <h2 class="text-base font-extrabold text-slate-900">
                    Job Description
                  </h2>

                  <p class="mt-1 text-xs text-slate-400">
                    Describe the role and its expectations
                  </p>
                </div>
              </div>

              <div class="p-5 sm:p-7">
                <JobDescriptionSection v-model="form" :errors="errors" />
              </div>
            </section>

            <!-- Application Questions -->
            <section
              class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
              <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                  <ClipboardList class="h-5 w-5" />
                </div>

                <div>
                  <h2 class="text-base font-extrabold text-slate-900">
                    Application Questions
                  </h2>

                  <p class="mt-1 text-xs text-slate-400">
                    Add custom questions for applicants
                  </p>
                </div>
              </div>

              <div class="p-5 sm:p-7">
                <JobQuestionSection v-model="form.job_questions" />
              </div>
            </section>
          </div>

          <!-- Right Action Panel -->
          <aside class="min-w-0 space-y-5 lg:sticky lg:top-6 lg:col-span-3">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 bg-linear-to-br from-blue-50 via-white to-indigo-50 px-5 py-5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                  <Sparkles class="h-5 w-5" />
                </div>

                <h3 class="mt-4 text-base font-extrabold text-slate-900">
                  Publish Your Job
                </h3>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                  Review your information before submitting.
                </p>
              </div>

              <div class="space-y-4 p-5">
                <div class="flex items-center gap-3 text-xs text-slate-600">
                  <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />
                  Complete job information
                </div>

                <div class="flex items-center gap-3 text-xs text-slate-600">
                  <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />
                  Add clear requirements
                </div>

                <div class="flex items-center gap-3 text-xs text-slate-600">
                  <CheckCircle2 class="h-4 w-4 shrink-0 text-emerald-500" />
                  Review before submission
                </div>
              </div>
            </div>

            <!-- Existing Form Actions -->
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
              <JobFormActions :loading="loading" :error-message="generalError.message" :page-title="pageTitle"
                @preview="openPreview" @save-draft="submitJob('draft')" @submit="submitJob('pending')" />
            </div>

            <!-- Preview Hint -->
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">
              <div class="flex items-start gap-2.5">
                <Eye class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600" />

                <p class="text-xs leading-5 text-indigo-700">
                  Use Preview to check how your job listing will appear before
                  submitting it.
                </p>
              </div>
            </div>
          </aside>
        </div>
      </form>
    </div>

    <!-- Preview Modal -->
    <Transition enter-active-class="transform transition ease-out duration-300"
      enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transform transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0">
      <JobPreviewModal v-if="showPreview" :job="form" :category-name="previewCategoryName" @close="closePreview" />
    </Transition>
  </main>
</template>
