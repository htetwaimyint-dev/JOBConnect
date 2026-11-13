<script setup lang="ts">
import { useRouter } from "vue-router";
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

const router = useRouter();

function goToAddImages() {
    router.push({
        name: "employer.company-profile.edit",
        query: {
            tab: "gallery",
        },
    });
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
                <BaseButton
                    size="28"
                    variant="outline"
                    color="primary"
                    @click="goToAddImages"
                >
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
