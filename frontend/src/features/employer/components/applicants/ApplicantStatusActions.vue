<script setup lang="ts">
import type { ApplicationStatus } from "@/features/applications/types/employer-application.types";

type NextApplicationStatus = Exclude<
    ApplicationStatus,
    "pending" | "withdrawn"
>;

defineProps<{
    nextStatusOptions: NextApplicationStatus[];
    updatingStatus: boolean;
    errorMessage: string | null;
}>();

const statusNote = defineModel<string>("statusNote", {
    required: true,
});

const emit = defineEmits<{
    "change-status": [status: NextApplicationStatus];
}>();

const formatLabel = (value: string): string => {
    return value
        .replace(/_/g, " ")
        .replace(/\b\w/g, (char) => char.toUpperCase());
};
</script>

<template>
    <section class="rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-900">
            Update application status
        </h3>

        <p v-if="nextStatusOptions.length === 0" class="mt-2 text-sm text-slate-500">
            No further status actions are available for this application.
        </p>

        <template v-else>
            <label class="mb-2 mt-4 block text-sm font-medium text-slate-700">
                Note (optional)
            </label>

            <textarea v-model="statusNote" rows="3" placeholder="Write a note about this status change..."
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"></textarea>

            <div class="mt-4 flex flex-wrap gap-2">
                <button v-for="status in nextStatusOptions" :key="status" type="button" :disabled="updatingStatus"
                    @click="emit('change-status', status)"
                    class="rounded-xl px-4 py-2.5 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                    :class="status === 'rejected'
                            ? 'border border-red-200 bg-white text-red-700 hover:bg-red-50'
                            : 'bg-[#102D59] text-white hover:bg-blue-900'
                        ">
                    {{
                        updatingStatus
                            ? "Updating..."
                            : `Mark as ${formatLabel(status)}`
                    }}
                </button>
            </div>
        </template>

        <p v-if="errorMessage" class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            {{ errorMessage }}
        </p>
    </section>
</template>
