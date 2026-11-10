<script setup lang="ts">
import { ref } from "vue";
import BaseButton from "@/components/common/BaseButton.vue";
import CompanyBasicInfoForm from "../components/CompanyBasicInfoForm.vue";
import CompanyOverviewForm from "../components/CompanyOverviewForm.vue";
import CompanyBrandingForm from "../components/CompanyBrandingForm.vue";
import CompanyGalleryForm from "../components/CompanyGalleryForm.vue";
import { useCompanyProfile } from "@/composables/useCompany.ts";
import { useCompanyProfileStore } from "@/stores/company-profile.store.ts";

const store = useCompanyProfileStore();
const { isPending, mutation, submitForm } = useCompanyProfile();

// Active tab simulation for the Built In sub-nav bar
const activeSubTab = ref("Overview");
const subTabs = [
    "Overview",
    "Offices",
    "Perks + Benefits",
    "Teams",
    "Culture",
    "Articles",
    "Employer Reputation",
];
</script>

<template>
    <div
        class="min-h-screen bg-[#F8F9FC] pb-28 font-sans text-slate-900 antialiased"
    >
        <form @submit.prevent="submitForm">
            <!-- Top Portal Navbar Header (Dark Theme Header from Reference) -->
            <header class="bg-[#0B132B] text-white">
                <div
                    class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center gap-2 font-black tracking-wider text-lg"
                        >
                            <span
                                class="rounded bg-blue-600 px-2 py-1 text-white"
                                >builtin</span
                            >
                            <span
                                class="font-normal text-slate-300 text-sm border-l border-slate-700 pl-3"
                                >Employer Portal</span
                            >
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <BaseButton
                            type="button"
                            size="28"
                            variant="outline"
                            color="neutral"
                            class="border-slate-700 bg-transparent text-slate-200 hover:bg-slate-800 text-xs font-medium"
                        >
                            VISIT HOMEPAGE
                        </BaseButton>
                        <div
                            class="h-8 w-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-white"
                        >
                            BP
                        </div>
                    </div>
                </div>
            </header>

            <!-- Manage Profile Title & Action Bar -->
            <div class="border-b border-slate-200 bg-white shadow-2xs">
                <div
                    class="mx-auto max-w-7xl px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900"
                        >
                            Manage Profile
                        </h1>
                    </div>

                    <!-- Header Actions -->
                    <div v-if="!isPending" class="flex items-center gap-3">
                        <BaseButton
                            type="button"
                            size="32"
                            color="neutral"
                            variant="outline"
                            :disabled="mutation.isPending.value"
                            @click="store.reset"
                            class="text-xs font-semibold"
                        >
                            Cancel
                        </BaseButton>
                        <BaseButton
                            type="submit"
                            size="32"
                            :loading="mutation.isPending.value"
                            :disabled="mutation.isPending.value"
                            class="bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700"
                        >
                            {{
                                store.isEditMode
                                    ? "Save Changes"
                                    : "Create Profile"
                            }}
                        </BaseButton>
                    </div>
                </div>

                <!-- Built In Sub-navigation Tab Bar -->
                <div class="mx-auto max-w-7xl px-6">
                    <nav class="flex space-x-8 overflow-x-auto scrollbar-none">
                        <button
                            v-for="tab in subTabs"
                            :key="tab"
                            type="button"
                            @click="activeSubTab = tab"
                            :class="[
                                activeSubTab === tab
                                    ? 'border-blue-600 text-blue-600 font-bold'
                                    : 'border-transparent text-slate-500 font-medium hover:text-slate-900 hover:border-slate-300',
                                'whitespace-nowrap border-b-2 py-3.5 text-xs sm:text-sm transition-colors',
                            ]"
                        >
                            {{ tab }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Main Layout Container with Right-Side Anchor Navigation -->
            <div class="mx-auto max-w-7xl px-6 pt-6">
                <!-- Loading Indicator -->
                <div
                    v-if="isPending"
                    class="flex flex-col items-center justify-center py-24"
                >
                    <div
                        class="h-8 w-8 animate-spin rounded-full border-2 border-slate-200 border-t-blue-600"
                    ></div>
                    <p class="mt-3 text-xs font-medium text-slate-400">
                        Loading company profile...
                    </p>
                </div>

                <!-- Content Grid (Forms Left / Right Sticky Anchor Navigation Menu) -->
                <div
                    v-else
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
                >
                    <!-- Left Forms Column (Span 8) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Notification Banners -->
                        <div
                            v-if="store.successMessage"
                            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-900 shadow-2xs"
                        >
                            {{ store.successMessage }}
                        </div>
                        <div
                            v-if="store.errorMessage"
                            class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-900 shadow-2xs"
                        >
                            {{ store.errorMessage }}
                        </div>

                        <!-- Built In Tip Banner -->
                        <div
                            class="flex items-start gap-3.5 rounded-xl border border-teal-200/80 bg-teal-50/40 p-4 shadow-2xs"
                        >
                            <div
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border border-teal-500 bg-teal-50 text-teal-600 text-xs font-bold"
                            >
                                ✓
                            </div>
                            <p
                                class="text-xs font-medium leading-relaxed text-slate-700"
                            >
                                <strong class="text-slate-900"
                                    >Getting top talent excited about your
                                    company starts here.</strong
                                >
                                Be sure to share plenty of details, especially
                                about your culture, perks and mission. This info
                                will go a long way to helping your company stand
                                out.
                            </p>
                        </div>

                        <!-- Form Modules -->
                        <div id="who-we-are">
                            <CompanyBasicInfoForm
                                v-model:form="store.form"
                                :logo-preview="store.logoPreview"
                                @select-logo="store.selectImage($event, 'logo')"
                            />
                        </div>

                        <div id="about-us">
                            <CompanyOverviewForm v-model:form="store.form" />
                        </div>

                        <div id="profile-style">
                            <CompanyBrandingForm
                                :cover-preview="store.coverPreview"
                                @select-cover="
                                    store.selectImage($event, 'cover')
                                "
                            />
                        </div>

                        <div id="gallery-images">
                            <CompanyGalleryForm
                                :existing-images="store.existingGallery"
                                :new-images="store.newGallery"
                                @add-images="store.addGallery"
                                @remove-existing="store.removeGallery($event)"
                                @remove-new="store.removeGallery($event, true)"
                            />
                        </div>
                    </div>

                    <!-- Right Side Quick Anchor Menu (Matching Built In Reference Sidebar) -->
                    <aside class="hidden lg:block lg:col-span-4 sticky top-6">
                        <div
                            class="rounded-xl border border-slate-200 bg-white p-5 shadow-2xs"
                        >
                            <nav class="space-y-4 text-xs">
                                <div>
                                    <a
                                        href="#who-we-are"
                                        class="font-bold text-slate-900 hover:text-blue-600 block"
                                        >Who We Are</a
                                    >
                                </div>
                                <div>
                                    <a
                                        href="#about-us"
                                        class="font-bold text-slate-900 hover:text-blue-600 block"
                                        >About Us</a
                                    >
                                    <div
                                        class="mt-2.5 space-y-2 pl-3 border-l-2 border-slate-100 text-slate-600"
                                    >
                                        <a
                                            href="#about-us"
                                            class="block hover:text-blue-600"
                                            >Description</a
                                        >
                                        <a
                                            href="#about-us"
                                            class="block hover:text-blue-600"
                                            >Industry Type</a
                                        >
                                        <a
                                            href="#about-us"
                                            class="block hover:text-blue-600"
                                            >Awards</a
                                        >
                                    </div>
                                </div>
                                <div>
                                    <a
                                        href="#profile-style"
                                        class="font-bold text-slate-900 hover:text-blue-600 block"
                                        >Profile Style</a
                                    >
                                    <div
                                        class="mt-2.5 space-y-2 pl-3 border-l-2 border-slate-100 text-slate-600"
                                    >
                                        <a
                                            href="#profile-style"
                                            class="block hover:text-blue-600"
                                            >Logo</a
                                        >
                                        <a
                                            href="#profile-style"
                                            class="block hover:text-blue-600"
                                            >Cover Photo</a
                                        >
                                        <a
                                            href="#profile-style"
                                            class="block hover:text-blue-600"
                                            >Brand Background Color</a
                                        >
                                        <a
                                            href="#gallery-images"
                                            class="block hover:text-blue-600"
                                            >Gallery Images + Video</a
                                        >
                                    </div>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block"
                                        >Technologies We Use</span
                                    >
                                    <div
                                        class="mt-2.5 space-y-2 pl-3 border-l-2 border-slate-100 text-slate-600"
                                    >
                                        <span class="block text-slate-400"
                                            >Engineering</span
                                        >
                                        <span class="block text-slate-400"
                                            >People Operations</span
                                        >
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Mobile Sticky Footer Bar -->
            <div
                v-if="!isPending"
                class="fixed bottom-0 inset-x-0 z-30 flex items-center justify-end gap-3 border-t border-slate-200 bg-white/95 px-6 py-3.5 backdrop-blur-md sm:hidden shadow-lg"
            >
                <BaseButton
                    type="button"
                    size="32"
                    color="neutral"
                    variant="outline"
                    :disabled="mutation.isPending.value"
                    @click="store.reset"
                >
                    Cancel
                </BaseButton>
                <BaseButton
                    type="submit"
                    size="32"
                    :loading="mutation.isPending.value"
                    :disabled="mutation.isPending.value"
                >
                    {{ store.isEditMode ? "Save Changes" : "Create Profile" }}
                </BaseButton>
            </div>
        </form>
    </div>
</template>
