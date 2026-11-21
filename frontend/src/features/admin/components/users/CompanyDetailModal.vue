<script setup lang="ts">
import { X } from "lucide-vue-next";
import type { Employer } from "../../types/user.types";
defineProps<{ employer: Employer | null }>();
const emit = defineEmits<{ (e: "close"): void }>();
</script>
<template>
  <div
    v-if="employer"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
    @click.self="emit('close')"
  >
    <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl sm:p-6">
      <div class="mb-5 flex items-start justify-between">
        <h2 class="text-lg font-bold text-slate-900">Company Details</h2>
        <button
          class="rounded-lg p-1 text-slate-500 hover:bg-slate-100"
          @click="emit('close')"
        >
          <X class="h-5 w-5" />
        </button>
      </div>
      <div class="flex items-center gap-4">
        <img
          :src="employer.companyLogo"
          class="h-16 w-16 rounded-xl border border-slate-200 object-cover"
          alt="Company logo"
        />
        <div>
          <h3 class="font-semibold text-slate-900">{{ employer.company }}</h3>
          <p class="text-sm text-slate-500">{{ employer.email }}</p>
        </div>
      </div>
      <div class="mt-5 grid grid-cols-2 gap-3">
        <div class="rounded-lg bg-slate-50 p-3">
          <p class="text-xs text-slate-500">Jobs Posted</p>
          <p class="mt-1 text-xl font-bold text-slate-900">
            {{ employer.jobCount }}
          </p>
        </div>
        <div class="rounded-lg bg-slate-50 p-3">
          <p class="text-xs text-slate-500">Account Status</p>
          <p
            class="mt-1 font-semibold"
            :class="employer.isDeleted ? 'text-red-600' : 'text-emerald-600'"
          >
            {{ employer.isDeleted ? "Suspended" : "Active" }}
          </p>
        </div>
      </div>
      <p class="mt-4 text-sm text-slate-500">
        Joined {{ employer.joinedDate }}
      </p>
      <button
        class="mt-5 w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
        @click="emit('close')"
      >
        Close
      </button>
    </div>
  </div>
</template>
