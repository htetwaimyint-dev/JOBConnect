<script setup lang="ts">
import { History } from "lucide-vue-next";

import type {
    EmployerApplication,
} from "@/features/applications/types/employer-application.types";

defineProps<{
    histories: NonNullable<EmployerApplication["status_histories"]>;
    formatDate: (value: string | null | undefined) => string;
}>();

const formatLabel = (value: string): string =>
    value.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
</script>

<template>
    <section>
        <h3 class="mb-3 flex items-center gap-2 font-bold text-slate-900">
            <History class="h-4 w-4 text-blue-700" />
            Status history
        </h3>

        <div v-if="histories.length" class="space-y-4">
            <div v-for="history in histories" :key="history.id" class="border-l-2 border-blue-200 pl-4">
                <p class="text-sm font-semibold text-slate-800">
                    {{ formatLabel(history.old_status || "New") }}

                    <span class="mx-1 text-slate-400">→</span>

                    {{ formatLabel(history.new_status) }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ formatDate(history.created_at) }}
                </p>

                <p v-if="history.note" class="mt-1 text-sm text-slate-600">
                    {{ history.note }}
                </p>
            </div>
        </div>

        <p v-else class="text-sm text-slate-400">
            No status history available.
        </p>
    </section>
</template>
