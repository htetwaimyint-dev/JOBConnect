<script setup lang="ts">
import { ref } from "vue";
import BasePasswordInput from "../BasePasswordInput.vue";

const password = ref("mySecret123");
const variants = ["outline", "soft", "ghost", "filled", "underline"] as const;
const sizes = ["sm", "md", "lg"] as const;
</script>

<template>
    <div class="space-y-8 p-6">
        <h1 class="text-xl font-semibold">Password Input</h1>

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
                <BasePasswordInput
                    :variant="variant"
                    :placeholder="`${variant} password`"
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
                <BasePasswordInput :size="size" placeholder="Enter password" />
            </div>
        </section>

        <!-- States -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">States</h2>

            <div class="max-w-md space-y-3">
                <BasePasswordInput
                    label="Disabled"
                    model-value="password123"
                    disabled
                />

                <BasePasswordInput
                    label="Readonly"
                    model-value="password123"
                    readonly
                />

                <BasePasswordInput
                    label="Required"
                    placeholder="Enter password"
                    required
                />

                <BasePasswordInput
                    label="With hint"
                    placeholder="Enter password"
                    hint="Use at least 8 characters."
                />

                <BasePasswordInput
                    label="With error"
                    model-value="123"
                    error="Password must be at least 8 characters."
                />
            </div>
        </section>

        <!-- Interactive -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">
                Interactive Example
            </h2>

            <div class="max-w-md space-y-2">
                <BasePasswordInput
                    v-model="password"
                    label="Your password"
                    placeholder="Enter your password"
                    hint="Click the eye icon to show or hide your password."
                />

                <p class="text-sm text-gray-500">
                    Password length: {{ password.length }}
                </p>
            </div>
        </section>
    </div>
</template>
