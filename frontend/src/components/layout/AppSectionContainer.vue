<script setup lang="ts">
withDefaults(
  defineProps<{
    width?: "default" | "wide" | "compact";
  }>(),
  {
    width: "default",
  },
);

const widthClasses = {
  default: "max-w-7xl",
  wide: "max-w-[1400px]",
  compact: "max-w-4xl",
};
</script>

<template>
  <div
    class="mx-auto w-full min-w-0 px-4 sm:px-6 lg:px-8"
    :class="widthClasses[width]"
  >
    <slot />
  </div>
</template>
