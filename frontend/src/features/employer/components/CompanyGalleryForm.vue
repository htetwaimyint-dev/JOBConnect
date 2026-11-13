<script setup lang="ts">
import type { GalleryImage } from "../types/company-form.types";

defineProps<{
    existingImages: string[];
    newImages: GalleryImage[];
    errors: Record<string, string[]>;
}>();
const emit = defineEmits<{
    addImages: [files: File[]];
    removeExisting: [index: number];
    removeNew: [index: number];
}>();

function handleFiles(e: Event) {
    const input = e.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    if (files.length) emit("addImages", files);
    input.value = "";
}
</script>

<template>
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs">
        <h5 class="text-xl font-bold text-slate-900 mb-6">Gallery Images</h5>

        <label
            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 transition hover:bg-slate-100 w-full mt-2"
        >
            <span class="text-base text-slate-400 mb-1">+</span>
            <span class="text-xs font-semibold text-slate-700"
                >Add Gallery Images</span
            >
            <span class="text-[10px] text-slate-400 ml-10"
                >PNG, JPG, WebP up to 5MB</span
            >
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
            class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"
        >
            <div
                v-for="(img, idx) in existingImages"
                :key="`existing-${img}-${idx}`"
                class="relative aspect-square overflow-hidden rounded-lg border bg-slate-100"
            >
                <img :src="img" class="h-full w-full object-cover" />
                <button
                    type="button"
                    @click="emit('removeExisting', idx)"
                    class="absolute top-1.5 right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-slate-900/70 text-white text-xs"
                >
                    ×
                </button>
            </div>

            <div
                v-for="(item, idx) in newImages"
                :key="item.preview"
                class="relative aspect-square overflow-hidden rounded-lg border bg-slate-100"
            >
                <img :src="item.preview" class="h-full w-full object-cover" />
                <button
                    type="button"
                    @click="emit('removeNew', idx)"
                    class="absolute top-1.5 right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-slate-900/70 text-white text-xs"
                >
                    ×
                </button>
                <span
                    class="absolute bottom-1.5 left-1.5 rounded bg-slate-900/80 px-1 py-0.5 text-[9px] text-white"
                    >New</span
                >
            </div>
        </div>
    </section>
</template>
