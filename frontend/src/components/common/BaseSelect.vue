<script setup lang="ts">
import { computed, useAttrs } from "vue";

defineOptions({
    inheritAttrs: false,
});

type SelectSize = "sm" | "md" | "lg";
type SelectVariant = "outline" | "soft" | "ghost";

interface SelectOption {
    label: string;
    value: string | number;
    disabled?: boolean;
}

interface Props {
    id?: string;
    label?: string;
    options: SelectOption[];
    placeholder?: string;
    size?: SelectSize;
    variant?: SelectVariant;
    hint?: string;
    error?: string;
    disabled?: boolean;
    readonly?: boolean;
    required?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: "md",
    variant: "outline",
    placeholder: "Select an option",
    disabled: false,
    readonly: false,
    required: false,
});

const model = defineModel<string | number | null>({
    default: null,
});

const attrs = useAttrs();

const selectClasses = computed(() => [
    "w-full appearance-none border bg-white",
    !model.value && model.value !== 0 ? "text-gray-400" : "text-gray-900",
    "transition-colors duration-150",
    "focus:outline-none focus:ring-2 focus:ring-offset-0",
    "disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500",
    {
        "h-9 pl-3 pr-9 text-sm": props.size === "sm",
        "h-10 pl-3.5 pr-10 text-sm": props.size === "md",
        "h-12 pl-4 pr-11 text-base": props.size === "lg",
    },
    {
        "border-gray-300 focus:border-blue-500 focus:ring-blue-500/20":
            props.variant === "outline" && !props.error,
        "border-transparent bg-gray-100 focus:border-blue-500 focus:ring-blue-500/20":
            props.variant === "soft" && !props.error,
        "border-transparent bg-transparent focus:border-blue-500 focus:ring-blue-500/20":
            props.variant === "ghost" && !props.error,
        "border-red-500 focus:border-red-500 focus:ring-red-500/20":
            !!props.error,
    },
]);

const hasOption = computed(() =>
    props.options.some((option) => option.value === model.value),
);
</script>

<template>
    <div class="w-full">
        <label
            v-if="label"
            :for="id"
            class="mb-1.5 block text-sm font-medium text-gray-700"
        >
            {{ label }}
            <span v-if="required" class="ml-0.5 text-red-500">*</span>
        </label>

        <div class="relative">
            <select
                v-bind="attrs"
                :id="id"
                v-model="model"
                :disabled="disabled"
                :required="required"
                :aria-invalid="!!error"
                :aria-describedby="
                    error ? `${id}-error` : hint ? `${id}-hint` : undefined
                "
                :class="selectClasses"
            >
                <option v-if="placeholder" value="" disabled hidden>
                    {{ placeholder }}
                </option>

                <option
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                    :disabled="option.disabled"
                >
                    {{ option.label }}
                </option>
            </select>

            <div
                class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m6 9 6 6 6-6"
                    />
                </svg>
            </div>
        </div>

        <p
            v-if="error"
            :id="`${id}-error`"
            class="mt-1.5 text-xs text-red-600"
            role="alert"
        >
            {{ error }}
        </p>

        <p
            v-else-if="hint"
            :id="`${id}-hint`"
            class="mt-1.5 text-xs text-gray-500"
        >
            {{ hint }}
        </p>
    </div>
</template>
