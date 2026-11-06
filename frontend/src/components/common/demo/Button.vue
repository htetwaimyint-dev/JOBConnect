<script setup lang="ts">
import BaseButton from "../BaseButton.vue";

const colors = [
    "primary",
    "neutral",
    "danger",
    "success",
    "warning",
    "light",
    "dark",
] as const;

const variants = ["solid", "soft", "outline", "ghost"] as const;
const sizes = ["20", "24", "28", "32", "36", "40", "44"] as const;
</script>

<template>
    <div class="space-y-8 p-6">
        <h1 class="text-2xl font-bold">BaseButton Demo</h1>

        <!-- Variants -->
        <section>
            <h2 class="mb-3 font-semibold">Variants</h2>
            <div class="flex flex-wrap gap-3">
                <BaseButton
                    v-for="variant in variants"
                    :key="variant"
                    :variant="variant"
                >
                    {{ variant }}
                </BaseButton>
            </div>
        </section>

        <!-- Colors -->
        <section>
            <h2 class="mb-3 font-semibold">Colors</h2>
            <div class="flex flex-wrap gap-3">
                <BaseButton v-for="color in colors" :key="color" :color="color">
                    {{ color }}
                </BaseButton>
            </div>
        </section>

        <!-- Variants by color -->
        <section>
            <h2 class="mb-3 font-semibold">All Combinations</h2>
            <div
                v-for="variant in variants"
                :key="variant"
                class="mb-4 space-y-2"
            >
                <p class="text-xs font-medium uppercase text-neutral-400">
                    {{ variant }}
                </p>
                <div class="flex flex-wrap gap-2">
                    <BaseButton
                        v-for="color in colors"
                        :key="color"
                        :color="color"
                        :variant="variant"
                    >
                        {{ color }}
                    </BaseButton>
                </div>
            </div>
        </section>

        <!-- Sizes -->
        <section>
            <h2 class="mb-3 font-semibold">Sizes</h2>
            <div class="flex flex-wrap items-center gap-3">
                <BaseButton v-for="size in sizes" :key="size" :size="size">
                    {{ size }}
                </BaseButton>
            </div>
        </section>

        <!-- States -->
        <section>
            <h2 class="mb-3 font-semibold">States</h2>
            <div class="flex flex-wrap gap-3">
                <BaseButton>Default</BaseButton>
                <BaseButton active>Active</BaseButton>
                <BaseButton disabled>Disabled</BaseButton>
                <BaseButton loading>Loading</BaseButton>
            </div>
        </section>

        <!-- Types -->
        <section>
            <h2 class="mb-3 font-semibold">Types</h2>
            <div class="flex flex-wrap gap-3">
                <BaseButton type="button">BaseButton</BaseButton>
                <BaseButton type="submit">Submit</BaseButton>
                <BaseButton type="link" to="/">Link</BaseButton>
            </div>
        </section>
    </div>
</template>
