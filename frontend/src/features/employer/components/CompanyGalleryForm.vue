<script setup lang="ts">
import type { GalleryImage } from "../types/company-form.types";

defineProps<{
    existingImages: string[];
    newImages: GalleryImage[];
}>();

const emit = defineEmits<{
    addImages: [files: File[]];
    removeExisting: [index: number];
    removeNew: [index: number];
}>();

function handleFiles(event: Event) {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);

    if (files.length) emit("addImages", files);

    input.value = "";
}
</script>

<template>
    <section class="border border-gray-100 bg-white p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900">Company Gallery</h2>

            <p class="mt-1 text-sm text-gray-500">
                Showcase your workplace, team and company culture.
            </p>
        </div>

        <label
            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center transition hover:border-violet-400 hover:bg-violet-50"
        >
            <span class="text-2xl text-violet-600">+</span>

            <span class="mt-2 text-sm font-semibold text-gray-700">
                Add Gallery Images
            </span>

            <span class="mt-1 text-xs text-gray-400">
                Select multiple images. Maximum 5MB per image.
            </span>

            <input
                type="file"
                accept="image/png,image/jpeg,image/webp"
                multiple
                class="hidden"
                @change="handleFiles"
            />
        </label>

        <div
            v-if="existingImages.length || newImages.length"
            class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3"
        >
            <!-- Existing Images -->
            <div
                v-for="(image, index) in existingImages"
                :key="`existing-${image}-${index}`"
                class="group relative overflow-hidden rounded-xl"
            >
                <img
                    :src="image"
                    :alt="`Gallery image ${index + 1}`"
                    class="h-36 w-full object-cover"
                />

                <button
                    type="button"
                    @click="emit('removeExisting', index)"
                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-red-600"
                    aria-label="Remove image"
                >
                    ×
                </button>
            </div>

            <!-- New Images -->
            <div
                v-for="(item, index) in newImages"
                :key="item.preview"
                class="group relative overflow-hidden rounded-xl"
            >
                <img
                    :src="item.preview"
                    :alt="item.file.name"
                    class="h-36 w-full object-cover"
                />

                <button
                    type="button"
                    @click="emit('removeNew', index)"
                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-red-600"
                    aria-label="Remove image"
                >
                    ×
                </button>

                <span
                    class="absolute bottom-2 left-2 rounded bg-violet-600 px-2 py-1 text-[10px] font-semibold text-white"
                >
                    New
                </span>
            </div>
        </div>

        <p v-else class="mt-5 text-center text-xs text-gray-400">
            No gallery images added yet.
        </p>
    </section>
</template>
