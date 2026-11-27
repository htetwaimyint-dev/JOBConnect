<script setup lang="ts">
import { computed } from "vue";
import { Sparkles } from "lucide-vue-next";

import type {
    EmployerApplication,
} from "@/features/applications/types/employer-application.types";

const props = defineProps<{
    application: EmployerApplication;
    evaluating: boolean;
    canEvaluate: boolean;
    evaluationError?: string | null;
}>();

const emit = defineEmits<{
    "run-evaluation": [];
}>();

// Current applicant's AI evaluation.
const aiEvaluation = computed(
    () => props.application.ai_evaluation ?? null,
);

// AI evaluation strengths.
const aiStrengths = computed(() => {
    const value = aiEvaluation.value?.strengths;
    return Array.isArray(value) ? value : [];
});

// AI evaluation weaknesses.
const aiWeaknesses = computed(() => {
    const value = aiEvaluation.value?.weaknesses;
    return Array.isArray(value) ? value : [];
});

// Matched skills.
const aiMatchedSkills = computed(() => {
    const value = aiEvaluation.value?.matched_skills;
    return Array.isArray(value) ? value : [];
});

// Missing skills.
const aiMissingSkills = computed(() => {
    const value = aiEvaluation.value?.missing_skills;
    return Array.isArray(value) ? value : [];
});

// Relevant experience.
const aiRelevantExperience = computed(() => {
    const value = aiEvaluation.value?.relevant_experience;
    return Array.isArray(value) ? value : [];
});

// Match score.
const matchScore = computed(() => {
    const score = Number(aiEvaluation.value?.match_score ?? 0);

    if (!Number.isFinite(score)) return 0;

    return Math.min(100, Math.max(0, score));
});

// Recommendation label.
const recommendationLabel = computed(() => {
    return String(
        aiEvaluation.value?.recommendation ?? "Not available",
    )
        .replace(/_/g, " ")
        .replace(/\b\w/g, (char) => char.toUpperCase());
});

// Recommendation color.
const recommendationClass = computed(() => {
    const recommendation = String(
        aiEvaluation.value?.recommendation ?? "",
    ).toLowerCase();

    if (recommendation.includes("high")) {
        return "bg-green-100 text-green-700 ring-green-200";
    }

    if (recommendation.includes("moderate")) {
        return "bg-amber-100 text-amber-700 ring-amber-200";
    }

    if (recommendation.includes("low")) {
        return "bg-red-100 text-red-700 ring-red-200";
    }

    return "bg-slate-100 text-slate-700 ring-slate-200";
});

