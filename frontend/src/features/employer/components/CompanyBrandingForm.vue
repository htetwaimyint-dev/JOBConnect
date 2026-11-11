<script setup lang="ts">
import { ref } from "vue";
import BaseButton from "@/components/common/BaseButton.vue";
defineProps<{
    coverPreview: string | null;
    errors: Record<string, string[]>;
}>();
const emit = defineEmits<{ selectCover: [file: File] }>();

const fileInput = ref<HTMLInputElement | null>(null);

function openFilePicker() {
    fileInput.value?.click();
}

function onCoverChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) emit("selectCover", file);
}
</script>

<template>
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-2xs">
        <h5 class="text-xl font-bold text-slate-900 mb-6">Profile Style</h5>

        <div
            class="relative flex h-40 items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 mt-3"
        >
            <img
                v-if="coverPreview"
                :src="coverPreview"
                alt="Cover"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <span v-else class="text-xs text-slate-400"
                >Cover Photo (16:9 Landscape recommended)</span
            >

            <label class="absolute bottom-3 right-3 cursor-pointer">
                <BaseButton
                    type="button"
                    size="28"
                    variant="solid"
                    @click="openFilePicker"
                >
                    Upload Cover
                </BaseButton>

                <input
                    ref="fileInput"
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    class="hidden"
                    @change="onCoverChange"
                />
            </label>
        </div>
    </section>
</template>
