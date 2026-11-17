<script setup lang="ts">
import { computed } from "vue";
import { sanitizeHtml } from "@/utils/jobs.utils";

const props = defineProps<{
    title: string;
    content?: string | null;
    emptyMessage?: string;
}>();

const safeContent = computed(() => sanitizeHtml(props.content));
</script>

<template>
    <section class="space-y-3 border-t border-gray-100 pt-6">
        <h2 class="text-lg font-bold text-gray-900">
            {{ title }}
        </h2>

        <div
            v-if="safeContent"
            class="job-rich-content text-sm leading-7 text-gray-600"
            v-html="safeContent"
        />

        <p v-else class="text-sm italic text-gray-400">
            {{ emptyMessage || "No information provided." }}
        </p>
    </section>
</template>

<style scoped>
.job-rich-content :deep(p) {
    margin-bottom: 0.75rem;
}

.job-rich-content :deep(h2) {
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
}

.job-rich-content :deep(h3) {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

.job-rich-content :deep(ul) {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
    list-style-type: disc;
}

.job-rich-content :deep(ol) {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
    list-style-type: decimal;
}

.job-rich-content :deep(li) {
    margin-bottom: 0.4rem;
}

.job-rich-content :deep(strong) {
    font-weight: 700;
    color: #374151;
}

.job-rich-content :deep(blockquote) {
    margin: 1rem 0;
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    color: #6b7280;
}
</style>
