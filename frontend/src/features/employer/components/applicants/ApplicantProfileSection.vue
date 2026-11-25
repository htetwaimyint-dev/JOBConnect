<script setup lang="ts">
import { computed } from "vue";
import {
    UserRound,
    Briefcase,
    GraduationCap,
    Code,
    FileText,
    MessageSquare,
    Eye,
} from "lucide-vue-next";


import type {
    EmployerApplication,
} from "@/features/applications/types/employer-application.types";

const props = defineProps<{
    application: EmployerApplication;
    previewLoading: boolean;
}>();

const emit = defineEmits<{
    "preview-resume": [];
}>();

const profile = computed(
    () => props.application.job_seeker?.profile,
);



const formatValue = (value: unknown): string => {
    if (value === null || value === undefined || value === "") {
        return "Not provided";
    }

    if (typeof value === "boolean") {
        return value ? "Yes" : "No";
    }

    if (typeof value === "object") {
        return JSON.stringify(value);
    }

    return String(value);
};

const formatLabel = (value: string): string =>
    value.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());

const formatPeriod = (
    start: unknown,
    end: unknown,
    isCurrent?: unknown,
) => {
    const formatMonth = (value: unknown) => {
        if (!value || typeof value !== "string") return "";

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) return "";

        return date.toLocaleDateString("en-US", {
            month: "short",
            year: "numeric",
        });
    };

    const startDate = formatMonth(start);
    const endDate = isCurrent === true ? "Present" : formatMonth(end);

    if (!startDate && !endDate) return "Date not provided";

    return `${startDate || "Unknown"} - ${endDate || "Present"}`;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Profile -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <UserRound class="h-4 w-4 text-blue-700" />
                Profile
            </h3>

            <div v-if="profile" class="rounded-xl border border-slate-200 p-4">
                <p v-if="profile.professional_title" class="text-sm font-semibold text-blue-700">
                    {{ profile.professional_title }}
                </p>

                <p v-else class="text-sm text-slate-400">
                    No professional title provided.
                </p>

                <p class="mt-4 text-sm leading-6 text-slate-600">
                    {{ profile.bio || "No biography provided." }}
                </p>

                <div class="mt-4 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <p class="text-xs text-slate-400">Location</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ profile.city || profile.address || "Not provided" }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">Expected Salary</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{
                                profile.expected_salary
                                    ? Number(profile.expected_salary).toLocaleString()
                                    : "Not provided"
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">Experience</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ profile.experience_years ?? 0 }} years
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">Gender</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ profile.gender || "Not provided" }}
                        </p>
                    </div>
                </div>
            </div>

            <p v-else class="text-sm text-slate-400">
                No profile information available.
            </p>
        </section>

        <!-- Skills -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <Code class="h-4 w-4 text-blue-700" />
                Skills
            </h3>

            <div v-if="application.job_seeker?.skills?.length" class="flex flex-wrap gap-2">
                <span v-for="(skill, index) in application.job_seeker.skills" :key="index"
                    class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-800">
                    {{ formatValue(skill.name ?? skill.skill_name ?? skill) }}
                </span>
            </div>

            <p v-else class="text-sm text-slate-400">
                No skills provided.
            </p>
        </section>

        <!-- Education -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <GraduationCap class="h-4 w-4 text-blue-700" />
                Education
            </h3>

            <div v-if="application.job_seeker?.educations?.length" class="space-y-3">
                <div v-for="education in application.job_seeker.educations" :key="Number(education.id)"
                    class="rounded-xl border border-slate-200 p-4">
                    <h4 class="font-semibold text-slate-900">
                        {{ education.degree || "Degree not provided" }}
                    </h4>

                    <p class="mt-1 text-sm font-medium text-blue-700">
                        {{ education.institution || "Institution not provided" }}
                    </p>

                    <p v-if="education.field_of_study" class="mt-1 text-sm text-slate-600">
                        {{ education.field_of_study }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        {{ formatPeriod(education.start_date, education.end_date) }}
                    </p>

                    <p v-if="education.description" class="mt-3 text-sm leading-6 text-slate-600">
                        {{ education.description }}
                    </p>
                </div>
            </div>

            <p v-else class="text-sm text-slate-400">
                No education information available.
            </p>
        </section>

        <!-- Experience -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <Briefcase class="h-4 w-4 text-blue-700" />
                Experience
            </h3>

            <div v-if="application.job_seeker?.experiences?.length" class="space-y-3">
                <div v-for="experience in application.job_seeker.experiences" :key="Number(experience.id)"
                    class="rounded-xl border border-slate-200 p-4">
                    <h4 class="font-semibold text-slate-900">
                        {{ experience.job_title || "Job title not provided" }}
                    </h4>

                    <p class="mt-1 text-sm font-medium text-blue-700">
                        {{ experience.company_name || "Company not provided" }}
                    </p>

                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                        <span>
                            {{
                                formatPeriod(
                                    experience.start_date,
                                    experience.end_date,
                                    experience.is_current,
                                )
                            }}
                        </span>

                        <span v-if="experience.employment_type" class="rounded-full bg-slate-100 px-2 py-1">
                            {{ formatLabel(String(experience.employment_type)) }}
                        </span>

                        <span v-if="experience.is_current" class="rounded-full bg-green-50 px-2 py-1 text-green-700">
                            Current
                        </span>
                    </div>

                    <p v-if="experience.description" class="mt-3 text-sm leading-6 text-slate-600">
                        {{ experience.description }}
                    </p>
                </div>
            </div>

            <p v-else class="text-sm text-slate-400">
                No work experience available.
            </p>
        </section>

        <!-- Resume -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <FileText class="h-4 w-4 text-blue-700" />
                Resume
            </h3>

            <div v-if="application.resume"
                class="flex flex-col gap-4 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="shrink-0 rounded-lg bg-red-50 p-3 text-red-600">
                        <FileText class="h-5 w-5" />
                    </div>

                    <div class="min-w-0">
                        <p class="break-words text-sm font-semibold text-slate-800">
                            {{ application.resume.title }}
                        </p>

                        <p class="mt-1 text-xs uppercase text-slate-500">
                            {{ application.resume.file_type || "Unknown file type" }}
                        </p>
                    </div>
                </div>

                <button type="button" :disabled="previewLoading" @click="emit('preview-resume')"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#102D59] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-900 disabled:cursor-not-allowed disabled:opacity-50">
                    <span v-if="previewLoading"
                        class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>

                    <Eye v-else class="h-4 w-4" />

                    {{ previewLoading ? "Loading..." : "View Resume" }}
                </button>
            </div>

            <p v-else class="text-sm text-slate-400">
                No resume attached.
            </p>
        </section>

        <!-- Application Answers -->
        <section>
            <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
                <MessageSquare class="h-4 w-4 text-blue-700" />
                Application answers
            </h3>

            <div v-if="application.answers?.length" class="space-y-3">
                <div v-for="answer in application.answers" :key="answer.id"
                    class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">
                        {{ answer.question?.question || "Question" }}
                    </p>

                    <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-600">
                        {{ answer.answer || "No answer provided" }}
                    </p>
                </div>
            </div>

            <p v-else class="text-sm text-slate-400">
                No additional answers available.
            </p>
        </section>
    </div>
</template>
