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
    <section
        class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs"
    >
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">
                Gallery Images + Video
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Upload office environment shots and culture imagery.
            </p>
        </div>

        <!-- Upload Dropzone -->
        <label
            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-6 text-center transition hover:bg-slate-100"
        >
            <span class="text-lg text-slate-400 mb-1">+</span>
            <span class="text-xs font-semibold text-slate-700">
                Add Gallery Images
            </span>
            <span class="mt-0.5 text-[11px] text-slate-400">
                PNG, JPG, WebP up to 5MB. Multiple selection supported.
            </span>

            <input
                type="file"
                accept="image/png,image/jpeg,image/webp"
                multiple
                class="hidden"
                @change="handleFiles"
            />
        </label>

        <!-- Gallery Grid -->
        <div
            v-if="existingImages.length || newImages.length"
            class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3"
        >
            <!-- Existing Images -->
            <div
                v-for="(image, index) in existingImages"
                :key="`existing-${image}-${index}`"
                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-100 aspect-square"
            >
                <img
                    :src="image"
                    :alt="`Gallery photo ${index + 1}`"
                    class="h-full w-full object-cover"
                />

                <button
                    type="button"
                    @click="emit('removeExisting', index)"
                    class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-slate-900/70 text-white text-xs transition hover:bg-rose-600"
                    aria-label="Remove image"
                >
                    ×
                </button>
            </div>

            <!-- New Images -->
            <div
                v-for="(item, index) in newImages"
                :key="item.preview"
                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-100 aspect-square"
            >
                <img
                    :src="item.preview"
                    :alt="item.file.name"
                    class="h-full w-full object-cover"
                />

                <button
                    type="button"
                    @click="emit('removeNew', index)"
                    class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-slate-900/70 text-white text-xs transition hover:bg-rose-600"
                    aria-label="Remove image"
                >
                    ×
                </button>

                <span
                    class="absolute bottom-2 left-2 rounded bg-slate-900/80 px-1.5 py-0.5 text-[9px] font-medium text-white"
                >
                    New
                </span>
            </div>
        </div>

        <!-- Empty State -->
        <p v-else class="mt-5 text-center text-xs text-slate-400">
            No gallery photos uploaded yet.
        </p>
    </section>
</template>
