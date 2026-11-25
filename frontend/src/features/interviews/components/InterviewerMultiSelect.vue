<script setup lang="ts">
import type { Interviewer } from "../types/interview.types";

defineProps<{
    interviewers: Interviewer[];
    modelValue: number[];
}>();

const emit = defineEmits<{
    (event: "update:modelValue", value: number[]): void;
    (event: "manage"): void;
}>();

function toggle(id: number, selected: number[]) {
    const next = selected.includes(id)
        ? selected.filter((item) => item !== id)
        : [...selected, id];

    emit("update:modelValue", next);
}
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700">
                Interviewers
            </label>
            <button
                type="button"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-700"
                @click="emit('manage')"
            >
                Manage interviewers
            </button>
        </div>

        <div v-if="interviewers.length" class="grid gap-2 sm:grid-cols-2">
            <label
                v-for="person in interviewers"
                :key="person.id"
                class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition"
                :class="
                    modelValue.includes(person.id)
                        ? 'border-indigo-300 bg-indigo-50'
                        : 'border-gray-200 hover:bg-gray-50'
                "
            >
                <input
                    type="checkbox"
                    :checked="modelValue.includes(person.id)"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    @change="toggle(person.id, modelValue)"
                />
                <span class="min-w-0">
                    <span
                        class="block truncate text-sm font-medium text-gray-800"
                    >
                        {{ person.name }}
                    </span>
                    <span class="block truncate text-xs text-gray-500">
                        {{ person.position }}
                    </span>
                </span>
            </label>
        </div>

        <p v-else class="text-sm text-gray-500">No interviewers added yet.</p>
    </div>
</template>
