<script setup lang="ts">
import type { GalleryImage } from "../types/company-form.types";

defineProps<{
    existingImages: string[];
    newImages?: GalleryImage[];
    companyName?: string;
}>();
</script>

<template>
    <section
        class="rounded-xl border border-gray-100 bg-white p-6 sm:p-8 space-y-4"
    >
        <h2 class="text-lg font-bold text-gray-900">
            Life at {{ companyName || "Company" }}
        </h2>

        <div
            v-if="existingImages.length || newImages?.length"
            class="grid grid-cols-1 sm:grid-cols-3 gap-3"
        >
            <!-- Large main image on left -->
            <div
                v-if="existingImages[0]"
                class="sm:col-span-2 aspect-video overflow-hidden rounded-lg bg-gray-100"
            >
                <img
                    :src="existingImages[0]"
                    class="h-full w-full object-cover"
                    alt="Life at company"
                />
            </div>

            <!-- Smaller images on right -->
            <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:col-span-1">
                <div
                    v-for="(image, index) in existingImages.slice(1, 3)"
                    :key="`existing-${index + 1}`"
                    class="aspect-video sm:aspect-2/1 overflow-hidden rounded-lg bg-gray-100 relative"
                >
                    <img
                        :src="image"
                        class="h-full w-full object-cover"
                        alt="Life at company"
                    />
                    <div
                        v-if="index === 1 && existingImages.length > 3"
                        class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-semibold text-sm cursor-pointer"
                    >
                        See More
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="rounded-md border border-dashed border-gray-200 p-8 text-center"
        >
            <p class="text-xs text-gray-500">No gallery images added yet.</p>
        </div>
    </section>
</template>
