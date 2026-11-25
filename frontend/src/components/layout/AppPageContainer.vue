<script setup lang="ts">
defineProps<{
    width?: "default" | "wide" | "full";
}>();

const widthClasses = {
    default: "max-w-7xl",
    wide: "max-w-[1600px]",
    full: "max-w-none",
};
</script>

<template>
    <main
        class="w-full min-w-0 px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8 has-[.screen-locked]:min-h-0 has-[.screen-locked]:flex-1 has-[.screen-locked]:overflow-hidden"
    >
        <div
            class="mx-auto w-full min-w-0 has-[.screen-locked]:flex has-[.screen-locked]:h-full has-[.screen-locked]:min-h-0 has-[.screen-locked]:flex-col"
            :class="widthClasses[width ?? 'default']"
        >
            <slot />
        </div>
    </main>
</template>
