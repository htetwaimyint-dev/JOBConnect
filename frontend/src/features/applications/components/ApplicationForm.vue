<script setup lang="ts">
import { onMounted, ref } from "vue";
import axios from "axios";

import { useAuthStore } from "@/stores/auth.store";
import { resumeRepository } from "@/features/job-seeker/repositories/resumeRepository";

import applicationRepository from "../repositories/applicationRepository";

import type { Resume } from "@/features/job-seeker/types/resume.types";
import type { JobApplicationQuestion } from "../types/application.types";

const props = defineProps<{
    jobId: number;
}>();

const emit = defineEmits<{
    submitted: [];
    cancel: [];
}>();

const authStore = useAuthStore();

const applicantName = ref(authStore.user?.name ?? "");
const applicantPhone = ref("");
const selectedResumeId = ref<number | "">("");

const resumes = ref<Resume[]>([]);
const questions = ref<JobApplicationQuestion[]>([]);
const answers = ref<Record<number, string>>({});

const isLoading = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const validationErrors = ref<Record<string, string[]>>({});

onMounted(async () => {
    await loadFormData();
});

async function loadFormData() {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        const [resumeData, questionData] = await Promise.all([
            resumeRepository.getAll(),
            applicationRepository.getApplicationQuestions(props.jobId),
        ]);

        resumes.value = resumeData;
        questions.value = questionData;

        const defaultResume = resumeData.find(
            (resume) => resume.is_default,
        );

        if (defaultResume) {
            selectedResumeId.value = defaultResume.id;
        }
    } catch (error) {
        console.error("Failed to load application form:", error);
        errorMessage.value =
            "Unable to load application information. Please try again.";
    } finally {
        isLoading.value = false;
    }
}

function getFieldError(field: string): string {
    return validationErrors.value[field]?.[0] ?? "";
}

