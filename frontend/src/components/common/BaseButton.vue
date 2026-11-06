<script setup lang="ts">
import { computed } from "vue";

type ButtonSize = "20" | "24" | "28" | "32" | "36" | "40" | "44";
type ButtonType = "button" | "submit" | "link";
type ButtonVariant = "solid" | "soft" | "outline" | "ghost";
type ButtonColor =
    | "primary"
    | "neutral"
    | "danger"
    | "success"
    | "warning"
    | "light"
    | "dark";

interface Props {
    size?: ButtonSize;
    type?: ButtonType;
    variant?: ButtonVariant;
    color?: ButtonColor;
    active?: boolean;
    disabled?: boolean;
    loading?: boolean;
    showText?: boolean;
    to?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: "24",
    type: "button",
    variant: "solid",
    color: "primary",
    active: false,
    disabled: false,
    loading: false,
    showText: true,
    to: "",
});

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

// State
const isDisabled = computed(() => props.disabled || props.loading);

const isLink = computed(() => props.type === "link");

const htmlType = computed(() =>
    props.type === "submit" ? "submit" : "button",
);

// Size styles
const sizeClasses: Record<ButtonSize, string> = {
    "20": "text-xs px-1 py-0.5 gap-1",
    "24": "text-xs px-2 py-1 gap-1",
    "28": "text-xs px-2.5 py-1.5 gap-1.5",
    "32": "text-sm px-2.5 py-1.5 gap-1.5",
    "36": "text-sm px-3 py-2 gap-2",
    "40": "text-sm px-3.5 py-2.5 gap-2.5",
    "44": "text-base px-3.5 py-2.5 gap-2.5",
};

// Icon styles
const iconSizeClasses: Record<ButtonSize, string> = {
    "20": "h-4 w-4",
    "24": "h-4 w-4",
    "28": "h-4 w-4",
    "32": "h-5 w-5",
    "36": "h-5 w-5",
    "40": "h-5 w-5",
    "44": "h-6 w-6",
};

// Theme styles
const theme: Record<
    ButtonColor,
    Record<
        ButtonVariant,
        {
            base: string;
            hover: string;
            pressed: string;
            active: string;
        }
    >
> = {
    primary: {
        solid: {
            base: "bg-blue-600 text-white",
            hover: "hover:bg-blue-700",
            pressed: "active:bg-blue-800",
            active: "bg-blue-700",
        },
        soft: {
            base: "bg-blue-100 text-blue-800",
            hover: "hover:bg-blue-200",
            pressed: "active:bg-blue-300",
            active: "!bg-blue-200",
        },
        outline: {
            base: "text-blue-600 ring-1 ring-inset ring-current",
            hover: "hover:bg-blue-200",
            pressed: "active:bg-blue-300",
            active: "bg-blue-200",
        },
        ghost: {
            base: "text-blue-600",
            hover: "hover:bg-blue-100",
            pressed: "active:bg-blue-300",
            active: "bg-blue-200",
        },
    },

    neutral: {
        solid: {
            base: "bg-neutral-600 text-white",
            hover: "hover:bg-neutral-700",
            pressed: "active:bg-neutral-800",
            active: "bg-neutral-700",
        },
        soft: {
            base: "bg-neutral-100 text-neutral-800",
            hover: "hover:bg-neutral-200",
            pressed: "active:bg-neutral-300",
            active: "!bg-neutral-200",
        },
        outline: {
            base: "text-neutral-600 ring-1 ring-inset ring-current",
            hover: "hover:bg-neutral-200",
            pressed: "active:bg-neutral-300",
            active: "bg-neutral-200",
        },
        ghost: {
            base: "text-neutral-600",
            hover: "hover:bg-neutral-100",
            pressed: "active:bg-neutral-300",
            active: "bg-neutral-200",
        },
    },

    danger: {
        solid: {
            base: "bg-red-600 text-white",
            hover: "hover:bg-red-700",
            pressed: "active:bg-red-800",
            active: "bg-red-700",
        },
        soft: {
            base: "bg-red-100 text-red-800",
            hover: "hover:bg-red-200",
            pressed: "active:bg-red-300",
            active: "!bg-red-200",
        },
        outline: {
            base: "text-red-600 ring-1 ring-inset ring-current",
            hover: "hover:bg-red-200",
            pressed: "active:bg-red-300",
            active: "bg-red-200",
        },
        ghost: {
            base: "text-red-600",
            hover: "hover:bg-red-100",
            pressed: "active:bg-red-300",
            active: "bg-red-200",
        },
    },

    success: {
        solid: {
            base: "bg-green-600 text-white",
            hover: "hover:bg-green-700",
            pressed: "active:bg-green-800",
            active: "bg-green-700",
        },
        soft: {
            base: "bg-green-100 text-green-800",
            hover: "hover:bg-green-200",
            pressed: "active:bg-green-300",
            active: "!bg-green-200",
        },
        outline: {
            base: "text-green-600 ring-1 ring-inset ring-current",
            hover: "hover:bg-green-200",
            pressed: "active:bg-green-300",
            active: "bg-green-200",
        },
        ghost: {
            base: "text-green-600",
            hover: "hover:bg-green-100",
            pressed: "active:bg-green-300",
            active: "bg-green-200",
        },
    },

    warning: {
        solid: {
            base: "bg-orange-600 text-white",
            hover: "hover:bg-orange-700",
            pressed: "active:bg-orange-800",
            active: "bg-orange-700",
        },
        soft: {
            base: "bg-orange-100 text-orange-800",
            hover: "hover:bg-orange-200",
            pressed: "active:bg-orange-300",
            active: "!bg-orange-200",
        },
        outline: {
            base: "text-orange-600 ring-1 ring-inset ring-current",
            hover: "hover:bg-orange-200",
            pressed: "active:bg-orange-300",
            active: "bg-orange-200",
        },
        ghost: {
            base: "text-orange-600",
            hover: "hover:bg-orange-100",
            pressed: "active:bg-orange-300",
            active: "bg-orange-200",
        },
    },

    light: {
        solid: {
            base: "bg-neutral-100 text-neutral-950",
            hover: "hover:bg-neutral-300",
            pressed: "active:bg-neutral-400",
            active: "bg-neutral-300",
        },
        soft: {
            base: "bg-neutral-100 text-neutral-800",
            hover: "hover:bg-neutral-200",
            pressed: "active:bg-neutral-300",
            active: "!bg-neutral-200",
        },
        outline: {
            base: "text-neutral-100 ring-1 ring-inset ring-current",
            hover: "hover:bg-neutral-200 hover:text-neutral-950 hover:ring-neutral-200",
            pressed:
                "active:bg-neutral-300 active:text-neutral-950 active:ring-neutral-300",
            active: "bg-neutral-200 text-neutral-950 ring-neutral-200",
        },
        ghost: {
            base: "text-neutral-100",
            hover: "hover:bg-neutral-200 hover:text-neutral-950",
            pressed: "active:bg-neutral-300 active:text-neutral-950",
            active: "bg-neutral-200 text-neutral-950",
        },
    },

    dark: {
        solid: {
            base: "bg-neutral-950 text-neutral-100",
            hover: "hover:bg-neutral-900",
            pressed: "active:bg-neutral-800",
            active: "!bg-neutral-900",
        },
        soft: {
            base: "bg-neutral-950 text-neutral-100",
            hover: "hover:bg-neutral-900",
            pressed: "active:bg-neutral-800",
            active: "!bg-neutral-900",
        },
        outline: {
            base: "text-neutral-950 ring-1 ring-inset ring-current",
            hover: "hover:bg-neutral-900 hover:text-neutral-100 hover:ring-neutral-900",
            pressed:
                "active:bg-neutral-800 active:text-neutral-100 active:ring-neutral-800",
            active: "bg-neutral-800 !text-neutral-100 ring-neutral-800",
        },
        ghost: {
            base: "text-neutral-950",
            hover: "hover:bg-neutral-950 hover:text-neutral-100",
            pressed: "active:bg-neutral-900 active:text-neutral-100",
            active: "bg-neutral-800 !text-neutral-100",
        },
    },
};

