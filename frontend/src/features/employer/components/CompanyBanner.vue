<script setup lang="ts">
import type { Company } from "../types/company.types";

withDefaults(
    defineProps<{
        company: Company;
        isFollowing?: boolean;
    }>(),
    {
        isFollowing: false,
    },
);

const emit = defineEmits<{
    follow: [];
    viewJobs: [];
}>();
</script>

<template>
    <section class="relative overflow-hidden bg-gray-900 pt-10 pb-10">
        <!-- Background image -->
        <div
            class="absolute inset-0 bg-cover bg-center"
            :style="{
                backgroundImage: `url(${company.cover_image})`,
            }"
        ></div>

        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-linear-to-r from-violet-950/95 via-violet-900/85 to-violet-800/50"
        ></div>

        <!-- Decorative shapes -->
        <div
            class="absolute -right-20 -top-32 h-72 w-72 rounded-full border border-white/10"
        ></div>
        <div
            class="absolute -right-8 -top-20 h-52 w-52 rounded-full border border-white/10"
        ></div>

        <!-- Content -->
        <div
            class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:px-8 sm:py-7"
        >
            <!-- Company Profile -->
            <div class="flex min-w-0 items-center gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2
                            class="text-xl font-bold text-white sm:text-xl mb-3"
                        >
                            {{ company.name }}
                        </h2>

                        <!-- <span
                            v-if="company.isVerified"
                            class="inline-flex items-center gap-1 rounded-full bg-white/15 px-2 py-1 text-[10px] font-medium text-white"
                        >
                            <span class="text-green-300">✓</span>
                            Verified
                        </span> -->
                    </div>

                    <p class="line-clamp-2 text-xs text-violet-100">
                        {{ company.tagline }}
                    </p>

                    <div
                        class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-violet-100/90"
                    >
                        <span class="flex items-center gap-1.5">
                            <span>⌖</span>
                            {{ company.address }}
                        </span>

                        <span class="flex items-center gap-1.5">
                            <span>♙</span>
                            {{ company.employee_count || 0 }} employees
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <!-- <div class="flex shrink-0 items-center gap-3 sm:pl-4">
                <button
                    type="button"
                    class="rounded-lg border border-white/40 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20"
                    @click="emit('follow')"
                >
                    {{ isFollowing ? "Following ✓" : "+ Follow" }}
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-violet-700 shadow-sm transition hover:bg-violet-50"
                    @click="emit('viewJobs')"
                >
                    View Jobs
                    <span class="ml-1">→</span>
                </button>
            </div> -->
        </div>
    </section>
</template>
