<script setup lang="ts">
import { ref } from "vue";
import BaseInput from "@/components/common/BaseInput.vue";
import BaseSelect from "@/components/common/BaseSelect.vue";

// Form reactive state
const form = ref({
    jobTitle: "",
    department: "",
    closingDate: "",
    employmentType: "",
    availablePosition: "",
    workplaceType: "On-Site",
    country: "",
    city: "",
    preferNotToSaySalary: false,
    minSalary: "",
    maxSalary: "",
    currency: "USD",
    paymentType: "Weekly",
    curriculum: "",
    description: "",
    candidateGender: "Male",
    nationality: "Arab Saudi",
    marriageSituation: "Single",
    yearOfExperience: "4 Years",
});

// Dropdown options
const jobTitleOptions = [
    { label: "Software Engineer", value: "software_engineer" },
    { label: "Math Teacher", value: "math_teacher" },
    { label: "Product Manager", value: "product_manager" },
];

const departmentOptions = [
    { label: "Engineering", value: "engineering" },
    { label: "Academics", value: "academics" },
    { label: "Product", value: "product" },
];

const employmentTypeOptions = [
    { label: "Full-time", value: "full_time" },
    { label: "Part-time", value: "part_time" },
    { label: "Contract", value: "contract" },
];

const currencyOptions = [
    { label: "USD", value: "USD" },
    { label: "EUR", value: "EUR" },
    { label: "GBP", value: "GBP" },
];

const curriculumOptions = [
    { label: "American Curriculum", value: "american" },
    { label: "British Curriculum", value: "british" },
    { label: "International Baccalaureate (IB)", value: "ib" },
];

const genderOptions = [
    { label: "Male", value: "Male" },
    { label: "Female", value: "Female" },
    { label: "Any", value: "Any" },
];

const nationalityOptions = [
    { label: "Arab Saudi", value: "Arab Saudi" },
    { label: "United Arab Emirates", value: "UAE" },
    { label: "Any Nationality", value: "Any" },
];

// Methods
function handleGenerateAI() {
    form.value.description =
        "As a Math Teacher, you will play a vital role in shaping students' critical thinking and problem-solving skills through the delivery of well-structured and engaging mathematics instruction. Your primary responsibility is to plan, develop, and implement lessons that align with the school's academic standards—whether based on the national curriculum or an international.";
}

function handleSaveDraft() {
    console.log("Saving draft...", form.value);
}

function handleNext() {
    console.log("Proceeding to next step...", form.value);
}
</script>

