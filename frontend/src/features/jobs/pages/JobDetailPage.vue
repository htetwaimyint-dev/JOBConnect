<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import type { Company } from "@/features/employer/types/company.types";
import type { Job } from "../types/job.types";
defineProps<{
  job: Job;
  company: Company;
  similarJobs?: Job[];
  otherCompanyJobs?: Job[];
  isBookmarked?: boolean;
}>();

const emit = defineEmits<{
  apply: [];
  bookmark: [];
  share: [];
  selectJob: [job: Job];
  close: [];
}>();
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
  >
    <div
      class="relative w-full max-w-5xl rounded-2xl bg-[#f7f7fb] shadow-xl max-h-[90vh] overflow-y-auto"
    >
      <!-- Modal Close Button -->
      <button
        type="button"
        class="absolute top-4 right-4 z-10 p-2 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition"
        @click="emit('close')"
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
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>

      <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <!-- Left Main Column: Job Details Content -->
          <div
            class="lg:col-span-8 bg-white rounded-xl border border-gray-100 p-6 sm:p-8 shadow-xs space-y-6"
          >
            <!-- Top Header: Job Title & Actions -->
            <div
              class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100"
            >
              <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                  {{ job.title }}
                </h1>
              </div>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  class="p-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition"
                  @click="emit('bookmark')"
                  title="Bookmark"
                >
                  <svg
                    class="w-5 h-5"
                    :class="
                      isBookmarked
                        ? 'text-blue-600 fill-current'
                        : 'text-gray-500'
                    "
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
                  @click="emit('share')"
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
                <BaseButton
                  type="button"
                  class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition"
                  @click="emit('apply')"
                >
                  Apply Now
                </BaseButton>
              </div>
            </div>

            <!-- Company Sub-header & Badges -->
            <div class="flex items-center gap-4">
              <img
                :src="company.logo ?? ''"
                :alt="`${company.name} logo`"
                class="h-14 w-14 rounded-xl border border-gray-100 object-cover bg-white shadow-xs shrink-0"
              />
              <div>
                <h3 class="text-sm font-bold text-gray-900">
                  {{ company.name }} •
                  <span class="font-normal text-gray-500">{{
                    job.location || company.address
                  }}</span>
                </h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span
                    class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                  >
                    {{ job.employment_type || "Fulltime" }}
                  </span>
                  <span
                    class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                  >
                    {{ job.workplace_type || "Remote" }}
                  </span>
                  <!-- <span
                                        class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                    >
                                        {{ job.experienceLevel || "2-4 Years" }}
                                    </span> -->
                </div>
              </div>
            </div>

            <!-- About This Role -->
            <div class="space-y-2 pt-2">
              <h4 class="text-base font-bold text-gray-900">About this role</h4>
              <p class="text-sm text-gray-600 leading-relaxed">
                {{
                  job.description ||
                  `As an ${job.title} on ${company.name}, you'll focus on design user-friendly on several platform (web, mobile, dashboard, etc) to our users needs. Your innovative solution will enhance the user experience on several platforms. Join us and let's making impact on user engagement at ${company.name}.`
                }}
              </p>
            </div>

            <!-- Qualification -->
            <div class="space-y-3 pt-2">
              <h4 class="text-base font-bold text-gray-900">Qualification</h4>
              <ul
                class="list-disc list-inside text-sm text-gray-600 space-y-2 leading-relaxed"
              >
                <template>
                  <li>
                    At least 2-4 years of relevant experience in product design
                    or related roles.
                  </li>
                  <li>
                    Knowledge of design validation, either through quantitative
                    or qualitative research.
                  </li>
                  <li>Have good knowledge using Figma and Figjam.</li>
                  <li>
                    Experience with analytics tools to gather data from users.
                  </li>
                </template>
              </ul>
            </div>

            <!-- Responsibility -->
            <div class="space-y-3 pt-2">
              <h4 class="text-base font-bold text-gray-900">Responsibility</h4>
              <ul
                class="list-disc list-inside text-sm text-gray-600 space-y-2 leading-relaxed"
              >
                <template v-if="job.responsibilities?.length">
                  <li v-for="(resp, idx) in job.responsibilities" :key="idx">
                    {{ resp }}
                  </li>
                </template>
                <template v-else>
                  <li>
                    Create design and user journey on every features and
                    product/business units across multiples devices (Web+App).
                  </li>
                  <li>
                    Identifying design problems through user journey and
                    devising elegant solutions.
                  </li>
                  <li>
                    Develop low and hi fidelity designs, user experience flow, &
                    prototype, translate it into highly-polished visual
                    composites following style and brand guidelines.
                  </li>
                  <li>
                    Brainstorm and works together with Design Lead, UX
                    Engineers, and PMs to execute a design sprint on specific
                    story or task.
                  </li>
                </template>
              </ul>
            </div>
          </div>

          <!-- Right Sidebar: Similar Jobs & Other Jobs -->
          <div class="lg:col-span-4 space-y-6">
            <!-- Similar Jobs -->
            <div
              class="bg-white rounded-xl border border-gray-100 p-5 shadow-xs space-y-4"
            >
              <h3 class="text-base font-bold text-gray-900">Similar Jobs</h3>

              <div class="space-y-4">
                <div
                  v-for="simJob in [
                    {
                      id: 101,
                      title: 'Lead UI Designer',
                      company: 'Gojek',
                      location: 'Jakarta, Indonesia',
                      type: 'Fulltime',
                      workplace: 'Onsite',
                      exp: '3-5 Years',
                      posted: '2 day ago',
                      applicants: 521,
                      logoBg: 'bg-green-600',
                    },
                    {
                      id: 102,
                      title: 'Sr. UX Designer',
                      company: 'GoPay',
                      location: 'Jakarta, Indonesia',
                      type: 'Fulltime',
                      workplace: 'Onsite',
                      exp: '3-5 Years',
                      posted: '2 day ago',
                      applicants: 210,
                      logoBg: 'bg-blue-600',
                    },
                    {
                      id: 103,
                      title: 'Jr. UI Designer',
                      company: 'OVO',
                      location: 'Jakarta, Indonesia',
                      type: 'Fulltime',
                      workplace: 'Onsite',
                      exp: '1-3 Years',
                      posted: 'an hour ago',
                      applicants: 120,
                      logoBg: 'bg-purple-600',
                    },
                  ]"
                  :key="simJob.id"
                  class="p-4 rounded-lg border border-gray-100 hover:border-gray-200 transition cursor-pointer space-y-3"
                  @click="emit('selectJob', simJob as any)"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div>
                      <h4 class="text-sm font-bold text-gray-900">
                        {{ simJob.title }}
                      </h4>
                      <p class="text-xs text-gray-500 mt-0.5">
                        {{ simJob.company }} •
                        {{ simJob.location }}
                      </p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg
                        class="w-4 h-4"
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
                  </div>

                  <div class="flex flex-wrap gap-1.5">
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ simJob.type }}</span
                    >
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ simJob.workplace }}</span
                    >
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ simJob.exp }}</span
                    >
                  </div>

                  <div
                    class="text-[11px] text-gray-400 pt-2 border-t border-gray-100"
                  >
                    {{ simJob.posted }} • {{ simJob.applicants }} Applicants
                  </div>
                </div>
              </div>
            </div>

            <!-- Other Jobs From Company -->
            <div
              class="bg-white rounded-xl border border-gray-100 p-5 shadow-xs space-y-4"
            >
              <h3 class="text-base font-bold text-gray-900">
                Other Jobs From {{ company.name }}
              </h3>

              <div class="space-y-4">
                <div
                  v-for="otherJob in [
                    {
                      id: 201,
                      title: 'UI Designer',
                      company: company.name,
                      location: company.address,
                      type: 'Internship',
                      workplace: 'Onsite',
                      exp: 'Fresh Graduate',
                      posted: 'a day ago',
                      applicants: 35,
                    },
                    {
                      id: 202,
                      title: 'Fronted End Developer',
                      company: company.name,
                      location: company.address,
                      type: 'Full Time',
                      workplace: 'Onsite',
                      exp: '1-3 Years',
                      posted: 'a day ago',
                      applicants: 35,
                    },
                  ]"
                  :key="otherJob.id"
                  class="p-4 rounded-lg border border-gray-100 hover:border-gray-200 transition cursor-pointer space-y-3"
                  @click="emit('selectJob', otherJob as any)"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div>
                      <h4 class="text-sm font-bold text-gray-900">
                        {{ otherJob.title }}
                      </h4>
                      <p class="text-xs text-gray-500 mt-0.5">
                        {{ otherJob.company }} •
                        {{ otherJob.location }}
                      </p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg
                        class="w-4 h-4"
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
                  </div>

                  <div class="flex flex-wrap gap-1.5">
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ otherJob.type }}</span
                    >
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ otherJob.workplace }}</span
                    >
                    <span
                      class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                      >{{ otherJob.exp }}</span
                    >
                  </div>

                  <div
                    class="text-[11px] text-gray-400 pt-2 border-t border-gray-100"
                  >
                    {{ otherJob.posted }} • {{ otherJob.applicants }} Applicants
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
