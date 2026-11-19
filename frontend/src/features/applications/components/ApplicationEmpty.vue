<script setup lang="ts">
import { computed } from "vue";
import { RouterLink } from "vue-router";
import { Inbox, Search, ArrowUpRight } from "lucide-vue-next";

const props = defineProps<{
    hasApplications: boolean;
}>();

const isEmpty = computed(() => !props.hasApplications);
</script>

<template>
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
            <Inbox class="h-8 w-8" />
        </div>

        <h3 class="mt-6 text-xl font-bold text-slate-900">
            {{
                isEmpty
                    ? "No Applications Yet"
                    : "No Matching Applications"
            }}
        </h3>

        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            {{
                isEmpty
                    ? `You haven't applied for any jobs yet. Explore available opportunities and take the next step in your
            career.`
                    : `We couldn't find any applications matching your search. Try another keyword or clear your filters.`
            }}
        </p>

        <RouterLink v-if="isEmpty" to="/jobseeker/jobs"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-800">
            <Search class="h-4 w-4" />
            Find Jobs
            <ArrowUpRight class="h-4 w-4" />
        </RouterLink>
    </div>
</template>