// Button label.
const evaluationButtonLabel = computed(() => {
    if (props.evaluating) return "Evaluating...";

    return aiEvaluation.value
        ? "Re-evaluate"
        : "Run AI Evaluation";
});
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-purple-100 bg-white shadow-sm">

        <!-- Header -->
        <div
            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-white px-5 py-4">

            <div>
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100">
                        <Sparkles class="h-5 w-5 text-purple-600" />
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-900">
                            AI Applicant Assessment
                        </h3>

                        <p class="text-xs text-slate-500">
                            Intelligent applicant evaluation
                        </p>
                    </div>
                </div>
            </div>

            <button type="button" :disabled="evaluating || !canEvaluate" @click="emit('run-evaluation')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-50">
                <Sparkles v-if="!evaluating" class="h-4 w-4" />

                <span v-else
                    class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>

                {{ evaluationButtonLabel }}
            </button>
        </div>

        <!-- Application Status Notice -->
        <div v-if="!canEvaluate && !aiEvaluation"
            class="mx-5 mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
            AI evaluation is available for pending or reviewed applications only.
        </div>

        <!-- Evaluation Error -->
        <div v-if="evaluationError"
            class="mx-5 mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600">
            {{ evaluationError }}
        </div>

        <!-- Evaluation Result -->
        <div v-if="aiEvaluation" class="space-y-5 p-5">

            <!-- Match Score and Recommendation -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- Match Score Card -->
                <div class="rounded-2xl border border-purple-100 bg-purple-50/70 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-purple-700">
                        Match Score
                    </p>

                    <div class="mt-3 flex items-end gap-1">
                        <span class="text-4xl font-extrabold tracking-tight text-purple-800">
                            {{ matchScore }}
                        </span>

                        <span class="mb-1 text-xl font-bold text-purple-600">
                            %
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-purple-100">
                        <div class="h-full rounded-full bg-purple-600 transition-all duration-500"
                            :style="{ width: `${matchScore}%` }"></div>
                    </div>

                    <p class="mt-2 text-xs text-purple-600">
                        Applicant's skill and job requirement alignment
                    </p>
                </div>

                <!-- Recommendation Card -->
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        AI Recommendation
                    </p>

                    <div class="mt-4">
                        <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-bold ring-1"
                            :class="recommendationClass">
                            {{ recommendationLabel }}
                        </span>
                    </div>

                    <p class="mt-4 text-xs leading-5 text-slate-500">
                        Based on the applicant's skills, experience, education and application answers.
                    </p>
                </div>
            </div>

            <!-- AI Reasoning -->
            <div class="rounded-2xl border border-purple-100 bg-white p-5">

                <div class="mb-3 flex items-center gap-2">
                    <Sparkles class="h-4 w-4 text-purple-600" />

                    <h4 class="font-bold text-slate-900">
                        AI Reasoning
                    </h4>
                </div>

                <p class="text-sm leading-7 text-slate-600">
                    {{ aiEvaluation.reasoning || "No analysis available." }}
                </p>
            </div>

            <!-- Strengths and Areas to Improve -->
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                <!-- Strengths -->
                <div class="rounded-2xl border border-green-100 bg-green-50/50 p-5">

                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="font-bold text-green-800">
                            Strengths
                        </h4>

                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                            {{ aiStrengths.length }}
                        </span>
                    </div>

                    <ul v-if="aiStrengths.length" class="space-y-3">
                        <li v-for="(item, index) in aiStrengths" :key="index"
                            class="flex gap-2.5 text-sm leading-6 text-slate-700">
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-700">
                                ✓
                            </span>

                            <span>{{ item }}</span>
                        </li>
                    </ul>

                    <p v-else class="text-sm text-slate-400">
                        No strengths identified.
                    </p>
                </div>

                <!-- Areas to Improve -->
                <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-5">

                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="font-bold text-amber-800">
                            Areas to improve
                        </h4>

                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            {{ aiWeaknesses.length }}
                        </span>
                    </div>

                    <ul v-if="aiWeaknesses.length" class="space-y-3">
                        <li v-for="(item, index) in aiWeaknesses" :key="index"
                            class="flex gap-2.5 text-sm leading-6 text-slate-700">
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">
                                !
                            </span>

                            <span>{{ item }}</span>
                        </li>
                    </ul>

                    <p v-else class="text-sm text-slate-400">
                        No improvement areas identified.
                    </p>
                </div>
            </div>

            <!-- Relevant Experience -->
            <div v-if="aiRelevantExperience.length" class="rounded-2xl border border-blue-100 bg-blue-50/40 p-5">
                <h4 class="mb-4 font-bold text-blue-900">
                    Relevant Experience
                </h4>

                <ul class="space-y-3">
                    <li v-for="(item, index) in aiRelevantExperience" :key="index"
                        class="flex gap-2.5 text-sm leading-6 text-slate-700">
                        <span class="mt-0.5 font-bold text-blue-600">
                            ✓
                        </span>

                        <span>{{ item }}</span>
                    </li>
                </ul>
            </div>

            <!-- Skills Assessment -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5">

                <h4 class="mb-5 font-bold text-slate-900">
                    Skills Assessment
                </h4>

                <!-- Matched Skills -->
                <div>
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-green-700">
                            Matched Skills
                        </p>

                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">
                            {{ aiMatchedSkills.length }} Skills
                        </span>
                    </div>

                    <div v-if="aiMatchedSkills.length" class="flex flex-wrap gap-2">
                        <span v-for="(skill, index) in aiMatchedSkills" :key="index"
                            class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                            {{ skill }}
                        </span>
                    </div>

                    <p v-else class="text-sm text-slate-400">
                        No matched skills found.
                    </p>
                </div>

                <!-- Divider -->
                <div class="my-5 border-t border-slate-100"></div>

                <!-- Missing Skills -->
                <div>
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-amber-700">
                            Missing Skills
                        </p>

                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">
                            {{ aiMissingSkills.length }} Skills
                        </span>
                    </div>

                    <div v-if="aiMissingSkills.length" class="flex flex-wrap gap-2">
                        <span v-for="(skill, index) in aiMissingSkills" :key="index"
                            class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                            {{ skill }}
                        </span>
                    </div>

                    <p v-else class="text-sm text-slate-400">
                        No missing skills identified.
                    </p>
                </div>
            </div>
        </div>

        <!-- No Evaluation -->
        <div v-else-if="!evaluating"
            class="m-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100">
                <Sparkles class="h-6 w-6 text-purple-600" />
            </div>

            <h4 class="mt-4 font-bold text-slate-800">
                No AI Evaluation Yet
            </h4>

            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                Run an AI evaluation to analyze this applicant's skills, experience and job compatibility.
            </p>
        </div>

        <!-- Evaluating State -->
        <div v-else class="m-5 rounded-2xl bg-purple-50 px-5 py-10 text-center">
            <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-purple-200 border-t-purple-600"></div>

            <h4 class="mt-4 font-bold text-purple-800">
                AI is evaluating this applicant
            </h4>

            <p class="mt-2 text-sm text-purple-600">
                Please wait while the AI analyzes the applicant's qualifications.
            </p>
        </div>
    </section>
</template>