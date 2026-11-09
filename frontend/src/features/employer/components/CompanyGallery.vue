<script setup lang="ts">
defineProps<{
    images: string[];
}>();
</script>

<template>
    <section class="space-y-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Life at Our Company</h2>
            <p class="mt-1 text-sm text-gray-500">
                Take a look inside our workplace and company culture.
            </p>
        </div>

        <div v-if="images.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div
                v-for="(image, index) in images"
                :key="`${image}-${index}`"
                class="group overflow-hidden rounded-2xl bg-gray-100"
            >
                <img
                    :src="image"
                    :alt="`Company gallery image ${index + 1}`"
                    loading="lazy"
                    class="h-56 w-full object-cover transition duration-500 group-hover:scale-105"
                />
            </div>
        </div>

        <div
            v-else
            class="rounded-2xl border border-gray-100 bg-white px-6 py-16 text-center"
        >
            <h3 class="font-semibold text-gray-800">No gallery images yet</h3>
            <p class="mt-2 text-sm text-gray-500">
                Company photos will appear here.
            </p>
        </div>
    </section>
</template>
