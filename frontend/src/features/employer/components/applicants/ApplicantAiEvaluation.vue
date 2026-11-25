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

const aiStrengths = computed(() => {
    const value = aiEvaluation.value?.strengths;

    return Array.isArray(value) ? value : [];
});

const aiWeaknesses = computed(() => {
    const value = aiEvaluation.value?.weaknesses;

    return Array.isArray(value) ? value : [];
});

const aiMatchedSkills = computed(() => {
    const value = aiEvaluation.value?.matched_skills;

    return Array.isArray(value) ? value : [];
});

const aiMissingSkills = computed(() => {
    const value = aiEvaluation.value?.missing_skills;

    return Array.isArray(value) ? value : [];
});

const formatLabel = (value: string): string =>
    value.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
</script>

<template>
    <section class="rounded-2xl border border-purple-100 p-5">
        <!-- Header and Button -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="flex items-center gap-2 font-bold text-slate-900">
                <Sparkles class="h-4 w-4 text-purple-600" />
                AI Evaluation
            </h3>

            <button v-if="!aiEvaluation" type="button" :disabled="evaluating || !canEvaluate"
                @click="emit('run-evaluation')"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-50">
                <Sparkles v-if="!evaluating" class="h-4 w-4" />

                <span v-else
                    class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>

                {{ evaluating ? "Evaluating..." : "Run AI Evaluation" }}
            </button>
        </div>

        <p v-if="!canEvaluate && !aiEvaluation" class="mb-3 text-xs text-amber-600">
            AI evaluation is available for pending or reviewed applications only.
        </p>

        <p v-if="evaluationError" class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-600">
            {{ evaluationError }}
        </p>

        <!-- AI Evaluation Result -->
        <div v-if="aiEvaluation" class="space-y-4 rounded-xl bg-purple-50/50 p-4">
            <!-- Match Score -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-purple-700">
                    Match Score
                </p>

                <p class="mt-1 text-3xl font-bold text-purple-800">
                    {{ aiEvaluation.match_score ?? "N/A" }}{{ aiEvaluation.match_score != null ? "%" : "" }}
                </p>
            </div>

            <!-- Recommendation -->
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    Recommendation
                </p>

                <p class="mt-1 text-sm text-purple-700">
                    {{
                        formatLabel(
                            String(aiEvaluation.recommendation || "Not available"),
                        )
                    }}
                </p>
            </div>

            <!-- Reasoning -->
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    Reasoning
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    {{ aiEvaluation.reasoning || "No analysis available." }}
                </p>
            </div>

            <!-- Strengths -->
            <div v-if="aiStrengths.length">
                <p class="text-sm font-semibold text-slate-800">
                    Strengths
                </p>

                <ul class="mt-2 space-y-2">
                    <li v-for="(item, index) in aiStrengths" :key="index" class="flex gap-2 text-sm text-slate-600">
                        <span class="text-green-600">✓</span>
                        {{ item }}
                    </li>
                </ul>
            </div>

            <!-- Weaknesses -->
            <div v-if="aiWeaknesses.length">
                <p class="text-sm font-semibold text-slate-800">
                    Areas to improve
                </p>

                <ul class="mt-2 space-y-2">
                    <li v-for="(item, index) in aiWeaknesses" :key="index" class="flex gap-2 text-sm text-slate-600">
                        <span class="text-amber-600">•</span>
                        {{ item }}
                    </li>
                </ul>
            </div>

            <!-- Matched Skills -->
            <div v-if="aiMatchedSkills.length">
                <p class="text-sm font-semibold text-slate-800">
                    Matched Skills
                </p>

                <div class="mt-2 flex flex-wrap gap-2">
                    <span v-for="(skill, index) in aiMatchedSkills" :key="index"
                        class="rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-purple-700">
                        {{ skill }}
                    </span>
                </div>
            </div>

            <!-- Missing Skills -->
            <div v-if="aiMissingSkills.length">
                <p class="text-sm font-semibold text-slate-800">
                    Missing Skills
                </p>

                <div class="mt-2 flex flex-wrap gap-2">
                    <span v-for="(skill, index) in aiMissingSkills" :key="index"
                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                        {{ skill }}
                    </span>
                </div>
            </div>
        </div>

        <p v-else class="rounded-xl bg-slate-50 p-4 text-sm text-slate-400">
            No AI evaluation available. Click "Run AI Evaluation" to evaluate this applicant.
        </p>
    </section>
</template>