async function submitApplication() {
    errorMessage.value = "";
    successMessage.value = "";
    validationErrors.value = {};

    if (!applicantName.value.trim()) {
        errorMessage.value = "Please enter your name.";
        return;
    }

    if (!applicantPhone.value.trim()) {
        errorMessage.value = "Please enter your phone number.";
        return;
    }

    if (!selectedResumeId.value) {
        errorMessage.value = "Please select a resume.";
        return;
    }

    for (const question of questions.value) {
        if (
            question.is_required &&
            !answers.value[question.id]?.trim()
        ) {
            errorMessage.value =
                `Please answer the required question: ${question.question}`;
            return;
        }
    }

    const submittedAnswers = questions.value
        .map((question) => {
            const answer = answers.value[question.id]?.trim();

            if (!answer) {
                return null;
            }

            return {
                job_question_id: question.id,
                answer,
            };
        })
        .filter(
            (
                item,
            ): item is {
                job_question_id: number;
                answer: string;
            } => item !== null,
        );

    isSubmitting.value = true;
    try {
        await applicationRepository.submitApplication({
            job_id: props.jobId,
            applicant_name: applicantName.value.trim(),
            applicant_phone: applicantPhone.value.trim(),
            resume_id: Number(selectedResumeId.value),
            answers: submittedAnswers,
        });

        successMessage.value = "Your application was submitted successfully.";

        emit("submitted");
    } catch (error: unknown) {
        console.error("Application submission failed:", error);

        if (axios.isAxiosError(error)) {
            const response = error.response?.data;

            if (response?.errors) {
                validationErrors.value = response.errors;
                errorMessage.value =
                    response.message ?? "Please check your information.";
            } else {
                errorMessage.value =
                    response?.message ?? "Failed to submit your application.";
            }
        } else {
            errorMessage.value =
                "Something went wrong. Please try again.";
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>



<template>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <!-- Compact Header -->
        <div class="px-4 pt-5 pb-3 sm:px-5">
            <h2 class="text-xl font-bold text-slate-900">
                Apply for this Job
            </h2>

            <div class="mt-3 h-0.5 w-full bg-blue-800"></div>
        </div>

        <!-- Form Content -->
        <div class="space-y-4 px-4 pb-5 sm:px-5">

            <!-- Loading -->
            <div v-if="isLoading" class="flex items-center justify-center gap-2 py-8">
                <div class="h-5 w-5 animate-spin rounded-full border-2 border-blue-100 border-t-blue-700"></div>
                <p class="text-sm text-slate-500">
                    Loading application form...
                </p>
            </div>

            <div v-else class="space-y-4">

                <!-- Error -->
                <div v-if="errorMessage"
                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700">
                    {{ errorMessage }}
                </div>

                <!-- Success -->
                <div v-if="successMessage"
                    class="rounded-lg border border-green-200 bg-green-50 px-3 py-2.5 text-sm text-green-700">
                    {{ successMessage }}
                </div>

                <!-- Personal Information -->
                <div class="space-y-3">

                    <!-- Full Name -->
                    <div>
                        <input v-model="applicantName" type="text" placeholder="Full Name"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-100" />

                        <p v-if="getFieldError('applicant_name')" class="mt-1 text-xs text-red-600">
                            {{ getFieldError("applicant_name") }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <input v-model="applicantPhone" type="tel" placeholder="Phone Number"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-100" />

                        <p v-if="getFieldError('applicant_phone')" class="mt-1 text-xs text-red-600">
                            {{ getFieldError("applicant_phone") }}
                        </p>
                    </div>
                </div>




                <!-- Resume Selection -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Select Your CV / Resume
                        <span class="text-red-500">*</span>
                    </label>

                    <div v-if="resumes.length > 0">
                        <select v-model="selectedResumeId"
                            class="w-full h-[50px] rounded-lg border border-slate-300 bg-white px-4 text-base font-medium text-slate-800 shadow-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            <option value="" disabled>
                                Choose your resume
                            </option>

                            <option v-for="resume in resumes" :key="resume.id" :value="resume.id">
                                {{ resume.title }}
                                {{ resume.is_default ? "(Default)" : "" }}
                            </option>
                        </select>
                    </div>

                    <div v-else class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-3 text-sm text-amber-800">
                        You don't have any resumes yet. Please upload a resume before applying.
                    </div>

                    <p v-if="getFieldError('resume_id')" class="mt-1 text-xs text-red-600">
                        {{ getFieldError("resume_id") }}
                    </p>
                </div>

                <!-- Application Questions -->
                <div v-if="questions.length > 0" class="space-y-3">

                    <div class="border-t border-slate-200 pt-3">
                        <h3 class="text-sm font-semibold text-slate-800">
                            Additional Questions
                        </h3>
                    </div>

                    <div v-for="(question, index) in questions" :key="question.id" class="space-y-2">
                        <label class="block text-sm font-medium leading-5 text-slate-700">
                            {{ index + 1 }}. {{ question.question }}

                            <span v-if="question.is_required" class="text-red-500">
                                *
                            </span>

                            <span v-else class="text-xs font-normal text-slate-400">
                                (Optional)
                            </span>
                        </label>

                        <textarea v-model="answers[question.id]" rows="2" :placeholder="question.is_required
                            ? 'Your answer...'
                            : 'Your answer (optional)...'"
                            class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-100"></textarea>

                        <p v-if="getFieldError(`answers.${index}.answer`)" class="text-xs text-red-600">
                            {{ getFieldError(`answers.${index}.answer`) }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2 pt-1">

                    <!-- Apply Button -->
                    <button type="button"
                        class="w-full rounded-lg bg-blue-700 px-4 py-3 text-base font-semibold text-white shadow-md transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="isSubmitting || isLoading || resumes.length === 0" @click="submitApplication">
                        {{
                            isSubmitting
                                ? "Submitting Application..."
                                : "Apply Now"
                        }}
                    </button>

                    <!-- Cancel -->
                    <button type="button"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="isSubmitting" @click="emit('cancel')">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    </section>
</template>