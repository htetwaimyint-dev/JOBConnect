<script setup lang="ts">
import { ref } from "vue";
import BaseCheckbox from "../BaseCheckbox.vue";

const checked = ref(true);
const disabled = ref(false);
const required = ref(false);
const error = ref(false);
</script>

<template>
    <div class="space-y-8 p-6">
        <h1 class="text-2xl font-bold">BaseCheckbox Demo</h1>

        <!-- Sizes -->
        <section>
            <h2 class="mb-3 font-semibold">Sizes</h2>
            <div class="space-y-3">
                <BaseCheckbox size="sm" label="Small checkbox" />
                <BaseCheckbox size="md" label="Medium checkbox" />
                <BaseCheckbox size="lg" label="Large checkbox" />
            </div>
        </section>

        <!-- States -->
        <section>
            <h2 class="mb-3 font-semibold">States</h2>
            <div class="space-y-3">
                <BaseCheckbox label="Unchecked" />
                <BaseCheckbox :model-value="true" label="Checked" />
                <BaseCheckbox disabled label="Disabled" />
                <BaseCheckbox
                    disabled
                    :model-value="true"
                    label="Disabled checked"
                />
            </div>
        </section>

        <!-- Description -->
        <section>
            <h2 class="mb-3 font-semibold">Description</h2>
            <BaseCheckbox
                label="Email notifications"
                description="Receive updates and notifications via email."
            />
        </section>

        <!-- Required -->
        <section>
            <h2 class="mb-3 font-semibold">Required</h2>
            <BaseCheckbox label="Accept terms and conditions" required />
        </section>

        <!-- Error -->
        <section>
            <h2 class="mb-3 font-semibold">Error</h2>
            <BaseCheckbox
                label="Accept terms and conditions"
                error="You must accept the terms and conditions."
            />
        </section>

        <!-- Interactive -->
        <section>
            <h2 class="mb-3 font-semibold">Interactive Example</h2>
            <BaseCheckbox
                v-model="checked"
                label="Enable notifications"
                description="Toggle this checkbox to see it in action."
            />
            <p class="mt-2 text-xs text-gray-500">Value: {{ checked }}</p>
        </section>
    </div>
</template>
