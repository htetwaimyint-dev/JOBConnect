<script setup lang="ts">
import { useAuthStore } from "@/stores/auth.store";
import type { Job, JobDetailMode, JobFormData } from "../types/job.types";

import { formatJobLabel } from "@/utils/jobs.utils";
import BaseButton from "@/components/common/BaseButton.vue";
import ApplicationForm from "@/features/applications/components/ApplicationForm.vue";

import { computed } from "vue";
import { useRouter } from "vue-router";
import { useSavedJobs } from "@/features/jobs/composables/useSavedJobs";

const props = defineProps<{
    job: Job | JobFormData;
    mode: JobDetailMode;
    categoryName?: string;
    showApplicationForm: boolean;
    applyError: string;
    applicationSuccess: boolean;
}>();

const emit = defineEmits<{
    apply: [];
    edit: [];
    submitted: [];
    cancel: [];
}>();

function handleClick() {
    emit("edit");
}

function handleApply() {
    emit("apply");
}

const router = useRouter();

const authStore = useAuthStore();
const role = authStore.user?.role;

const {
    isSaved,
    toggleSavedJob,
    saveMutation,
    removeMutation,
    isJobSeeker,
} = useSavedJobs();

const saved = computed(() => {
    if ("id" in props.job) {
        return isSaved(props.job.id).value;
    }

    return false;
});

const isSaving = computed(
    () => saveMutation.isPending.value || removeMutation.isPending.value
);

async function handleSaveJob() {
    // Guest → Login
    if (!authStore.isAuthenticated) {
        router.push({ name: "login" });
        return;
    }

    // Only Job Seekers
    if (!isJobSeeker.value) {
        return;
    }

    if (!("id" in props.job)) {
        return;
    }

    try {
        await toggleSavedJob(props.job.id);
    } catch (error) {
        console.error("Failed to save job:", error);
    }
}
</script>

<template>
    <aside class="space-y-5 lg:col-span-4">

        <!-- Employer Actions -->
        <div v-if="role === 'employer'" class="space-y-4">
            <div v-if="mode === 'preview'">
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
                    <h3 class="text-sm font-bold text-blue-900">
                        Preview Mode
                    </h3>

                    <p class="mt-2 text-xs leading-5 text-blue-700">
                        This is a preview of your job listing. Your changes
                        will not be published until you submit the job for
                        approval.
                    </p>
                </div>

                <BaseButton
                    class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700">
                    Back to Editing
                </BaseButton>
            </div>

            <div v-else>
                <BaseButton
                    class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700"
                    @click="handleClick">
                    Edit Job
                </BaseButton>
            </div>
        </div>

        <!-- Public / Job Seeker Application Section -->
        <div v-else class="space-y-4">


            <!-- Save Job Button -->
            <BaseButton v-if="mode === 'public' && (!authStore.isAuthenticated || isJobSeeker)" :disabled="isSaving"
                class="w-full border border-blue-200 bg-blue-50 px-5 py-2.5 text-[15px]! font-semibold text-blue-700 hover:bg-blue-100"
                @click="handleSaveJob">
                {{ isSaving ? "Saving..." : saved ? "Remove Saved Job" : "Save Job" }}
            </BaseButton>

            <!-- Apply Button -->
            <BaseButton class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700"
                @click="handleApply">
                Apply Now
            </BaseButton>

            <!-- Application Error -->
            <div v-if="applyError" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ applyError }}
            </div>

            <!-- Application Success -->
            <div v-if="applicationSuccess"
                class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                Your application has been submitted successfully!
            </div>

            <!-- Application Form -->
            <ApplicationForm v-if="showApplicationForm && 'id' in job" :job-id="job.id" @submitted="emit('submitted')"
                @cancel="emit('cancel')" />
        </div>

        <!-- Job Summary -->
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">
                Job Summary
            </h3>

            <div class="mt-5 space-y-4">

                <div>
                    <p class="text-xs text-gray-500">Job Title</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ job.title || "Not specified" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Category</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ categoryName || "Not specified" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Workplace</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatJobLabel(job.workplace_type) || "Not specified" }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Location</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{
                            [job.city, job.country]
                                .filter(Boolean)
                                .join(", ") || "Not specified"
                        }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Application Deadline</p>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ job.deadline || "Not specified" }}
                    </p>
                </div>

            </div>
        </div>

    </aside>
</template>