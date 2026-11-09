<script setup lang="ts">
import { ref } from "vue";

import CompanyBanner from "./features/employer/components/CompanyBanner.vue";
import CompanyOverview from "./features/employer/components/CompanyOverview.vue";
import CompanyJobs from "./features/employer/components/CompanyJobs.vue";
import CompanyGallery from "./features/employer/components/CompanyGallery.vue";
import CompanySidebar from "./features/employer/components/CompanySidebar.vue";

import {
    company,
    jobs,
} from "./features/employer/repositories/companyRepository.ts";
import BaseButton from "./components/common/BaseButton.vue";

type Tab = "about" | "jobs" | "gallery";

const activeTab = ref<Tab>("about");
const isFollowing = ref(false);

const tabs: { label: string; value: Tab }[] = [
    { label: "About", value: "about" },
    { label: "Jobs", value: "jobs" },
    { label: "Gallery", value: "gallery" },
];

function toggleFollow() {
    isFollowing.value = !isFollowing.value;
}

function goToJobs() {
    activeTab.value = "jobs";
    window.scrollTo({ top: 0, behavior: "smooth" });
}
</script>

<template>
    <main class="min-h-screen bg-[#f7f7fb]">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Main Two Column Layout -->
            <div
                class="mt-8 grid grid-cols-1 items-start gap-8 lg:grid-cols-14"
            >
                <!-- Left Column -->
                <section class="min-w-0 lg:col-span-10">
                    <!-- Company Banner -->
                    <CompanyBanner
                        :company="company"
                        :is-following="isFollowing"
                        @follow="toggleFollow"
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
                                @click="activeTab = tab.value"
                                class="rounded-none! border-b-2 px-1 pb-2 text-sm font-semibold transition-colors hover:bg-gray-100 active:bg-gray-100"
                                :class="
                                    activeTab === tab.value
                                        ? 'border-violet-600 text-violet-700'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                "
                            >
                                {{ tab.label }}

                                <span
                                    v-if="tab.value === 'jobs'"
                                    class="ml-1.5 rounded-full bg-violet-50 px-2 py-0.5 text-xs text-violet-700"
                                >
                                    {{ company.openJobsCount }}
                                </span>
                            </BaseButton>
                            <!-- <button
                                v-for="tab in tabs"
                                :key="tab.value"
                                type="button"
                                @click="activeTab = tab.value"
                                class="border-b-2 px-1 pb-2 text-sm font-semibold transition-colors"
                                :class="
                                    activeTab === tab.value
                                        ? 'border-violet-600 text-violet-700'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                "
                            >
                                {{ tab.label }}

                                <span
                                    v-if="tab.value === 'jobs'"
                                    class="ml-1.5 rounded-full bg-violet-50 px-2 py-0.5 text-xs text-violet-700"
                                >
                                    {{ company.openJobsCount }}
                                </span>
                            </button> -->
                        </nav>
                    </div>
                    <!-- Tab Content -->
                    <div class="mt-4">
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
                            :images="company.gallery"
                        />
                    </div>
                </section>

                <!-- Right Column: Always Visible -->
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-6">
                        <CompanySidebar :company="company" />
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>
