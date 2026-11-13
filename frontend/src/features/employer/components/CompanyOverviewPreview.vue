<script setup lang="ts">
import type { CompanyOverviewSection } from "../types/company-form.types";

defineProps<{
    sections: CompanyOverviewSection[];
    availableIcons: Array<{ id: string; label: string; svg: string }>;
}>();

function getIconPath(
    iconId: string,
    availableIcons: Array<{ id: string; label: string; svg: string }>,
) {
    return (
        availableIcons.find((i: any) => i.id === iconId)?.svg ||
        availableIcons[0]?.svg ||
        "M12 2a7 7 0 00-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 001 1h6a1 1 0 001-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 00-7-7z"
    );
}
</script>

<template>
    <div class="relative pl-1 space-y-3">
        <div
            v-for="section in sections"
            :key="`preview-${section.id}`"
            class="relative flex items-start group"
        >
            <div class="flex">
                <div class="relative flex flex-col items-center">
                    <!-- Overlapping Icon at the Top -->
                    <div
                        class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-xs"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                :d="
                                    getIconPath(
                                        section.icon ?? '',
                                        availableIcons,
                                    )
                                "
                            />
                        </svg>
                    </div>

                    <!-- Dynamic Line: Stretches automatically to the bottom of the text -->
                    <div class="w-0.5 flex-1 bg-slate-200 -mt-2"></div>
                </div>

                <div class="flex-1 pt-1">
                    <h6
                        class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2"
                    >
                        <span class="-ml-1 text-2xl text-slate-400 font-normal"
                            >▸</span
                        >
                        {{ section.title || "Untitled Section" }}
                    </h6>

                    <p
                        class="ml-6 mt-4 text-xs text-slate-600 leading-relaxed whitespace-pre-line"
                    >
                        {{ section.content || "No description provided." }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="!sections.length"
            class="text-center py-8 text-xs text-slate-400"
        >
            No story sections available to preview.
        </div>
    </div>
</template>
