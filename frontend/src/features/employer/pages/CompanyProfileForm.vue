<script setup lang="ts">
import { ref, watch } from "vue";
import ManageProfileHeader from "../components/ManageProfileHeader.vue";
import TipBanner from "../components/TipBanner.vue";
import CompanyBasicInfoForm from "../components/CompanyBasicInfoForm.vue";
import CompanyOverviewForm from "../components/CompanyOverviewForm.vue";
import CompanyBrandingForm from "../components/CompanyBrandingForm.vue";
import CompanyGalleryForm from "../components/CompanyGalleryForm.vue";
import { useCompanyProfile } from "@/composables/useUpdateCompanyProfile.ts";
import { useCompanyProfileStore } from "@/stores/company-profile.store.ts";
import { useRoute, useRouter } from "vue-router";

const store = useCompanyProfileStore();
const { isPending, mutation, submitForm } = useCompanyProfile();

// Dynamic Tab Switching
const activeTab = ref("Overview");
const tabs = ["Overview", "Description", "Gallery"];

const route = useRoute();

watch(
    () => route.query.tab,
    (tab) => {
        if (tab === "gallery") {
            activeTab.value = "Gallery";
        }
    },
    { immediate: true },
);

const router = useRouter();

function goToProfile() {
    router.push({ name: "company-profile" });
}
</script>

<template>
    <div
        class="min-h-screen bg-[#F8F9FC] pb-28 font-sans text-slate-900 antialiased"
    >
        <form @submit.prevent="submitForm">
            <!-- Title & Tab Navigation Header -->
            <ManageProfileHeader
                v-model:active-tab="activeTab"
                :tabs="tabs"
                :is-pending="isPending"
                :is-edit-mode="store.isEditMode"
                :is-submitting="mutation.isPending.value"
                @reset="store.reset"
                @viewProfile="goToProfile"
            />

            <!-- Main Layout Body -->
            <main class="mx-auto max-w-5xl px-6 pt-6">
                <!-- Loading Spinner -->
                <div
                    v-if="isPending"
                    class="flex flex-col items-center justify-center py-24"
                >
                    <div
                        class="h-8 w-8 animate-spin rounded-full border-2 border-slate-200 border-t-[#0052FF]"
                    ></div>
                    <p class="mt-3 text-xs font-medium text-slate-400">
                        Loading company profile...
                    </p>
                </div>

                <div v-else class="space-y-6">
                    <!-- Status Messages -->
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform -translate-y-2 opacity-0"
                        enter-to-class="transform translate-y-0 opacity-100"
                    >
                        <div
                            v-if="store.successMessage"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-900 shadow-2xs"
                        >
                            {{ store.successMessage }}
                        </div>
                    </Transition>

                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform -translate-y-2 opacity-0"
                        enter-to-class="transform translate-y-0 opacity-100"
                    >
                        <div
                            v-if="store.errorMessage"
                            class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-900 shadow-2xs"
                        >
                            {{ store.errorMessage }}
                        </div>
                    </Transition>

                    <!-- Callout Tip Banner -->
                    <TipBanner />

                    <!-- Tab Content Views -->
                    <Transition
                        mode="out-in"
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-100 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <!-- Tab 1: Overview Component -->
                        <div v-if="activeTab === 'Overview'" key="overview">
                            <CompanyBasicInfoForm
                                v-model:form="store.form"
                                :errors="store.errors"
                                :logo-preview="store.logoPreview"
                                @select-logo="store.selectImage($event, 'logo')"
                            />
                        </div>

                        <!-- Tab 2: Description Component -->
                        <div
                            v-else-if="activeTab === 'Description'"
                            key="description"
                        >
                            <CompanyOverviewForm
                                v-model:form="store.form"
                                :errors="store.errors"
                            />
                        </div>

                        <!-- Tab 3: Gallery & Branding Components -->
                        <div
                            v-else-if="activeTab === 'Gallery'"
                            key="gallery"
                            class="space-y-6"
                        >
                            <CompanyBrandingForm
                                :cover-preview="store.coverPreview"
                                :errors="store.errors"
                                @select-cover="
                                    store.selectImage($event, 'cover')
                                "
                            />

                            <CompanyGalleryForm
                                :existing-images="store.existingGallery"
                                :new-images="store.newGallery"
                                :errors="store.errors"
                                @add-images="store.addGallery"
                                @remove-existing="store.removeGallery($event)"
                                @remove-new="store.removeGallery($event, true)"
                            />
                        </div>
                    </Transition>
                </div>
            </main>
        </form>
    </div>
</template>
