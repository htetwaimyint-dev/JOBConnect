<script setup lang="ts">
import { computed } from "vue";

type AlertVariant = "error" | "success" | "warning" | "info";

interface Props {
    variant?: AlertVariant;
    title?: string;
    message?: string;
    dismissible?: boolean;
    showIcon?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: "info",
    title: "",
    message: "",
    dismissible: false,
    showIcon: true,
});

const emit = defineEmits<{
    dismiss: [];
}>();

const styles = {
    error: {
        container: "border-red-200 bg-red-50 text-red-800",
        icon: "text-red-500",
        title: "text-red-900",
    },
    success: {
        container: "border-green-200 bg-green-50 text-green-800",
        icon: "text-green-500",
        title: "text-green-900",
    },
    warning: {
        container: "border-yellow-200 bg-yellow-50 text-yellow-800",
        icon: "text-yellow-600",
        title: "text-yellow-900",
    },
    info: {
        container: "border-blue-200 bg-blue-50 text-blue-800",
        icon: "text-blue-500",
        title: "text-blue-900",
    },
} as const;

const variantClasses = computed(() => styles[props.variant]);

const icons: Record<AlertVariant, string> = {
    error: "M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z",
    success: "M5 13l4 4L19 7",
    warning:
        "M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z",
    info: "M12 16v-4m0-4h.01M22 12a10 10 0 11-20 0 10 10 0 0120 0z",
};
</script>

<template>
    <div
        :class="[
            'flex items-start gap-3 rounded-lg border p-4',
            variantClasses.container,
        ]"
        :role="variant === 'error' ? 'alert' : 'status'"
        :aria-live="variant === 'error' ? 'assertive' : 'polite'"
    >
        <!-- Icon -->
        <div
            v-if="showIcon"
            :class="['mt-0.5 shrink-0', variantClasses.icon]"
            aria-hidden="true"
        >
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path :d="icons[variant]" />
            </svg>
        </div>

        <!-- Content -->
        <div class="min-w-0 flex-1">
            <h3
                v-if="title"
                :class="['text-sm font-semibold', variantClasses.title]"
            >
                {{ title }}
            </h3>

            <p
                v-if="message"
                :class="['text-sm leading-5', title ? 'mt-1' : '']"
            >
                {{ message }}
            </p>

            <div
                v-if="$slots.default"
                :class="['text-sm', title || message ? 'mt-2' : '']"
            >
                <slot />
            </div>
        </div>

        <!-- Dismiss button -->
        <button
            v-if="dismissible"
            type="button"
            class="shrink-0 rounded-md p-1 opacity-70 transition hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-current"
            aria-label="Dismiss alert"
            @click="emit('dismiss')"
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
            >
                <path d="M18 6L6 18M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
