<script setup lang="ts">
import { computed, useAttrs } from "vue";

defineOptions({
    inheritAttrs: false,
});

type InputSize = "sm" | "md" | "lg";
type InputVariant = "outline" | "soft" | "ghost" | "filled" | "underline";

interface Props {
    id?: string;
    label?: string;
    type?: string;
    size?: InputSize;
    variant?: InputVariant;
    placeholder?: string;
    hint?: string;
    error?: string;
    disabled?: boolean;
    readonly?: boolean;
    required?: boolean;
    clearable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: "text",
    size: "md",
    variant: "outline",
    disabled: false,
    readonly: false,
    required: false,
    clearable: false,
});

const model = defineModel<string | number>({
    default: "",
});

const emit = defineEmits<{
    input: [event: Event];
    change: [event: Event];
    blur: [event: FocusEvent];
    focus: [event: FocusEvent];
    clear: [];
}>();

const attrs = useAttrs();

const hasValue = computed(() => model.value !== "");

const inputClasses = computed(() => [
    "w-full min-w-0 border bg-white text-gray-900",
    "placeholder:text-gray-400",
    "transition-colors duration-150",
    "focus:outline-none focus:ring-2 focus:ring-offset-0",
    "disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500",
    "read-only:cursor-default",
    {
        "h-9 text-sm": props.size === "sm",
        "h-10 text-sm": props.size === "md",
        "h-12 text-base": props.size === "lg",
    },
    {
        "border border-gray-300 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20":
            props.variant === "outline" && !props.error,

        "border border-transparent bg-gray-100 hover:bg-gray-200 focus:bg-white focus:border-gray-300 focus:ring-2 focus:ring-gray-200":
            props.variant === "soft" && !props.error,

        "border border-transparent bg-transparent hover:bg-gray-100 focus:bg-gray-50 focus:border-transparent focus:ring-2 focus:ring-blue-500/20":
            props.variant === "ghost" && !props.error,

        "border border-transparent bg-blue-50 hover:bg-blue-100 focus:bg-blue-50 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20":
            props.variant === "filled" && !props.error,

        "rounded-none border-0 border-b-2 border-gray-300 bg-transparent px-0 focus:border-blue-500 focus:ring-0":
            props.variant === "underline" && !props.error,

        "border border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-500/20":
            !!props.error,

        "border-0 border-b-2 border-b-red-500 focus:border-b-red-500 focus:ring-0":
            props.variant === "underline" && !!props.error,
    },
]);

function onClear() {
    model.value = "";
    emit("clear");
}
</script>

<template>
    <div class="w-full">
        <label v-if="label" :for="id" class="mb-1.5 block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="ml-0.5 text-red-500">*</span>
        </label>

        <div class="relative flex items-center">
            <div v-if="$slots.leading" class="absolute left-3 z-10 flex items-center text-gray-400">
                <slot name="leading" />
            </div>

            <input v-bind="attrs" :id="id" v-model="model" :type="type" :placeholder="placeholder" :disabled="disabled"
                :readonly="readonly" :required="required" :aria-invalid="!!error" :aria-describedby="error ? `${id}-error` : hint ? `${id}-hint` : undefined
                    " :class="[
                    inputClasses,
                    {
                        'pl-10': $slots.leading,
                        'pl-3': !$slots.leading && size === 'sm',
                        'pl-3.5': !$slots.leading && size === 'md',
                        'pl-4': !$slots.leading && size === 'lg',

                        'pr-10': $slots.trailing || (clearable && hasValue),
                        'pr-3': !$slots.trailing && !(clearable && hasValue) && size === 'sm',
                        'pr-3.5': !$slots.trailing && !(clearable && hasValue) && size === 'md',
                        'pr-4': !$slots.trailing && !(clearable && hasValue) && size === 'lg',
                    },
                ]" @input="emit('input', $event)" @change="emit('change', $event)" @blur="emit('blur', $event)"
                @focus="emit('focus', $event)" />

            <button v-if="clearable && hasValue && !disabled && !readonly" type="button"
                class="absolute right-3 text-gray-400 hover:text-gray-700" aria-label="Clear input" @click="onClear">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div v-else-if="$slots.trailing" class="absolute right-3 flex items-center text-gray-400">
                <slot name="trailing" />
            </div>
        </div>

        <p v-if="error" :id="`${id}-error`" class="mt-1.5 text-xs text-red-600" role="alert">
            {{ error }}
        </p>

        <p v-else-if="hint" :id="`${id}-hint`" class="mt-1.5 text-xs text-gray-500">
            {{ hint }}
        </p>
    </div>
</template>
