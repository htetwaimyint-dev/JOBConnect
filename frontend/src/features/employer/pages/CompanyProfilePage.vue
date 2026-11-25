<script setup lang="ts">
import { computed, ref } from "vue";

import { Eye, X, AlertCircle } from "lucide-vue-next";

import CompanyBanner from "../components/CompanyBanner.vue";
import CompanyOverview from "../components/CompanyOverview.vue";
import CompanyJobs from "../components/CompanyJobs.vue";
import CompanyGallery from "../components/CompanyGallery.vue";
import CompanySidebar from "../components/CompanySidebar.vue";

import type { Job, JobListParams } from "@/features/jobs/types/job.types";
import { useCompanyProfile } from "@/features/employer/composables/useUpdateCompanyProfile";
import { useJobs } from "@/features/jobs/composables/useJobs";

// Company Profile
const {
  data: company,
  isPending,
  isError,
  error,
  refetch,
} = useCompanyProfile();

// Rejected Status
const isRejected = computed(
  () => company.value?.verification_status === "rejected",
);

// Preview Modal
const isPreviewOpen = ref(false);

function handlePreview() {
  isPreviewOpen.value = true;
}

function closePreview() {
  isPreviewOpen.value = false;
}

// Jobs
const params = computed<JobListParams>(() => ({
  page: 1,
}));

const { getJobsByCompany } = useJobs();
const jobsQuery = getJobsByCompany(params);

const jobs = computed(() => jobsQuery.data.value?.data ?? []);

// Navigation
function goToJobs() {
  // Add job navigation here when needed.
}

function selectJob(job: Job) {
  // Add job detail navigation here when needed.
}
</script>

