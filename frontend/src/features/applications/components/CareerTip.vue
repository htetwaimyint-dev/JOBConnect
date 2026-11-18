<script setup lang="ts">
import { RouterLink } from "vue-router";
import { ArrowUpRight, Sparkles } from "lucide-vue-next";
</script>

<template>
    <div class="rounded-2xl border border-cyan-100 bg-gradient-to-br from-cyan-50 to-blue-50 p-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm">
            <Sparkles class="h-5 w-5" />
        </div>

        <h3 class="mt-4 font-bold text-[#102D59]">
            Career Tip
        </h3>

        <p class="mt-2 text-sm leading-6 text-slate-600">
            Keep your profile and resume up to date to make a
            strong impression on employers.
        </p>

        <RouterLink to="/jobseeker/profile"
            class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-blue-700 transition hover:text-blue-900">
            Update Your Profile
            <ArrowUpRight class="h-4 w-4" />
        </RouterLink>
    </div>
</template>
