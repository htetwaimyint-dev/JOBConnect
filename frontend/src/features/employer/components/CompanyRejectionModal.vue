<script setup lang="ts">
import { X, AlertCircle, FilePenLine, ShieldAlert } from "lucide-vue-next";

defineProps<{
  reason: string;
}>();

const emit = defineEmits<{
  close: [];
  edit: [];
}>();
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/50 px-4 py-6 backdrop-blur-sm"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl"
      >
        <!-- Header -->
        <div class="border-b border-slate-100 bg-blue-50/80 px-6 py-5">
          <div class="flex items-start gap-3">
            <div
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
            >
              <ShieldAlert :size="23" />
            </div>

            <div class="flex-1 pt-0.5">
              <h2 class="text-base font-bold text-slate-900">
                Company Verification
              </h2>

              <p class="mt-1 text-xs font-medium text-blue-600">
                Verification Rejected
              </p>
            </div>

            <button
              type="button"
              @click="emit('close')"
              class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-100 hover:text-slate-700"
              aria-label="Close modal"
            >
              <X :size="20" />
            </button>
          </div>

          <p class="mt-4 text-sm leading-6 text-slate-600">
            Your company profile needs some changes before it can be approved.
            Please review the feedback below.
          </p>
        </div>

        <!-- Body -->
        <div class="px-6 py-3">
          <!-- Rejection Reason -->
          <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 p-4">
            <div class="mb-2 flex items-center gap-2">
              <AlertCircle :size="17" class="text-rose-600" />

              <p
                class="text-xs font-bold uppercase tracking-wider text-rose-700"
              >
                Rejection Reason
              </p>
            </div>

            <p
              class="whitespace-pre-line break-words text-sm leading-6 text-slate-700"
            >
              {{ reason }}
            </p>
          </div>

          <p class="mt-4 text-xs leading-5 text-slate-500">
            Please review the feedback, update your company profile, and submit
            it again for verification.
          </p>
        </div>

        <!-- Footer -->
        <div
          class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/80 px-6 py-4 sm:flex-row sm:justify-end"
        >
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
          >
            Close
          </button>

          <button
            type="button"
            @click="emit('edit')"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
          >
            <FilePenLine :size="16" />
            Edit Company Profile
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
