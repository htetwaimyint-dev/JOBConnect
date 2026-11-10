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
    <section
        class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs"
    >
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">
                Profile Style & Cover Photo
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Customize your brand appearance and header banner background.
            </p>
        </div>

        <div
            class="relative flex h-40 items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 sm:h-48"
        >
            <img
                v-if="coverPreview"
                :src="coverPreview"
                alt="Cover background"
                class="absolute inset-0 h-full w-full object-cover"
            />

            <div
                v-else
                class="flex flex-col items-center gap-1 text-center p-4"
            >
                <span class="text-xs font-medium text-slate-600"
                    >No cover photo selected</span
                >
                <span class="text-[11px] text-slate-400"
                    >Recommended landscape aspect ratio</span
                >
            </div>

            <label class="absolute bottom-3 right-3 cursor-pointer">
                <BaseButton
                    size="28"
                    variant="solid"
                    color="neutral"
                    class="bg-white text-slate-900 border border-slate-200 shadow-2xs hover:bg-slate-50"
                >
                    {{ coverPreview ? "Change Cover" : "Upload Cover" }}
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
