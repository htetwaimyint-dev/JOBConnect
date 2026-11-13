<script setup lang="ts">
defineProps<{
    loading: boolean;
    errorMessage: string;
}>();

const emit = defineEmits<{
    saveDraft: [];
    submit: [];
    preview: [];
}>();
</script>

<template>
    <div
        class="rounded-md border border-gray-200 bg-white p-6 shadow-xs space-y-6"
    >
        <div class="border-b border-gray-100 pb-4">
            <h2 class="text-base font-bold text-gray-900">Create New Job</h2>
        </div>

        <p class="text-xs text-gray-500 leading-relaxed">
            Complete the job details and save your job as a draft or submit it
            for approval.
        </p>

        <p
            v-if="errorMessage"
            class="rounded-lg bg-red-50 p-3 text-xs text-red-600"
        >
            {{ errorMessage }}
        </p>

        <div class="flex flex-col gap-2.5">
            <button
                type="button"
                :disabled="loading"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                @click="emit('preview')"
            >
                Preview
            </button>

            <button
                type="button"
                :disabled="loading"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                @click="emit('saveDraft')"
            >
                Save as Draft
            </button>

            <button
                type="button"
                :disabled="loading"
                class="w-full rounded-xl bg-purple-600 px-5 py-2.5 text-xs font-semibold text-white hover:bg-purple-700 disabled:opacity-50"
                @click="emit('submit')"
            >
                {{ loading ? "Submitting..." : "Submit Job" }}
            </button>
        </div>
    </div>
</template>
