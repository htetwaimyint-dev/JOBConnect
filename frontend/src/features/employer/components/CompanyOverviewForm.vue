<script setup lang="ts">
import { ref } from "vue";
import type { CompanyForm } from "../types/company-form.types";
import CompanyOverviewCard from "./CompanyOverviewCard.vue";
import CompanyOverviewPreview from "./CompanyOverviewPreview.vue";

const form = defineModel<CompanyForm>("form", { required: true });

// Ensure array exists with defaults
if (!form.value.sections) {
    form.value.sections = [
        {
            id: "1",
            title: "Company Vision and Mission",
            content:
                "Our Vision is to continue to achieve global growth by providing diverse value unique to JTI.\nOur Mission is to nurture our internationally recognized brand roster while leading the way in Reduced-Risk Products and new ways of doing business.",
            icon: "lightbulb",
        },
        {
            id: "2",
            title: "What we do",
            content:
                "CDSG is one of Myanmar's most respected and prominent group of companies, tracing its roots back to a small trading business started in the 1960s.",
            icon: "briefcase",
        },
    ];
}

const isPreviewMode = ref(false);

const availableIcons = [
    {
        id: "lightbulb",
        label: "Vision",
        svg: "M12 2a7 7 0 00-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 001 1h6a1 1 0 001-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 00-7-7z",
    },
    {
        id: "briefcase",
        label: "Work",
        svg: "M20 7h-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z",
    },
    {
        id: "star",
        label: "Perks",
        svg: "M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z",
    },
    {
        id: "building",
        label: "Culture",
        svg: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12",
    },
    {
        id: "target",
        label: "Goals",
        svg: "M12 2a10 10 0 100 20 10 10 0 000-20zm0 14a4 4 0 110-8 4 4 0 010 8z",
    },
    {
        id: "users",
        label: "Team",
        svg: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
    },
];

function addSection() {
    form.value.sections.push({
        id: Date.now().toString(),
        title: "",
        content: "",
        icon: "lightbulb",
    });
}

function removeSection(index: number) {
    form.value.sections.splice(index, 1);
}

function moveSection(index: number, direction: "up" | "down") {
    const targetIndex = direction === "up" ? index - 1 : index + 1;
    if (targetIndex < 0 || targetIndex >= form.value.sections.length) return;

    if (form.value.sections[targetIndex] && form.value.sections[index])
        [form.value.sections[index], form.value.sections[targetIndex]] = [
            form.value.sections[targetIndex],
            form.value.sections[index],
        ];
}
</script>

<template>
    <section
        class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm"
    >
        <!-- Section Header -->
        <div
            class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 gap-4"
        >
            <div>
                <h4
                    class="text-lg font-bold text-slate-900 flex items-center gap-2.5"
                >
                    <span
                        class="h-2.5 w-2.5 rounded-full bg-linear-to-r from-blue-600 to-indigo-600 shadow-xs shadow-blue-500/50"
                    ></span>
                    About Us
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">
                    Organize your company vision, mission, and culture.
                </p>
            </div>

            <!-- Mode Toggle -->
            <div
                class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 self-start sm:self-auto shadow-inner"
            >
                <button
                    type="button"
                    @click="isPreviewMode = false"
                    :class="[
                        !isPreviewMode
                            ? 'bg-linear-to-r font-bold'
                            : 'text-slate-400 hover:text-slate-900 font-medium',
                        'px-3.5 py-1.5 text-[13px]! rounded-lg transition-all',
                    ]"
                >
                    Edit Cards
                </button>
                <button
                    type="button"
                    @click="isPreviewMode = true"
                    :class="[
                        isPreviewMode
                            ? 'bg-linear-to-r font-bold'
                            : 'text-slate-400 hover:text-slate-900 font-medium',
                        'px-3.5 py-1.5 text-[13px]! rounded-lg transition-all',
                    ]"
                >
                    Preview
                </button>
            </div>
        </div>

        <!-- PREVIEW MODE: Uses new dedicated component matching the reference image -->
        <CompanyOverviewPreview
            v-if="isPreviewMode"
            :sections="form.sections"
            :available-icons="availableIcons"
        />

        <!-- EDIT MODE: Compact Cards List -->
        <div v-else class="space-y-4">
            <CompanyOverviewCard
                v-for="(section, index) in form.sections"
                :key="section.id"
                :section="section"
                :index="index"
                :is-first="index === 0"
                :is-last="index === form.sections.length - 1"
                :available-icons="availableIcons"
                @remove="removeSection(index)"
                @move-up="moveSection(index, 'up')"
                @move-down="moveSection(index, 'down')"
            />

            <!-- Vibrant Add Card Button -->
            <button
                type="button"
                @click="addSection"
                class="w-full rounded-xl border-2 border-dashed border-blue-300 bg-linear-to-r from-blue-50/50 via-indigo-50/30 to-blue-50/50 py-3.5 text-xs font-bold text-blue-700 hover:border-blue-500 hover:bg-blue-100/50 hover:text-blue-900 transition flex items-center justify-center gap-2 shadow-2xs group"
            >
                <span
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-black group-hover:scale-110 transition-transform"
                    >+</span
                >
                Add Story Section Card
            </button>
        </div>
    </section>
</template>
