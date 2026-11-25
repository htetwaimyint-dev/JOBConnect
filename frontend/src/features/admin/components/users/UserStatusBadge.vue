<script setup lang="ts">
const props = defineProps<{ isDeleted: boolean }>();
</script>
<template>
  <span
    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
    :class="
      props.isDeleted
        ? 'bg-red-100 text-red-700'
        : 'bg-emerald-100 text-emerald-700'
    "
    >{{ props.isDeleted ? "Suspended" : "Active" }}</span
  >
</template>