<template>
  <main class="min-h-screen bg-[#F8FAFC] pb-16">
    <!-- Loading -->
    <div v-if="isPending" class="mx-auto max-w-7xl px-4 py-16 text-center">
      <p class="text-sm text-slate-500">Loading company profile...</p>
    </div>

    <!-- Error -->
    <div v-else-if="isError" class="mx-auto max-w-7xl px-4 py-16 text-center">
      <p class="font-semibold text-red-600">
        {{ error?.message || "Unable to load company profile." }}
      </p>

      <button
        type="button"
        @click="refetch()"
        class="mt-4 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
      >
        Try Again
      </button>
    </div>

    <!-- Company Profile -->
    <template v-else-if="company">
      <!-- Company Banner -->
      <CompanyBanner
        :company="company"
        @preview="handlePreview"
        @view-jobs="goToJobs"
      />

      <!-- Rejection Reason -->
      <div
        v-if="isRejected && company.rejection_reason?.trim()"
        class="mx-auto max-w-[1160px] px-4 sm:px-6 lg:px-8"
      >
        <div
          class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 sm:p-5"
        >
          <div class="flex items-start gap-3">
            <AlertCircle class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />

            <div class="min-w-0">
              <p class="text-sm font-bold text-rose-700">
                Company Verification Rejection Reason
              </p>

              <p
                class="mt-2 whitespace-pre-line text-sm leading-6 text-rose-700"
              >
                {{ company.rejection_reason }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Two Column Layout -->
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
          <!-- Left Column -->
          <div class="min-w-0 space-y-8 lg:col-span-8">
            <CompanyOverview :company="company" />

            <CompanyGallery
              :existing-images="company.gallery ?? []"
              :company-name="company.name"
            />
          </div>

          <!-- Right Column -->
          <div class="min-w-0 lg:col-span-4">
            <div class="lg:sticky lg:top-6">
              <CompanySidebar :company="company" />
            </div>
          </div>
        </div>
        <CompanyJobs :jobs="jobs" :company="company" @select-job="selectJob" />
      </div>

      <!-- Preview Modal -->
      <Teleport to="body">
        <div
          v-if="isPreviewOpen"
          class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 p-4"
          @click.self="closePreview"
        >
          <div
            class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl sm:p-7"
          >
            <!-- Close Button -->
            <button
              type="button"
              @click="closePreview"
              class="absolute right-4 top-4 rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
              aria-label="Close preview"
            >
              <X class="h-5 w-5" />
            </button>

            <!-- Modal Header -->
            <div class="mb-6 flex items-center gap-2 pr-10">
              <Eye class="h-5 w-5 text-blue-600" />

              <h2 class="text-lg font-bold text-slate-900">
                Company Profile Preview
              </h2>
            </div>

            <div class="space-y-6">
              <!-- Company Information -->
              <div class="flex flex-wrap items-center gap-4">
                <img
                  v-if="company.logo"
                  :src="company.logo"
                  :alt="`${company.name} logo`"
                  class="h-16 w-16 rounded-xl border border-slate-200 object-cover"
                />

                <div
                  v-else
                  class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                >
                  <Eye class="h-7 w-7" />
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-xl font-bold text-slate-900">
                      {{ company.name }}
                    </h3>

                    <span
                      v-if="isRejected"
                      class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600"
                    >
                      Rejected
                    </span>

                    <span
                      v-else-if="company.verification_status === 'verified'"
                      class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600"
                    >
                      Verified
                    </span>

                    <span
                      v-else
                      class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600"
                    >
                      Pending
                    </span>
                  </div>

                  <p class="mt-1 text-sm text-slate-500">
                    {{
                      company.tagline || company.industry || "Company Profile"
                    }}
                  </p>
                </div>
              </div>

              <!-- Rejection Reason in Preview -->
              <div
                v-if="isRejected"
                class="rounded-xl border border-rose-200 bg-rose-50 p-4"
              >
                <div class="flex items-start gap-3">
                  <AlertCircle class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />

                  <div class="min-w-0">
                    <p class="text-sm font-bold text-rose-700">
                      Company Verification Rejected
                    </p>

                    <p class="mt-1 text-sm text-rose-700">
                      Your company verification request has been rejected.
                    </p>

                    <div
                      v-if="company.rejection_reason?.trim()"
                      class="mt-3 rounded-lg border border-rose-200 bg-white p-3"
                    >
                      <p
                        class="text-xs font-bold uppercase tracking-wide text-rose-600"
                      >
                        Rejection Reason
                      </p>

                      <p
                        class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700"
                      >
                        {{ company.rejection_reason }}
                      </p>
                    </div>

                    <p v-else class="mt-2 text-sm italic text-rose-600">
                      No rejection reason provided.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Cover Image -->
              <div
                v-if="company.cover_image"
                class="h-40 overflow-hidden rounded-xl bg-slate-100"
              >
                <img
                  :src="company.cover_image"
                  alt="Company cover"
                  class="h-full w-full object-cover"
                />
              </div>

              <!-- Description -->
              <div>
                <h3 class="mb-2 font-semibold text-slate-900">About Company</h3>

                <p class="whitespace-pre-line text-sm leading-7 text-slate-600">
                  {{
                    company.description || "No company description available."
                  }}
                </p>
              </div>

              <!-- Details -->
              <div
                class="grid grid-cols-1 gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2"
              >
                <div>
                  <p class="text-xs text-slate-400">Industry</p>
                  <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ company.industry || "N/A" }}
                  </p>
                </div>

                <div>
                  <p class="text-xs text-slate-400">Location</p>
                  <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ company.city || company.address || "N/A" }}
                  </p>
                </div>

                <div>
                  <p class="text-xs text-slate-400">Website</p>

                  <a
                    v-if="company.website"
                    :href="company.website"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-1 block break-all text-sm font-semibold text-blue-600 hover:underline"
                  >
                    {{ company.website }}
                  </a>

                  <p v-else class="mt-1 text-sm font-semibold text-slate-700">
                    N/A
                  </p>
                </div>

                <div>
                  <p class="text-xs text-slate-400">Company Size</p>
                  <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ company.employee_count || "N/A" }}
                  </p>
                </div>

                <div>
                  <p class="text-xs text-slate-400">Address</p>
                  <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ company.address || "N/A" }}
                  </p>
                </div>

                <div>
                  <p class="text-xs text-slate-400">Company Email</p>
                  <p
                    class="mt-1 break-all text-sm font-semibold text-slate-700"
                  >
                    {{ company.company_email || "N/A" }}
                  </p>
                </div>
              </div>

              <!-- Gallery -->
              <div
                v-if="company.gallery?.length"
                class="border-t border-slate-100 pt-5"
              >
                <h3 class="mb-3 font-semibold text-slate-900">
                  Company Gallery
                </h3>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                  <img
                    v-for="(image, index) in company.gallery"
                    :key="index"
                    :src="image"
                    :alt="`${company.name} gallery image ${index + 1}`"
                    class="h-32 w-full rounded-lg object-cover"
                  />
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="mt-7 flex justify-end border-t border-slate-100 pt-5">
              <button
                type="button"
                @click="closePreview"
                class="rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </Teleport>
    </template>

    <!-- No Company -->
    <div v-else class="mx-auto max-w-7xl px-4 py-16 text-center">
      <p class="text-sm text-slate-500">No company profile found.</p>
    </div>
  </main>
</template>
