<script setup lang="ts">
import { ref, watch } from "vue";
import type {
    Interviewer,
    InterviewerFormData,
} from "../types/interview.types";

const props = defineProps<{
    open: boolean;
    interviewers: Interviewer[];
    saving?: boolean;
}>();

const emit = defineEmits<{
    (event: "close"): void;
    (event: "create", data: InterviewerFormData): void;
    (event: "update", id: number, data: InterviewerFormData): void;
    (event: "delete", id: number): void;
}>();

const name = ref("");
const position = ref("");
const editingId = ref<number | null>(null);

watch(
    () => props.open,
    (open) => {
        if (open) reset();
    },
);

function reset() {
    name.value = "";
    position.value = "";
    editingId.value = null;
}

function edit(person: Interviewer) {
    editingId.value = person.id;
    name.value = person.name;
    position.value = person.position;
}

function submit() {
    const data = {
        name: name.value.trim(),
        position: position.value.trim(),
    };

    if (!data.name || !data.position) return;

    if (editingId.value !== null) {
        emit("update", editingId.value, data);
    } else {
        emit("create", data);
    }

    reset();
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 p-4"
            @click.self="emit('close')"
        >
            <section class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Manage Interviewers
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Reuse interviewers across multiple interviews.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100"
                        aria-label="Close"
                        @click="emit('close')"
                    >
                        ✕
                    </button>
                </div>

                <form class="mt-5 space-y-3" @submit.prevent="submit">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Name
                        </label>
                        <input
                            v-model="name"
                            required
                            maxlength="255"
                            placeholder="Enter interviewer name"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Position
                        </label>
                        <input
                            v-model="position"
                            required
                            maxlength="255"
                            placeholder="e.g. HR Manager"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            v-if="editingId !== null"
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm text-gray-600 hover:bg-gray-100"
                            @click="reset"
                        >
                            Cancel edit
                        </button>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                editingId !== null
                                    ? "Save changes"
                                    : "Add interviewer"
                            }}
                        </button>
                    </div>
                </form>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800">
                        Existing Interviewers
                    </h3>

                    <div
                        v-if="interviewers.length"
                        class="max-h-56 space-y-2 overflow-y-auto"
                    >
                        <div
                            v-for="person in interviewers"
                            :key="person.id"
                            class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 p-3"
                        >
                            <div class="min-w-0">
                                <p
                                    class="truncate text-sm font-medium text-gray-800"
                                >
                                    {{ person.name }}
                                </p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ person.position }}
                                </p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <button
                                    type="button"
                                    class="text-xs font-medium text-indigo-600"
                                    @click="edit(person)"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="text-xs font-medium text-red-600"
                                    @click="emit('delete', person.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <p v-else class="text-sm text-gray-500">
                        No interviewers yet.
                    </p>
                </div>
            </section>
        </div>
    </Teleport>
</template>
