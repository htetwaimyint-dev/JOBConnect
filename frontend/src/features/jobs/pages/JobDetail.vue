<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useJobs } from "../composables/useJobs";
import JobDetailContent from "../components/JobDetailContent.vue";


import { useAuthStore } from "@/stores/auth.store";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const jobId = computed(() => String(route.params.id));

const { getJobById } = useJobs();

const { data: job, isLoading, isError } = getJobById(jobId);

const showApplicationForm = ref(false);
const applyError = ref("");
const applicationSuccess = ref(false);

function applyForJob() {
    applyError.value = "";
    applicationSuccess.value = false;

    // Guest user
    if (!authStore.isAuthenticated) {
        router.push({ name: "login" });
        return;
    }

    // Only Job Seekers can apply
    if (authStore.user?.role !== "job_seeker") {
        applyError.value =
            "Only job seekers can apply for this job.";
        return;
    }

    // Show application form on the same page
    showApplicationForm.value = true;
}

function handleApplicationSubmitted() {
    showApplicationForm.value = false;
    applicationSuccess.value = true;
}

function editJob() {
    router.push({
        name: "employer-job-edit",
        params: { id: job.value?.id },
    });
}
</script>

<template>
    <div class="min-h-screen bg-[#f7f7fb]">
        <div v-if="isLoading" class="flex min-h-screen items-center justify-center">
            Loading job details...
        </div>

        <div v-else-if="isError || !job" class="flex min-h-screen flex-col items-center justify-center gap-4">
            <h2 class="text-xl font-bold text-gray-900">
                Job Not Found
            </h2>

            <p class="text-sm text-gray-500">
                This job may have been removed or is no longer available.
            </p>

            <button type="button" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white"
                @click="router.back()">
                Go Back
            </button>
        </div>

        <div v-else class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <JobDetailContent :job="job" mode="public" :show-application-form="showApplicationForm"
                :apply-error="applyError" :application-success="applicationSuccess" @apply="applyForJob" @edit="editJob"
                @submitted="handleApplicationSubmitted" @cancel="showApplicationForm = false" />


        </div>
    </div>
</template>
