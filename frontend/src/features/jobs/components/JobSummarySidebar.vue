<script setup lang="ts">
import { useAuthStore } from "@/stores/auth.store";
import type { Job, JobDetailMode, JobFormData } from "../types/job.types";

import { formatJobLabel } from "@/utils/jobs.utils";
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
  job: Job | JobFormData;
  mode: JobDetailMode;
  categoryName?: string;
}>();

const emit = defineEmits<{
  edit: [];
}>();

function handleClick() {
  emit("edit");
}

const authstore = useAuthStore();
const role = authstore.user?.role;
</script>

<template>
  <aside class="lg:col-span-4 space-y-5">
    <!-- Job Summary -->
    <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-xs">
      <h3 class="text-base font-bold text-gray-900">Job Summary</h3>

      <div class="mt-5 space-y-4">
        <div>
          <p class="text-xs text-gray-500">Job Title</p>
          <p class="mt-1 text-sm font-medium text-gray-900">
            {{ job.title || "Not specified" }}
          </p>
        </div>

        <div>
          <p class="text-xs text-gray-500">Category</p>
          <p class="mt-1 text-sm font-medium text-gray-900">
            {{ categoryName || "Not specified" }}
          </p>
        </div>

        <div>
          <p class="text-xs text-gray-500">Workplace</p>
          <p class="mt-1 text-sm font-medium text-gray-900">
            {{ formatJobLabel(job.workplace_type) || "Not specified" }}
          </p>
        </div>

        <div>
          <p class="text-xs text-gray-500">Location</p>
          <p class="mt-1 text-sm font-medium text-gray-900">
            {{
              [job.city, job.country].filter(Boolean).join(", ") ||
              "Not specified"
            }}
          </p>
        </div>

        <div>
          <p class="text-xs text-gray-500">Application Deadline</p>
          <p class="mt-1 text-sm font-medium text-gray-900">
            {{ job.deadline || "Not specified" }}
          </p>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div v-if="mode !== 'admin'" class="sticky top-24 z-10">
      <!-- Employer Actions -->
      <div v-if="role === 'employer'" class="space-y-4">
        <div v-if="mode === 'preview'">
          <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
            <h3 class="text-sm font-bold text-blue-900">Preview Mode</h3>

            <p class="mt-2 text-xs leading-5 text-blue-700">
              This is a preview of your job listing. Your changes will not be
              published until you submit the job for approval.
            </p>
          </div>

          <BaseButton
            class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700"
          >
            Back to Editing
          </BaseButton>
        </div>

        <div v-else>
          <BaseButton
            class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700"
            @click="handleClick"
          >
            Edit Job
          </BaseButton>
        </div>
      </div>

      <!-- Candidate/Public Apply Button -->
      <div v-else>
        <BaseButton
          class="w-full bg-gray-900 px-5 py-2.5 text-[15px]! font-semibold text-white hover:bg-gray-700"
        >
          Apply Now
        </BaseButton>
      </div>
    </div>
  </aside>
</template>
