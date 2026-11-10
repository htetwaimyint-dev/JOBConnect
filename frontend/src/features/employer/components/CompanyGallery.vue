<script setup lang="ts">
import type { GalleryImage } from "../types/company-form.types";
import BaseButton from "@/components/common/BaseButton.vue";

defineProps<{
    existingImages: string[];
    newImages?: GalleryImage[];
}>();

const emit = defineEmits<{
    addImages: [files: FileList];
    removeExisting: [index: number];
    removeNew: [index: number];
}>();

function onImagesChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files?.length) emit("addImages", input.files);
    input.value = "";
}
</script>

<template>
    <section class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Gallery</h2>
                <p class="mt-0.5 text-xs text-gray-500">
                    Showcase your workplace and team.
                </p>
            </div>

            <label class="shrink-0 cursor-pointer">
                <BaseButton size="28" variant="outline" color="primary">
                    Add images
                </BaseButton>
                <input
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    multiple
                    class="hidden"
                    @change="onImagesChange"
                />
            </label>
        </div>

        <div
            v-if="existingImages.length || newImages?.length"
            class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-5"
        >
            <div
                v-for="(image, index) in existingImages"
                :key="`existing-${index}`"
                class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200"
            >
                <img
                    :src="image"
                    class="h-full w-full object-cover"
                    alt="Gallery image"
                />
                <button
                    type="button"
                    class="absolute right-1 top-1 rounded bg-white/90 px-2 py-1 text-xs text-red-600 opacity-100 shadow sm:opacity-0 sm:group-hover:opacity-100"
                    @click="emit('removeExisting', index)"
                >
                    Remove
                </button>
            </div>

            <div
                v-for="(image, index) in newImages"
                :key="`new-${index}`"
                class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200"
            >
                <img
                    :src="image.preview"
                    class="h-full w-full object-cover"
                    alt="New gallery image"
                />
                <button
                    type="button"
                    class="absolute right-1 top-1 rounded bg-white/90 px-2 py-1 text-xs text-red-600 opacity-100 shadow sm:opacity-0 sm:group-hover:opacity-100"
                    @click="emit('removeNew', index)"
                >
                    Remove
                </button>
            </div>
        </div>

        <div
            v-else
            class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center"
        >
            <p class="text-xs text-gray-500">No gallery images added yet.</p>
        </div>
    </section>
</template>
