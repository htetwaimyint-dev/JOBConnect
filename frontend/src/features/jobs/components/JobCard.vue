<script setup lang="ts">
import { RouterLink } from "vue-router";
import type { Job } from "../types/job.types";
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.store";
import { useSavedJobs } from "../composables/useSavedJobs";

const props = defineProps<{
    job: Job;
}>();

const router = useRouter();
const authStore = useAuthStore();

const {
    isSaved,
    toggleSavedJob,
    saveMutation,
    removeMutation,
    isJobSeeker,
} = useSavedJobs();

const saved = isSaved(props.job.id);

const isSaving = computed(
    () => saveMutation.isPending.value || removeMutation.isPending.value
);

async function handleSaveJob() {
    // Guest → Login
    if (!authStore.isAuthenticated) {
        router.push({ name: "login" });
        return;
    }

    // Only job seekers can save jobs
    if (!isJobSeeker.value) {
        return;
    }

    try {
        await toggleSavedJob(props.job.id);
    } catch (error) {
        console.error("Failed to save job:", error);
    }
}

const formatSalary = (job: Job) => {
    if (job.salary_type === "negotiable") {
        return "Negotiable";
    }

    if (job.salary_min === null && job.salary_max === null) {
        return "Not specified";
    }

    const min = job.salary_min?.toLocaleString() ?? "";
    const max = job.salary_max?.toLocaleString() ?? "";

    if (min && max) return `${min} - ${max}`;

    return min || max;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

const formatLabel = (value: string) => {
    return value.replaceAll("_", " ").replace(/\b\w/g, (char) =>
        char.toUpperCase()
    );
};
</script>

<template>
    <article
        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5">
        <!-- Top Accent -->
        <div
            class="absolute left-0 top-0 h-1 w-0 bg-gradient-to-r from-blue-600 to-cyan-400 transition-all duration-500 group-hover:w-full">
        </div>

        <!-- Company & Job Title -->
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <img v-if="job.company.logo" :src="job.company.logo" :alt="job.company.name"
                    class="h-14 w-14 rounded-2xl border border-slate-100 bg-white object-cover p-1" />

                <div v-else
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 text-xl font-bold text-white shadow-md shadow-blue-600/20">
                    {{ job.company.name.charAt(0).toUpperCase() }}
                </div>
            </div>

            <div class="min-w-0 flex-1">
                <h3
                    class="line-clamp-2 text-lg font-bold leading-snug text-[#102D59] transition group-hover:text-blue-600">
                    {{ job.title }}
                </h3>

                <p class="mt-1 truncate text-sm font-medium text-slate-500">
                    {{ job.company.name }}
                </p>
            </div>


            <button v-if="!authStore.isAuthenticated || isJobSeeker" type="button" :disabled="isSaving"
                @click="handleSaveJob"
                class="shrink-0 flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition"
                :class="saved
                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                    : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700'
                    ">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18l-6-4-6 4V4Z"
                        :fill="saved ? 'currentColor' : 'none'" />
                </svg>

                {{ saved ? "Saved" : "Save" }}
            </button>
        </div>

        <!-- Job Tags -->
        <div class="mt-5 flex flex-wrap gap-2">
            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                {{ formatLabel(job.employment_type) }}
            </span>

            <span class="rounded-full bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700">
                {{ formatLabel(job.workplace_type) }}
            </span>

            <span v-if="job.category" class="rounded-full bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-700">
                {{ job.category.name }}
            </span>
        </div>

        <!-- Job Details -->
        <div class="mt-6 space-y-4">
            <!-- Location -->
            <div class="flex items-center gap-3 text-sm text-slate-600">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                </div>

                <span class="min-w-0 truncate">
                    {{ [job.city, job.country].filter(Boolean).join(", ") || "Location not specified" }}
                </span>
            </div>

            <!-- Salary -->
            <div class="flex items-center gap-3 text-sm">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="6" width="18" height="13" rx="2" />
                        <path d="M3 10h18M8 15h3" />
                    </svg>
                </div>

                <span class="font-semibold text-slate-800">
                    {{ formatSalary(job) }}
                </span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-5 mt-6">
            <div>
                <p class="text-xs text-slate-400">Posted on</p>
                <p class="mt-1 text-sm font-medium text-slate-600">
                    {{ formatDate(job.created_at) }}
                </p>
            </div>

            <RouterLink :to="`/jobs/${job.id}`"
                class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white shadow-md shadow-blue-900/10 transition-all duration-300 hover:bg-blue-600 hover:shadow-lg">
                View Details

                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14m-6-6 6 6-6 6" />
                </svg>
            </RouterLink>
        </div>
    </article>
</template>
