<script setup lang="ts">
import { Users, UserCheck, UserX } from "lucide-vue-next";
defineProps<{
  total: number;
  active: number;
  suspended: number;
  noun: string;
}>();
</script>
<template>
  <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div
      v-for="card in [
        {
          label: `Total ${noun}`,
          value: total,
          icon: Users,
          iconClass: 'bg-blue-50 text-blue-600',
        },
        {
          label: `Active ${noun}`,
          value: active,
          icon: UserCheck,
          iconClass: 'bg-emerald-50 text-emerald-600',
        },
        {
          label: `Suspended ${noun}`,
          value: suspended,
          icon: UserX,
          iconClass: 'bg-slate-100 text-slate-600',
        },
      ]"
      :key="card.label"
      class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"
    >
      <div class="flex items-center gap-3 sm:gap-4">
        <div
          class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
          :class="card.iconClass"
        >
          <component :is="card.icon" class="h-5 w-5" />
        </div>
        <div>
          <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
          <p class="text-2xl font-bold text-slate-900">{{ card.value }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
