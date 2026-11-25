<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import type { Company } from "../types/company.types";
import type { Job } from "@/features/jobs/types/job.types";
import JobCard from "@/features/jobs/components/JobCard.vue";

defineProps<{
  jobs: Job[];
  company?: Company;
}>();

const emit = defineEmits<{
  selectJob: [job: Job];
  viewAllJobs: [];
}>();
</script>

<template>
  <section class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold text-gray-900">
        Latest Jobs From {{ company?.name || "  " }}
      </h2>
      <!-- <button
        type="button"
        class="text-sm font-semibold text-blue-600 hover:underline"
        @click="emit('viewAllJobs')"
      >
        View All Jobs
      </button> -->

      <RouterLink
        to="/employer/jobs"
        class="inline-flex items-center gap-2 rounded-xl bg-[#102D59] px-5 py-3 text-sm font-semibold text-white shadow-md shadow-blue-900/10 transition-all duration-300 hover:bg-blue-600 hover:shadow-lg"
      >
        View All Jobs
      </RouterLink>
    </div>

    <!-- Job Cards List -->
    <div v-if="jobs.length" class="space-y-4">
      <JobCard v-for="job in jobs" :key="job.id" :job="job" />
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="flex items-stretch justify-center rounded-md border border-gray-100 bg-white p-8 text-center"
    >
      <div
        class="px-5 py-6 w-full flex flex-col justify-center items-center border-2 border-dashed border-gray-200 rounded-md"
      >
        <h3 class="font-semibold text-gray-800">No open positions</h3>
        <p class="mt-1 text-sm text-gray-500">
          There are currently no available jobs at this company.
        </p>
      </div>
    </div>
  </section>
</template>
