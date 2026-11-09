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
  <RouterView />
</template>

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
