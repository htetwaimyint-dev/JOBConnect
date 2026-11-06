<script setup lang="ts">
defineOptions({
    inheritAttrs: false,
});

type CheckboxSize = "sm" | "md" | "lg";

interface Props {
    id?: string;
    label?: string;
    description?: string;
    error?: string;
    size?: CheckboxSize;
    disabled?: boolean;
    required?: boolean;
}

withDefaults(defineProps<Props>(), {
    size: "sm",
    disabled: false,
    required: false,
});

const model = defineModel<boolean>({
    default: false,
});
</script>

<template>
    <div class="w-full">
        <label
            :for="id"
            class="grid grid-cols-[auto_1fr] items-center gap-x-2.5"
            :class="disabled ? 'cursor-not-allowed' : 'cursor-pointer'"
        >
            <input
                v-bind="$attrs"
                :id="id"
                v-model="model"
                type="checkbox"
                :disabled="disabled"
                :required="required"
                :aria-invalid="!!error"
                :aria-describedby="
                    error
                        ? `${id}-error`
                        : description
                          ? `${id}-description`
                          : undefined
                "
                :class="[
                    'shrink-0 rounded border',
                    'bg-white border-gray-300 transition-colors duration-150',
                    'checked:bg-blue-600 checked:border-blue-600',
                    'focus:outline-none focus:ring-1 focus:ring-blue-500/10',
                    'disabled:cursor-not-allowed disabled:bg-gray-100',
                    'disabled:checked:bg-gray-400',
                    {
                        'h-3.5 w-3.5': size === 'sm',
                        'h-4 w-4': size === 'md',
                        'h-5 w-5': size === 'lg',
                        'border-red-500 focus:ring-red-500/20': !!error,
                    },
                ]"
            />

            <div class="min-w-0">
                <span
                    v-if="label || $slots.default"
                    class="block text-sm font-medium leading-5 text-gray-700"
                    :class="{ 'text-gray-400': disabled }"
                >
                    <slot>{{ label }}</slot>
                    <span v-if="required" class="ml-0.5 text-red-500">*</span>
                </span>

                <p
                    v-if="description"
                    :id="`${id}-description`"
                    class="mt-0.5 text-xs leading-4 text-gray-500"
                >
                    {{ description }}
                </p>
            </div>
        </label>

        <p
            v-if="error"
            :id="`${id}-error`"
            class="mt-1.5 text-xs leading-4 text-red-600"
            role="alert"
        >
            {{ error }}
        </p>
    </div>
</template>
