<script setup lang="ts">
import BaseCheckbox from "@/components/common/BaseCheckbox.vue";
import type { JobApplicationQuestionForm } from "../types/job.types";

const questions = defineModel<JobApplicationQuestionForm[]>({
    required: true,
});

function addQuestion() {
    questions.value.push({
        question: "",
        is_required: true,
        sort_order: questions.value.length,
    });
}

function removeQuestion(index: number) {
    questions.value.splice(index, 1);

    updateSortOrder();
}

function updateSortOrder() {
    questions.value.forEach((question, index) => {
        question.sort_order = index;
    });
}
</script>

<template>
    <section class="rounded-xl border border-gray-200 bg-white p-8 space-y-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">
                    Application Questions
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Add questions applicants must answer when applying for this
                    job.
                </p>
            </div>

            <span class="text-sm text-gray-500">
                {{ questions.length }} questions
            </span>
        </div>

        <div
            v-if="questions.length === 0"
            class="rounded-lg border-2 border-dashed border-gray-200 p-8 text-center"
        >
            <div class="text-gray-400 mb-2">
                <span class="text-3xl">?</span>
            </div>

            <p class="text-sm font-medium text-gray-700">
                No application questions yet
            </p>
        </div>

        <div
            v-for="(item, index) in questions"
            :key="index"
            class="rounded-lg border border-gray-200 p-4 space-y-4"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">
                    Question {{ index + 1 }}
                </span>

                <button
                    type="button"
                    @click="removeQuestion(index)"
                    class="text-sm text-red-500 hover:text-red-700"
                >
                    Remove
                </button>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">
                        Your question
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <BaseCheckbox v-model="item.is_required"
                            ><span class="-ml-1 text-[12px]! text-gray-600!">
                                Required question
                            </span></BaseCheckbox
                        >
                    </label>
                </div>

                <textarea
                    v-model="item.question"
                    rows="3"
                    maxlength="2000"
                    placeholder="e.g. Why are you interested in this position?"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                ></textarea>

                <p class="mt-1 text-right text-xs text-gray-400">
                    {{ item.question.length }}/2000
                </p>
            </div>
        </div>

        <button
            type="button"
            @click="addQuestion"
            class="w-full rounded-lg border border-dashed border-indigo-300 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition"
        >
            + Add a question
        </button>

        <p class="text-xs text-gray-500">
            Applicants will see these questions when submitting their
            application.
        </p>
    </section>
</template>
