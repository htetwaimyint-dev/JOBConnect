<script setup lang="ts">
import { ref } from "vue";
import BaseSelect from "../BaseSelect.vue";

const options = [
    { label: "Apple", value: "apple" },
    { label: "Banana", value: "banana" },
    { label: "Orange", value: "orange" },
    { label: "Mango", value: "mango" },
];

const optionsWithDisabled = [
    ...options,
    { label: "Grapes (Unavailable)", value: "grapes", disabled: true },
];

const variants = ["outline", "soft", "ghost"] as const;
const sizes = ["sm", "md", "lg"] as const;

const selectedFruit = ref<string | number | null>("apple");
const selectedCountry = ref<string | number | null>(null);
</script>

<template>
    <div class="space-y-8 p-6">
        <h1 class="text-xl font-semibold">BaseSelect</h1>

        <!-- Variants -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Variants</h2>

            <div
                v-for="variant in variants"
                :key="variant"
                class="max-w-md space-y-1"
            >
                <p class="text-xs font-medium capitalize text-gray-500">
                    {{ variant }}
                </p>
                <BaseSelect
                    :variant="variant"
                    :options="options"
                    placeholder="Choose a fruit"
                />
            </div>
        </section>

        <!-- Sizes -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Sizes</h2>

            <div v-for="size in sizes" :key="size" class="max-w-md space-y-1">
                <p class="text-xs font-medium uppercase text-gray-500">
                    {{ size }}
                </p>
                <BaseSelect
                    :size="size"
                    :options="options"
                    placeholder="Choose a fruit"
                />
            </div>
        </section>

        <!-- States -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">States</h2>

            <div class="max-w-md space-y-3">
                <BaseSelect
                    label="Disabled"
                    :options="options"
                    model-value="apple"
                    disabled
                />

                <BaseSelect
                    label="Required"
                    :options="options"
                    placeholder="Choose a fruit"
                    required
                />

                <BaseSelect
                    label="With hint"
                    :options="options"
                    placeholder="Choose a fruit"
                    hint="BaseSelect your favorite fruit."
                />

                <BaseSelect
                    label="With error"
                    :options="options"
                    placeholder="Choose a fruit"
                    error="Please select a fruit."
                />
            </div>
        </section>

        <!-- Options -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Options</h2>

            <div class="max-w-md space-y-3">
                <BaseSelect
                    label="Options with disabled item"
                    :options="optionsWithDisabled"
                    placeholder="Choose a fruit"
                />
            </div>
        </section>

        <!-- Interactive -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">
                Interactive Example
            </h2>

            <div class="max-w-md space-y-3">
                <BaseSelect
                    v-model="selectedFruit"
                    label="Favorite fruit"
                    :options="options"
                    placeholder="Choose a fruit"
                    hint="BaseSelect an option to update the value."
                />

                <p class="text-sm text-gray-500">
                    Selected value:
                    <span class="font-medium text-gray-900">
                        {{ selectedFruit ?? "None" }}
                    </span>
                </p>
            </div>
        </section>
    </div>
</template>
