<script setup lang="ts">
import DOMPurify from "dompurify";
import type { JobFormData } from "../types/job.types";

import { onMounted, onUnmounted } from "vue";

let previousBodyOverflow = "";

onMounted(() => {
    const scrollbarWidth =
        window.innerWidth - document.documentElement.clientWidth;
    previousBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    document.body.style.paddingRight = `${scrollbarWidth}px`;
});

onUnmounted(() => {
    document.body.style.overflow = previousBodyOverflow;
    document.body.style.paddingRight = "";
});

const props = defineProps<{
    job: JobFormData;
    categoryName?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

function sanitizeHtml(content?: string): string {
    if (!content) return "";

    return DOMPurify.sanitize(content);
}

function formatLabel(value?: string): string {
    if (!value) return "";

    const labels: Record<string, string> = {
        full_time: "Full Time",
        part_time: "Part Time",
        contract: "Contract",
        internship: "Internship",
        onsite: "On-site",
        remote: "Remote",
        hybrid: "Hybrid",
        any: "Any Gender",
        male: "Male",
        female: "Female",
    };

    return labels[value] ?? value;
}

function formatSalary(): string {
    if (props.job.salary_type === "negotiable") {
        return "Negotiable";
    }

    if (props.job.salary_min == null || props.job.salary_max == null) {
        return "Not specified";
    }

    const formatter = new Intl.NumberFormat("en-US");

    return `${formatter.format(Number(props.job.salary_min))} - ${formatter.format(Number(props.job.salary_max))}`;
}
</script>

<template>
    <<!-- 1. Global Fixed Layer (Invisible Container) -->
    <div
        class="fixed inset-0 z-50 flex items-start justify-center px-3 pt-16 sm:px-0"
        @keydown.esc="emit('close')"
    >
        <!-- 2. DARK BACKDROP: Fades in place over the full screen -->
        <Transition
            appear
            enter-active-class="ease-out duration-300 transition-opacity"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200 transition-opacity"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div class="fixed inset-0 bg-black/60" @click="emit('close')" />
        </Transition>

        <!-- 3. MODAL SHEET: Slides up cleanly independently from the bottom -->
        <Transition
            appear
            enter-active-class="ease-out duration-300 transform transition"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="ease-in duration-200 transform transition"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-full opacity-0"
        >
            <div class="relative flex h-[calc(100dvh-4rem)] w-full flex-col">
                <button
                    type="button"
                    class="absolute -top-11 right-2 z-20 rounded-full bg-white/20 p-2 text-white transition hover:bg-white/40 focus:outline-none focus:ring-2 focus:ring-white"
                    aria-label="Close preview"
                    @click="emit('close')"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <!-- Actual Modal -->
                <div
                    class="relative flex min-h-0 w-full flex-1 flex-col overflow-hidden rounded-t-xl bg-[#f7f7fb] shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="job-preview-title"
                >
                    <!-- Preview Content -->
                    <div
                        class="preview-scroll-area min-h-0 flex-1 overflow-y-auto py-8 px-4 sm:px-15"
                    >
                        <div
                            class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12"
                        >
                            <!-- Main Job Details -->
                            <div class="space-y-3 shadow-xs lg:col-span-8">
                                <!-- Job Heading & Action Bar -->
                                <div
                                    class="border-b border-gray-100 pb-6 space-y-4"
                                >
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-0"
                                    >
                                        <div>
                                            <h1
                                                id="job-preview-title"
                                                class="font-bold text-gray-900 text-xl"
                                            >
                                                {{
                                                    job.title ||
                                                    "Your Job Title"
                                                }}
                                            </h1>
                                        </div>

                                        <!-- <div
                                        class="flex items-center gap-2 shrink-0"
                                    >
                                        <button
                                            type="button"
                                            class="p-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                                            title="Bookmark"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="p-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                                            title="Share"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition"
                                        >
                                            Apply Now
                                        </button>
                                    </div> -->
                                    </div>

                                    <!-- Company & Location Info Line -->
                                    <div class="flex items-center gap-4 pt-2">
                                        <div
                                            class="h-12 w-12 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold text-lg shrink-0"
                                        >
                                            {{
                                                (
                                                    job.title?.[0] || "C"
                                                ).toUpperCase()
                                            }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-bold text-gray-900"
                                            >
                                                Company Name •
                                                <span
                                                    class="font-normal text-gray-500"
                                                    >{{
                                                        [job.city, job.country]
                                                            .filter(Boolean)
                                                            .join(", ") ||
                                                        "Location"
                                                    }}</span
                                                >
                                            </p>
                                            <div
                                                class="mt-2 flex flex-wrap gap-2"
                                            >
                                                <span
                                                    class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                                >
                                                    {{
                                                        formatLabel(
                                                            job.employment_type,
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                                >
                                                    {{
                                                        formatLabel(
                                                            job.workplace_type,
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                                >
                                                    {{ job.vacancies || 1 }}
                                                    Position(s)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Overview -->
                                <section class="space-y-4">
                                    <h3
                                        class="text-base font-bold text-gray-900"
                                    >
                                        Job Overview
                                    </h3>

                                    <div
                                        class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="rounded-lg bg-blue-50 p-2 text-blue-600"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Employment Type
                                                </p>
                                                <p
                                                    class="mt-1 text-sm font-semibold text-gray-900"
                                                >
                                                    {{
                                                        formatLabel(
                                                            job.employment_type,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="rounded-lg bg-purple-50 p-2 text-purple-600"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.196-2.045M17 20H7m10 0v-2c0-.728-.195-1.411-.536-2M7 20H2v-2a3 3 0 015.196-2.045M7 20v-2c0-.728.195-1.411.536-2m0 0a5.002 5.002 0 019.928 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Available Positions
                                                </p>
                                                <p
                                                    class="mt-1 text-sm font-semibold text-gray-900"
                                                >
                                                    {{ job.vacancies || 1 }}
                                                    Position(s)
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="rounded-lg bg-green-50 p-2 text-green-600"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Salary
                                                </p>
                                                <p
                                                    class="mt-1 text-sm font-semibold text-gray-900"
                                                >
                                                    {{ formatSalary() }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="rounded-lg bg-orange-50 p-2 text-orange-600"
                                            >
                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Gender Preference
                                                </p>
                                                <p
                                                    class="mt-1 text-sm font-semibold text-gray-900"
                                                >
                                                    {{
                                                        formatLabel(
                                                            job.gender,
                                                        ) || "Any Gender"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="job.deadline"
                                        class="rounded-lg bg-gray-50 p-3 text-sm text-gray-600"
                                    >
                                        Application deadline:
                                        <strong class="text-gray-900">
                                            {{ job.deadline }}
                                        </strong>
                                    </div>
                                </section>

                                <!-- Description -->
                                <section
                                    class="space-y-3 border-t border-gray-100 pt-6"
                                >
                                    <h3
                                        class="text-base font-bold text-gray-900"
                                    >
                                        About This Role
                                    </h3>

                                    <div
                                        v-if="job.description"
                                        class="job-rich-content text-sm leading-7 text-gray-600"
                                        v-html="sanitizeHtml(job.description)"
                                    />

                                    <p
                                        v-else
                                        class="text-sm italic text-gray-400"
                                    >
                                        Your job description will appear here.
                                    </p>
                                </section>

                                <!-- Requirements -->
                                <section
                                    class="space-y-3 border-t border-gray-100 pt-6"
                                >
                                    <h3
                                        class="text-base font-bold text-gray-900"
                                    >
                                        Requirements
                                    </h3>

                                    <div
                                        v-if="job.requirements"
                                        class="job-rich-content text-sm leading-7 text-gray-600"
                                        v-html="sanitizeHtml(job.requirements)"
                                    />

                                    <p
                                        v-else
                                        class="text-sm italic text-gray-400"
                                    >
                                        No requirements added yet.
                                    </p>
                                </section>

                                <!-- Responsibilities -->
                                <section
                                    class="space-y-3 border-t border-gray-100 pt-6"
                                >
                                    <h3
                                        class="text-base font-bold text-gray-900"
                                    >
                                        Responsibilities
                                    </h3>

                                    <div
                                        v-if="job.responsibilities"
                                        class="job-rich-content text-sm leading-7 text-gray-600"
                                        v-html="
                                            sanitizeHtml(job.responsibilities)
                                        "
                                    />

                                    <p
                                        v-else
                                        class="text-sm italic text-gray-400"
                                    >
                                        No responsibilities added yet.
                                    </p>
                                </section>
                            </div>

                            <!-- Right Sidebar -->
                            <aside class="space-y-5 lg:col-span-4">
                                <div
                                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-xs"
                                >
                                    <h3
                                        class="text-base font-bold text-gray-900"
                                    >
                                        Job Summary
                                    </h3>

                                    <div class="mt-5 space-y-4">
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Job Title
                                            </p>
                                            <p
                                                class="mt-1 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    job.title || "Not specified"
                                                }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Category
                                            </p>
                                            <p
                                                class="mt-1 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    categoryName ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Workplace
                                            </p>
                                            <p
                                                class="mt-1 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    formatLabel(
                                                        job.workplace_type,
                                                    ) || "Not specified"
                                                }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Location
                                            </p>
                                            <p
                                                class="mt-1 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    [job.city, job.country]
                                                        .filter(Boolean)
                                                        .join(", ") ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Application Deadline
                                            </p>
                                            <p
                                                class="mt-1 text-sm font-semibold text-gray-900"
                                            >
                                                {{
                                                    job.deadline ||
                                                    "Not specified"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="rounded-xl border border-blue-100 bg-blue-50 p-5"
                                >
                                    <h3 class="text-sm font-bold text-blue-900">
                                        Preview Mode
                                    </h3>
                                    <p
                                        class="mt-2 text-xs leading-5 text-blue-700"
                                    >
                                        This is a preview of your job listing.
                                        Your changes will not be published until
                                        you submit the job for approval.
                                    </p>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
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

.job-rich-content :deep(em) {
    font-style: italic;
}

.job-rich-content :deep(u) {
    text-decoration: underline;
}

.job-rich-content :deep(blockquote) {
    margin: 1rem 0;
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    color: #6b7280;
}

/* Hide scrollbar while keeping scrolling enabled */
.preview-scroll-area {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.preview-scroll-area::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}
</style>
