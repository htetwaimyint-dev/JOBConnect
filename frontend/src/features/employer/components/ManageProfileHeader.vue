<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
import { useRouter } from "vue-router";

defineProps<{
    activeTab: string;
    tabs: string[];
    isPending: boolean;
    isEditMode: boolean;
    isSubmitting: boolean;
}>();

const emit = defineEmits<{
    "update:activeTab": [tab: string];
    reset: [];
    viewProfile: [];
}>();

const router = useRouter();

function goToProfile() {
    router.push({
        name: "company-profile",
    });
}
</script>

<template>
    <div class="border-b border-slate-200 bg-white shadow-2xs">
        <div class="mx-auto max-w-7xl px-6 pt-6 pb-2">
            <!-- Title + View Profile Row -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <h1
                    class="text-2xl font-extrabold tracking-tight text-slate-900"
                >
                    Manage Profile
                </h1>

                <div v-if="!isPending" class="flex items-center gap-3">
                    <BaseButton
                        type="button"
                        size="32"
                        color="neutral"
                        variant="outline"
                        :disabled="isSubmitting"
                        @click="emit('reset')"
                        class="text-xs font-semibold text-slate-700 border-slate-300"
                    >
                        Cancel
                    </BaseButton>

                    <BaseButton
                        type="button"
                        size="32"
                        variant="outline"
                        color="neutral"
                        @click="goToProfile"
                        class="text-xs font-bold text-[#0052FF] border-[#0052FF] hover:bg-blue-50 tracking-wider"
                    >
                        VIEW PROFILE
                    </BaseButton>

                    <BaseButton
                        type="submit"
                        size="32"
                        :loading="isSubmitting"
                        :disabled="isSubmitting"
                        class="bg-[#0052FF] text-white text-xs font-semibold hover:bg-blue-700 shadow-2xs"
                    >
                        {{ isEditMode ? "Save Changes" : "Create Profile" }}
                    </BaseButton>
                </div>
            </div>

            <!-- Built In Horizontal Navigation Tabs -->
            <div class="mt-6 border-b border-slate-200">
                <nav class="-mb-px flex gap-3 overflow-x-auto scrollbar-none">
                    <button
                        v-for="tab in tabs"
                        :key="tab"
                        type="button"
                        @click="emit('update:activeTab', tab)"
                        :class="[
                            activeTab === tab
                                ? 'border-[#0052FF] text-[#0052FF] font-bold'
                                : 'border-transparent text-slate-600 font-medium hover:text-slate-900 hover:border-slate-300',
                            'whitespace-nowrap border-b-2 pb-3.5 text-xs! sm:text-xs! px-2 transition-colors',
                        ]"
                    >
                        {{ tab }}
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>
