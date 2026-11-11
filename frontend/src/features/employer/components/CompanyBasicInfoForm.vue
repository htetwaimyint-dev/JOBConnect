<script setup lang="ts">
import type { CompanyForm } from "../types/company-form.types";
import BaseInput from "@/components/common/BaseInput.vue";

defineProps<{ logoPreview: string | null; errors: Record<string, string[]> }>();
const emit = defineEmits<{ selectLogo: [file: File] }>();
const form = defineModel<CompanyForm>("form", { required: true });

const employeeOptions = [
    "1-10",
    "11-50",
    "51-200",
    "201-500",
    "501-1000",
    "1000+",
];

function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) emit("selectLogo", file);
}
</script>

<template>
    <section
        class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm"
    >
        <h4 class="text-lg font-bold text-slate-900 mb-6">Who We Are</h4>
        <div class="bg-[#f4f7fa] px-5 py-3 rounded-sm mt-3">
            <div class="divide-y divide-slate-200">
                <!-- Row 1: Company Name / Public Profile -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Public profile</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            This will be displayed on your profile.
                        </p>
                    </div>
                    <div class="md:col-span-8 space-y-3 mt-1">
                        <BaseInput
                            v-model="form.name"
                            placeholder="Untitled UI"
                            class="h-8! px-3! text-xs!"
                        />
                        <p
                            v-if="errors.name?.length"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.name[0] }}
                        </p>
                    </div>
                </div>

                <!-- Row 2: Tagline -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Tagline</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            A quick snapshot of your company.
                        </p>
                    </div>
                    <div class="md:col-span-8 mt-1">
                        <BaseInput
                            v-model="form.tagline"
                            placeholder="A short company tagline or slogan"
                            class="h-8! px-3! text-xs!"
                        />
                        <p
                            v-if="errors.tagline?.length"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ errors.tagline[0] }}
                        </p>
                    </div>
                </div>

                <!-- Row 3: Industry & Website -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Industry & Web</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            Your primary sector and corporate URL.
                        </p>
                    </div>
                    <div
                        class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1"
                    >
                        <div>
                            <BaseInput
                                v-model="form.industry"
                                placeholder="e.g. Technology"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.industry?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.industry[0] }}
                            </p>
                        </div>
                        <div>
                            <BaseInput
                                v-model="form.website"
                                placeholder="https://example.com"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.website?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.website[0] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Contact Email & Phone -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Contact Details</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            Direct communication channels.
                        </p>
                    </div>
                    <div
                        class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1"
                    >
                        <div>
                            <BaseInput
                                v-model="form.company_email"
                                type="email"
                                placeholder="contact@example.com"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.company_email?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.company_email[0] }}
                            </p>
                        </div>
                        <div>
                            <BaseInput
                                v-model="form.phone"
                                type="tel"
                                placeholder="+1 (555) 000-0000"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.phone?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.phone[0] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Founded Year & Employee Count -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Scale & Foundation</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            Company age and total headcount.
                        </p>
                    </div>
                    <div
                        class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1"
                    >
                        <div>
                            <BaseInput
                                v-model="form.foundedYear"
                                type="number"
                                placeholder="e.g. 2016"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.founded_year?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.founded_year[0] }}
                            </p>
                        </div>
                        <div>
                            <select
                                v-model="form.employeeCount"
                                class="h-8 w-full border border-slate-200 bg-white px-3 text-xs! text-slate-600 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/10"
                            >
                                <option value="">Select company size</option>
                                <option
                                    v-for="opt in employeeOptions"
                                    :key="opt"
                                    :value="opt"
                                >
                                    {{ opt }} employees
                                </option>
                            </select>
                            <p
                                v-if="errors.employee_count?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.employee_count[0] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Location (City & Address) -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Headquarters</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            Primary office location.
                        </p>
                    </div>
                    <div
                        class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1"
                    >
                        <div>
                            <BaseInput
                                v-model="form.city"
                                placeholder="City"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.city?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.city[0] }}
                            </p>
                        </div>
                        <div>
                            <BaseInput
                                v-model="form.address"
                                placeholder="Street Address"
                                class="h-8! px-3! text-xs!"
                            />
                            <p
                                v-if="errors.address?.length"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ errors.address[0] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Row 7: Company Logo Upload (Matching the reference layout precisely) -->
                <div
                    class="py-3 grid grid-cols-1 md:grid-cols-12 gap-4 items-start"
                >
                    <div class="md:col-span-4">
                        <label
                            class="block text-xs font-semibold text-slate-900"
                            >Company logo</label
                        >
                        <p class="text-xs text-slate-500 mt-0.5">
                            Update your company logo and preview.
                        </p>
                    </div>
                    <div
                        class="md:col-span-8 flex flex-col sm:flex-row items-center gap-4 mt-1"
                    >
                        <!-- Current Preview Badge -->
                        <div
                            class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 w-full sm:w-auto"
                        >
                            <div
                                class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-white border border-slate-200 flex items-center justify-center"
                            >
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
                                    alt="Logo"
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    v-else
                                    class="text-[9px] font-bold text-slate-400"
                                    >LOGO</span
                                >
                            </div>
                            <span
                                class="text-xs! font-semibold text-slate-900 truncate max-w-30"
                                >{{ form.name || "Company Logo" }}</span
                            >
                        </div>

                        <!-- Drag & Drop Upload Box -->
                        <label
                            class="flex-1 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white p-3 text-center transition hover:border-blue-500 hover:bg-blue-50/20 w-full"
                        >
                            <div
                                class="flex items-center gap-2 text-xs font-semibold text-slate-700 justify-center"
                            >
                                <svg
                                    class="h-4 w-4 text-blue-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                    />
                                </svg>
                                <span>Click to upload or drag and drop</span>
                            </div>
                            <span class="mt-1 text-[10px] text-slate-400"
                                >SVG, PNG, JPG or GIF (max. 800x400px)</span
                            >
                            <input
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                class="hidden"
                                @change="onLogoChange"
                            />
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