// Disabled styles
const disabledClasses: Record<ButtonVariant, string> = {
    solid: "text-neutral-400 bg-neutral-200",
    soft: "text-neutral-400 bg-neutral-50",
    outline: "text-neutral-400 ring-1 ring-inset ring-current",
    ghost: "text-neutral-400",
};

// Computed classes
const classes = computed(() => {
    const styles = theme[props.color][props.variant];

    return [
        "relative inline-flex items-center justify-center",
        "rounded font-medium whitespace-nowrap",
        "transition-colors duration-150",
        "focus-visible:outline-none",
        "focus-visible:ring-2 focus-visible:ring-blue-500",
        "focus-visible:ring-offset-2",
        sizeClasses[props.size],

        isDisabled.value
            ? disabledClasses[props.variant]
            : [
                  styles.base,
                  styles.hover,
                  styles.pressed,
                  props.active ? styles.active : "",
              ],

        isDisabled.value ? "cursor-not-allowed" : "cursor-pointer",
    ];
});

const iconClasses = computed(() => [iconSizeClasses[props.size], "shrink-0"]);

// Click handler
function handleClick(event: MouseEvent) {
    if (isDisabled.value) {
        event.preventDefault();
        event.stopPropagation();
        return;
    }

    emit("click", event);
}

defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <component
        :is="isLink ? 'a' : 'button'"
        :href="isLink ? to || undefined : undefined"
        :type="!isLink ? htmlType : undefined"
        :disabled="!isLink ? isDisabled : undefined"
        :aria-disabled="isLink ? isDisabled : undefined"
        :aria-busy="loading ? 'true' : undefined"
        :tabindex="isLink && isDisabled ? -1 : undefined"
        :class="[classes, $attrs.class]"
        v-bind="$attrs"
        @click="handleClick"
    >
        <!-- Loading spinner -->
        <span
            v-if="loading"
            :class="iconClasses"
            class="animate-spin rounded-full border-2 border-current border-r-transparent"
            aria-hidden="true"
        />

        <!-- Leading icon -->
        <span
            v-if="$slots.leading && !loading"
            :class="iconClasses"
            class="inline-flex items-center justify-center"
            aria-hidden="true"
        >
            <slot name="leading" />
        </span>

        <!-- Button text -->
        <span v-if="showText" class="inline-flex items-center">
            <slot />
        </span>

        <!-- Trailing icon -->
        <span
            v-if="$slots.trailing"
            :class="iconClasses"
            class="inline-flex items-center justify-center"
            aria-hidden="true"
        >
            <slot name="trailing" />
        </span>
    </component>
</template>
