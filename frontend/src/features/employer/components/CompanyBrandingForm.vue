<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
    coverPreview: string | null;
}>();

const emit = defineEmits<{
    selectCover: [file: File];
}>();

function onCoverChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) emit("selectCover", file);
}
</script>

<template>
    <section class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="mb-3">
            <h2 class="text-sm font-semibold text-gray-900">Cover image</h2>
            <p class="mt-0.5 text-xs text-gray-500">
                Add a banner to your company profile.
            </p>
        </div>

        <div
            class="relative flex h-32 items-center justify-center overflow-hidden rounded-lg border border-dashed border-gray-300 bg-gray-50 sm:h-40"
        >
            <img
                v-if="coverPreview"
                :src="coverPreview"
                alt="Company cover"
                class="absolute inset-0 h-full w-full object-cover"
            />

            <div
                v-else
                class="relative flex flex-col items-center gap-1 text-center"
            >
                <span class="text-xs font-medium text-gray-600">
                    No cover image selected
                </span>
                <span class="text-xs text-gray-400">
                    Recommended: wide landscape image
                </span>
            </div>

            <label class="absolute bottom-2 right-2 cursor-pointer">
                <BaseButton size="28" variant="solid" color="primary">
                    {{ coverPreview ? "Change cover" : "Upload cover" }}
                </BaseButton>
                <input
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    class="hidden"
                    @change="onCoverChange"
                />
            </label>
        </div>
    </section>
</template>
