<script setup lang="ts">
import { ref, computed } from "vue";

import CompanyBanner from "../components/CompanyBanner.vue";
import CompanyOverview from "../components/CompanyOverview.vue";
import CompanyJobs from "../components/CompanyJobs.vue";
import CompanyGallery from "../components/CompanyGallery.vue";
import CompanySidebar from "../components/CompanySidebar.vue";

import type { Company, Job } from "../types/company.types.ts";
import BaseButton from "@/components/common/BaseButton.vue";
import { useCompanyStore } from "@/stores/company.store.ts";
import { useCompanyProfile } from "@/composables/useUpdateCompanyProfile.ts";
import type { CompanyTab } from "@/stores/company.store.ts";
import { useRouter } from "vue-router";

const companyStore = useCompanyStore();

const {
    data: company,
    isPending,
    isError,
    error,
    refetch,
} = useCompanyProfile();

const jobs: Job[] = [];

const tabs: { label: string; value: CompanyTab }[] = [
    { label: "About", value: "about" },
    { label: "Jobs", value: "jobs" },
    { label: "Gallery", value: "gallery" },
];

const activeTab = computed(() => companyStore.activeTab);

function selectTab(tab: CompanyTab) {
    companyStore.setActiveTab(tab);
}

function goToJobs() {}
</script>

<template>
    <main class="min-h-screen bg-[#f7f7fb]">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Loading -->
            <div v-if="isPending" class="rounded-2xl bg-white p-12 text-center">
                <p class="text-sm text-gray-500">Loading company profile...</p>
            </div>

            <!-- Error -->
            <div
                v-else-if="isError"
                class="rounded-2xl border border-red-100 bg-white p-10 text-center"
            >
                <p class="font-semibold text-red-600">
                    {{ error?.message || "Unable to load company profile." }}
                </p>

                <button
                    type="button"
                    @click="refetch()"
                    class="mt-4 rounded-lg bg-violet-600 px-5 py-2 text-sm font-semibold text-white hover:bg-violet-700"
                >
                    Try Again
                </button>
            </div>

            <!-- Company Profile -->
            <template v-else-if="company">
                <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-14">
                    <!-- Left Column -->
                    <section class="min-w-0 lg:col-span-10">
                        <!-- Banner -->
                        <CompanyBanner
                            :company="company"
                            :is-following="companyStore.isFollowing"
                            @follow="companyStore.toggleFollow"
                            @view-jobs="goToJobs"
                        />
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 pt-3">
                            <nav
                                class="-mb-px flex gap-8"
                                aria-label="Company sections"
                            >
                                <BaseButton
                                    v-for="tab in tabs"
                                    type="button"
                                    variant="ghost"
                                    @click="selectTab(tab.value)"
                                    class="rounded-none! border-b-2 px-1 pb-2 text-sm font-semibold transition-colors hover:bg-gray-100 active:bg-gray-100"
                                    :class="
                                        activeTab === tab.value
                                            ? 'border-violet-600 text-violet-700'
                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                    "
                                >
                                    {{ tab.label }}

                                    <!-- <span
                                        v-if="tab.value === 'jobs'"
                                        class="ml-1.5 rounded-full bg-violet-50 px-2 py-0.5 text-xs text-violet-700"
                                    >
                                        {{ company.openJobsCount }}
                                    </span> -->
                                </BaseButton>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="pt-6">
                            <CompanyOverview
                                v-if="activeTab === 'about'"
                                :company="company"
                            />

                            <CompanyJobs
                                v-else-if="activeTab === 'jobs'"
                                :jobs="jobs"
                            />

                            <CompanyGallery
                                v-else-if="activeTab === 'gallery'"
                                :existing-images="company.gallery ?? []"
                            />
                        </div>
                    </section>

                    <!-- Right Sidebar -->
                    <aside class="lg:col-span-4">
                        <div class="lg:sticky lg:top-6">
                            <CompanySidebar :company="company" />
                        </div>
                    </aside>
                </div>
            </template>
        </div>
    </main>
</template>
