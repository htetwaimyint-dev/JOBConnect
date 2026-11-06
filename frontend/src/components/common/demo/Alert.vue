<script setup lang="ts">
import { ref } from "vue";
import BaseAlert from "../BaseAlert.vue";

const variants = ["info", "success", "warning", "error"] as const;

const visibleAlerts = ref({
    info: true,
    success: true,
    warning: true,
    error: true,
});

function dismiss(variant: keyof typeof visibleAlerts.value) {
    visibleAlerts.value[variant] = false;
}

function resetAlerts() {
    visibleAlerts.value = {
        info: true,
        success: true,
        warning: true,
        error: true,
    };
}
</script>

<template>
    <div class="space-y-8 p-6">
        <h1 class="text-xl font-semibold">BaseAlert</h1>

        <!-- Variants -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Variants</h2>

            <BaseAlert
                variant="info"
                title="Information"
                message="This is an informational alert message."
            />

            <BaseAlert
                variant="success"
                title="Success"
                message="Your changes have been saved successfully."
            />

            <BaseAlert
                variant="warning"
                title="Warning"
                message="Please review the information before continuing."
            />

            <BaseAlert
                variant="error"
                title="Error"
                message="Something went wrong. Please try again."
            />
        </section>

        <!-- Without Title -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Without Title</h2>

            <BaseAlert
                variant="info"
                message="This alert displays a message without a title."
            />
        </section>

        <!-- Without Icon -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Without Icon</h2>

            <BaseAlert
                variant="success"
                title="Saved"
                message="Your profile has been updated."
                :show-icon="false"
            />
        </section>

        <!-- Dismissible -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">
                    Dismissible Alerts
                </h2>

                <button
                    type="button"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                    @click="resetAlerts"
                >
                    Reset alerts
                </button>
            </div>

            <BaseAlert
                v-if="visibleAlerts.info"
                variant="info"
                title="Information"
                message="You can dismiss this alert."
                dismissible
                @dismiss="dismiss('info')"
            />

            <BaseAlert
                v-if="visibleAlerts.success"
                variant="success"
                title="Success"
                message="This alert can also be dismissed."
                dismissible
                @dismiss="dismiss('success')"
            />

            <BaseAlert
                v-if="visibleAlerts.warning"
                variant="warning"
                title="Warning"
                message="This warning can be dismissed."
                dismissible
                @dismiss="dismiss('warning')"
            />

            <BaseAlert
                v-if="visibleAlerts.error"
                variant="error"
                title="Error"
                message="This error alert can be dismissed."
                dismissible
                @dismiss="dismiss('error')"
            />
        </section>

        <!-- Slot Content -->
        <section class="space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Custom Content</h2>

            <BaseAlert variant="info" title="New update available">
                <p>
                    A new version is available. Update now to get the latest
                    features.
                </p>

                <button
                    type="button"
                    class="mt-2 font-semibold text-blue-700 underline"
                >
                    Learn more
                </button>
            </BaseAlert>

            <BaseAlert variant="warning" title="Account notice">
                <p>Your subscription will expire soon.</p>
                <p class="mt-1 font-medium">
                    Please renew your subscription to avoid interruption.
                </p>
            </BaseAlert>
        </section>
    </div>
</template>
