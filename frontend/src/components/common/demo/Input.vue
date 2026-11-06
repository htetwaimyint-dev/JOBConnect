<script setup lang="ts">
import { ref } from "vue";
import BaseInput from "../BaseInput.vue";

const value = ref("");
const clearValue = ref("Hello world");
const password = ref("password");
</script>

<template>
    <div class="max-w-2xl space-y-8 p-6">
        <h1 class="text-2xl font-bold">BaseInput Demo</h1>

        <!-- Variants -->
        <section>
            <h2 class="mb-3 font-semibold">Variants</h2>

            <div class="space-y-5">
                <BaseInput
                    variant="outline"
                    label="Outline"
                    placeholder="Enter your text..."
                />

                <BaseInput
                    variant="soft"
                    label="Soft"
                    placeholder="Enter your text..."
                />

                <BaseInput
                    variant="ghost"
                    label="Ghost"
                    placeholder="Enter your text..."
                />

                <BaseInput
                    variant="filled"
                    label="Filled"
                    placeholder="Enter your text..."
                />

                <BaseInput
                    variant="underline"
                    label="Underline"
                    placeholder="Enter your text..."
                />
            </div>
        </section>

        <!-- Sizes -->
        <section>
            <h2 class="mb-3 font-semibold">Sizes</h2>

            <div class="space-y-3">
                <BaseInput size="sm" placeholder="Small input" />
                <BaseInput size="md" placeholder="Medium input" />
                <BaseInput size="lg" placeholder="Large input" />
            </div>
        </section>

        <!-- Types -->
        <section>
            <h2 class="mb-3 font-semibold">Types</h2>

            <div class="space-y-3">
                <BaseInput type="text" placeholder="Text input" />
                <BaseInput type="email" placeholder="Email address" />
                <BaseInput
                    v-model="password"
                    type="password"
                    placeholder="Password"
                />
                <BaseInput type="number" placeholder="Number input" />
                <BaseInput type="search" placeholder="Search input" />
            </div>
        </section>

        <!-- States -->
        <section>
            <h2 class="mb-3 font-semibold">States</h2>

            <div class="space-y-3">
                <BaseInput placeholder="Default" />
                <BaseInput placeholder="Disabled" disabled />
                <BaseInput model-value="Readonly value" readonly />
                <BaseInput placeholder="Required" required />
                <BaseInput
                    placeholder="Error state"
                    error="Please enter a valid value."
                />
            </div>
        </section>

        <!-- Label & Hint -->
        <section>
            <h2 class="mb-3 font-semibold">Label & Hint</h2>

            <BaseInput
                label="Username"
                placeholder="Enter username"
                hint="Your username must be unique."
            />

            <div class="mt-4">
                <BaseInput
                    label="Email address"
                    placeholder="Enter email"
                    required
                />
            </div>
        </section>

        <!-- Clearable -->
        <section>
            <h2 class="mb-3 font-semibold">Clearable</h2>

            <BaseInput
                v-model="clearValue"
                label="Search"
                placeholder="Search..."
                clearable
            />
        </section>

        <!-- Icons -->
        <section>
            <h2 class="mb-3 font-semibold">Leading & Trailing</h2>

            <div class="space-y-3">
                <BaseInput placeholder="Search...">
                    <template #leading>
                        <span>⌕</span>
                    </template>
                </BaseInput>

                <BaseInput placeholder="Enter amount">
                    <template #trailing>
                        <span>USD</span>
                    </template>
                </BaseInput>
            </div>
        </section>

        <!-- Interactive -->
        <section>
            <h2 class="mb-3 font-semibold">Interactive</h2>

            <BaseInput
                v-model="value"
                label="Your input"
                placeholder="Type something..."
            />

            <p class="mt-2 text-xs text-gray-500">
                Value: {{ value || "Empty" }}
            </p>
        </section>
    </div>
</template>
