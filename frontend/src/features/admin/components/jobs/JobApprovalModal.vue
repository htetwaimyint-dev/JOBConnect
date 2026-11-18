<script setup lang="ts">
import { CheckCircle2, XCircle, LoaderCircle, X } from "lucide-vue-next";

import type { Job } from "../../types/job-approval.types";

type ApprovalAction = "published" | "rejected";

defineProps<{
  job: Job | null;
  action: ApprovalAction | null;
  rejectionReason: string;
  submitting: boolean;
}>();

const emit = defineEmits<{
  close: [];
  confirm: [];
  "update:rejectionReason": [value: string];
}>();
</script>

<template>
  <Teleport to="body">
    <div
      v-if="job && action"
      class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
      @click.self="emit('close')"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-xl"
            :class="
              action === 'published'
                ? 'bg-emerald-50 text-emerald-600'
                : 'bg-red-50 text-red-600'
            "
          >
            <CheckCircle2 v-if="action === 'published'" :size="24" />
            <XCircle v-else :size="24" />
          </div>

          <button
            type="button"
            @click="emit('close')"
            :disabled="submitting"
            class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 disabled:opacity-50"
          >
            <X :size="20" />
          </button>
        </div>

        <!-- Content -->
        <h3 class="mt-5 text-lg font-bold text-slate-900">
          {{ action === "published" ? "Approve Job?" : "Reject Job?" }}
        </h3>

        <p class="mt-2 text-sm leading-6 text-slate-500">
          {{
            action === "published"
              ? `Are you sure you want to approve "${job.title}"? This job will be published.`
              : `Please provide a reason for rejecting "${job.title}".`
          }}
        </p>

        <!-- Rejection Reason -->
        <div v-if="action === 'rejected'" class="mt-4">
          <label class="mb-2 block text-sm font-semibold text-slate-700">
            Rejection Reason
            <span class="text-red-500">*</span>
          </label>

          <textarea
            :value="rejectionReason"
            @input="
              emit(
                'update:rejectionReason',
                ($event.target as HTMLTextAreaElement).value,
              )
            "
            rows="4"
            maxlength="1000"
            placeholder="Explain why this job is being rejected..."
            :disabled="submitting"
            class="w-full resize-none rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
          ></textarea>

          <p class="mt-1 text-right text-xs text-slate-400">
            {{ rejectionReason.length }}/1000
          </p>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-3">
          <button
            type="button"
            @click="emit('close')"
            :disabled="submitting"
            class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
          >
            Cancel
          </button>

          <button
            type="button"
            @click="emit('confirm')"
            :disabled="
              submitting || (action === 'rejected' && !rejectionReason.trim())
            "
            class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-50"
            :class="
              action === 'published'
                ? 'bg-emerald-600 hover:bg-emerald-700'
                : 'bg-red-600 hover:bg-red-700'
            "
          >
            <LoaderCircle v-if="submitting" :size="17" class="animate-spin" />

            {{
              submitting
                ? "Processing..."
                : action === "published"
                  ? "Confirm Approval"
                  : "Confirm Rejection"
            }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
