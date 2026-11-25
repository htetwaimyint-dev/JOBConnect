<template>
    <div class="flex flex-col items-center justify-center min-h-[300px] text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 mb-4">
            <CalendarDays :size="32" class="text-indigo-500" />
        </div>
        <h2 class="text-lg font-bold text-slate-900">No interviews yet</h2>
        <p class="mt-2 text-sm text-slate-500 max-w-xs">
            You don't have any scheduled interviews. Once an employer schedules
            an interview for one of your applications, it will appear here.
        </p>
        <div class="mt-6">
            <RouterLink
                to="/jobseeker/jobs"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition"
            >
                <ArrowRight :size="16" />
                Browse jobs
            </RouterLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
import { CalendarDays, ArrowRight } from "lucide-vue-next";
</script>