<script setup lang="ts">
import { ref } from "vue";
import BaseInput from "./BaseInput.vue";

defineOptions({
    inheritAttrs: false,
});

type InputSize = "sm" | "md" | "lg";
type InputVariant = "outline" | "soft" | "ghost" | "filled" | "underline";

interface Props {
    id?: string;
    label?: string;
    size?: InputSize;
    variant?: InputVariant;
    placeholder?: string;
    hint?: string;
    error?: string;
    disabled?: boolean;
    readonly?: boolean;
    required?: boolean;
}

withDefaults(defineProps<Props>(), {
    size: "md",
    variant: "outline",
    disabled: false,
    readonly: false,
    required: false,
});

const model = defineModel<string>({
    default: "",
});

const visible = ref(false);

function toggleVisibility() {
    visible.value = !visible.value;
}
</script>

<template>
    <BaseInput
        v-bind="$attrs"
        v-model="model"
        :id="id"
        :type="visible ? 'text' : 'password'"
        :label="label"
        :size="size"
        :variant="variant"
        :placeholder="placeholder"
        :hint="hint"
        :error="error"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
    >
        <template #trailing>
            <button
                type="button"
                class="flex items-center justify-center text-gray-400 transition-colors hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded"
                :disabled="disabled || readonly"
                :aria-label="visible ? 'Hide password' : 'Show password'"
                :aria-pressed="visible"
                @click="toggleVisibility"
            >
                <!-- Eye icon -->
                <svg
                    v-if="!visible"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"
                    />
                    <circle cx="12" cy="12" r="3" />
                </svg>

                <!-- Eye off icon -->
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a15.7 15.7 0 0 1-3.2 3.9M6.2 6.2C3.65 8.1 2.25 12 2.25 12s3.5 7 9.75 7c1.2 0 2.3-.25 3.3-.65"
                    />
                </svg>
            </button>
        </template>
    </BaseInput>
</template>