<template>
    <div class="min-h-screen bg-gray-50/60 pb-20 font-sans text-slate-900">
        <!-- Top Navigation Header Bar -->
        <header
            class="sticky top-0 z-30 border-b border-gray-200 bg-white shadow-2xs"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
            >
                <div class="flex items-center gap-4">
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 transition"
                        aria-label="Close"
                    >
                        ✕
                    </button>
                    <h1 class="text-lg font-bold tracking-tight text-gray-900">
                        Create New Jobs
                    </h1>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 transition"
                    >
                        Preview
                    </button>
                    <button
                        type="button"
                        @click="handleSaveDraft"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 transition"
                    >
                        Save as Draft
                    </button>
                    <button
                        type="button"
                        @click="handleNext"
                        class="rounded-xl bg-purple-600 px-5 py-2 text-xs font-semibold text-white shadow-md hover:bg-purple-700 transition"
                    >
                        Next
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Body Layout -->
        <div class="mx-auto max-w-7xl px-6 pt-8">
            <!-- Right Form Content (Span 8) -->
            <main class="lg:col-span-8 space-y-6">
                <!-- Card 1: Job Detail -->
                <section
                    class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
                >
                    <h2
                        class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
                    >
                        Job Detail
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <BaseSelect
                            v-model="form.jobTitle"
                            label="Job Title"
                            placeholder="Select Job Title"
                            :options="jobTitleOptions"
                        />
                        <BaseSelect
                            v-model="form.department"
                            label="Department"
                            placeholder="Select Department"
                            :options="departmentOptions"
                        />
                        <BaseInput
                            v-model="form.closingDate"
                            label="Job Vacancy Closing Date"
                            type="date"
                            :options="[]"
                        />
                        <BaseSelect
                            v-model="form.employmentType"
                            label="Employment Type"
                            placeholder="Select Type"
                            :options="employmentTypeOptions"
                        />
                    </div>

                    <div>
                        <BaseInput
                            v-model="form.availablePosition"
                            label="Job Available Position"
                            placeholder="Enter position name"
                        />
                    </div>

                    <!-- Workplace Type Radio Cards -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                            >Workplace Type</label
                        >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- On-Site -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.workplaceType === 'On-Site'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >On-Site</span
                                    >
                                    <input
                                        type="radio"
                                        value="On-Site"
                                        v-model="form.workplaceType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Employees work from an office
                                </p>
                            </label>

                            <!-- Hybrid -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.workplaceType === 'Hybrid'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >Hybrid</span
                                    >
                                    <input
                                        type="radio"
                                        value="Hybrid"
                                        v-model="form.workplaceType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Employees work from both office and home
                                </p>
                            </label>

                            <!-- Remote -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.workplaceType === 'Remote'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >Remote</span
                                    >
                                    <input
                                        type="radio"
                                        value="Remote"
                                        v-model="form.workplaceType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Employees work from home
                                </p>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Card 2: Location -->
                <section
                    class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-4"
                >
                    <h2
                        class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
                    >
                        Location
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <BaseSelect
                            v-model="form.country"
                            label="Country"
                            placeholder="Select Country"
                            :options="[
                                { label: 'Saudi Arabia', value: 'SA' },
                                { label: 'United States', value: 'US' },
                            ]"
                        />
                        <BaseSelect
                            v-model="form.city"
                            label="City"
                            placeholder="Select City"
                            :options="[
                                { label: 'Riyadh', value: 'riyadh' },
                                { label: 'Jeddah', value: 'jeddah' },
                            ]"
                        />
                    </div>
                </section>

                <!-- Card 3: Salary & Payment Type -->
                <section
                    class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-5"
                >
                    <div
                        class="flex items-center justify-between border-b border-gray-100 pb-3"
                    >
                        <h2 class="text-base font-bold text-gray-900">
                            Salary
                        </h2>
                        <label
                            class="flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-600"
                        >
                            <input
                                type="checkbox"
                                v-model="form.preferNotToSaySalary"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            />
                            Prefer not to say
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <BaseInput
                            v-model="form.minSalary"
                            label="Salary Range"
                            placeholder="Min Salary"
                        />
                        <div class="sm:pt-6">
                            <BaseInput
                                v-model="form.maxSalary"
                                placeholder="Max Salary"
                            />
                        </div>
                        <BaseSelect
                            v-model="form.currency"
                            label="Currency"
                            :options="currencyOptions"
                        />
                    </div>

                    <!-- Payment Type Radio Cards -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                            >Payment Type</label
                        >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Weekly -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.paymentType === 'Weekly'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >Weekly</span
                                    >
                                    <input
                                        type="radio"
                                        value="Weekly"
                                        v-model="form.paymentType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Payment is processed every week. Suitable
                                    for short-term projects.
                                </p>
                            </label>

                            <!-- Monthly -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.paymentType === 'Monthly'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >Monthly</span
                                    >
                                    <input
                                        type="radio"
                                        value="Monthly"
                                        v-model="form.paymentType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Payment is processed each month. Ideal for
                                    ongoing work or contracts.
                                </p>
                            </label>

                            <!-- One Time -->
                            <label
                                :class="[
                                    'cursor-pointer rounded-xl border p-4 transition-all flex flex-col justify-between',
                                    form.paymentType === 'One Time'
                                        ? 'border-purple-600 bg-purple-50/20 ring-1 ring-purple-600'
                                        : 'border-gray-200 bg-white hover:border-gray-300',
                                ]"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >One Time</span
                                    >
                                    <input
                                        type="radio"
                                        value="One Time"
                                        v-model="form.paymentType"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                </div>
                                <p
                                    class="text-[11px] text-gray-500 leading-normal"
                                >
                                    Single payment made once. Best for
                                    fixed-scope or one-off jobs.
                                </p>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Card 4: Job Description and Requirement -->
                <section
                    class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-4"
                >
                    <h2
                        class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
                    >
                        Job Description and Requirement
                    </h2>

                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                            >Description</label
                        >
                        <div class="relative">
                            <textarea
                                v-model="form.description"
                                rows="5"
                                placeholder="Enter job description..."
                                class="w-full rounded-xl border border-gray-300 bg-white p-3.5 text-xs text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/20 transition resize-y"
                            ></textarea>
                            <div class="absolute bottom-3 left-3">
                                <!-- <button
                                    type="button"
                                    @click="handleGenerateAI"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-linear-to-r from-orange-500 to-purple-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-95 transition"
                                >
                                    <span>Generate with AI</span>
                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                </button> -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Card 5: Preferences -->
                <section
                    class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-xs space-y-4"
                >
                    <h2
                        class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3"
                    >
                        Preferences
                    </h2>

                    <div
                        class="flex items-center gap-2 rounded-xl bg-purple-50/60 p-3 text-purple-900 border border-purple-100 text-xs"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-purple-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <span>This preferences is for internal use only.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <BaseSelect
                            v-model="form.candidateGender"
                            label="Candidate Gender"
                            placeholder="Select Gender"
                            :options="genderOptions"
                        />
                        <BaseSelect
                            v-model="form.nationality"
                            label="Nationality"
                            placeholder="Select Nationality"
                            :options="nationalityOptions"
                        />
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end"
                    >
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-gray-700"
                                >Marriage Situation</label
                            >
                            <div class="flex items-center gap-6 h-10">
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700"
                                >
                                    <input
                                        type="radio"
                                        value="Single"
                                        v-model="form.marriageSituation"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                    Single
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700"
                                >
                                    <input
                                        type="radio"
                                        value="Marriage"
                                        v-model="form.marriageSituation"
                                        class="text-purple-600 focus:ring-purple-500"
                                    />
                                    Marriage
                                </label>
                            </div>
                        </div>
                        <div>
                            <BaseInput
                                v-model="form.yearOfExperience"
                                label="Year of Experience"
                                placeholder="e.g. 4 Years"
                            />
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
