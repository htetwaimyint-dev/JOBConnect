<script setup lang="ts">
import { FileText, X } from "lucide-vue-next";

defineProps<{
    open: boolean;
    loading: boolean;
    error: string | null;
    previewUrl: string | null;
    resumeTitle: string;
}>();

const emit = defineEmits<{
    close: [];
}>();
</script>

<template>
    <Transition name="drawer">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/70 p-3 sm:p-6"
            @click.self="emit('close')">
            <div
                class="flex h-full max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <FileText class="h-5 w-5 shrink-0 text-blue-700" />

                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900">
                                Resume Preview
                            </h3>

                            <p class="text-xs text-slate-500">
                                {{ resumeTitle }}
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="emit('close')"
                        class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="min-h-0 flex-1 bg-slate-100">
                    <!-- Loading -->
                    <div v-if="loading" class="flex h-full flex-col items-center justify-center">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-100 border-t-blue-700">
                        </div>

                        <p class="mt-4 text-sm text-slate-500">
                            Preparing resume preview...
                        </p>
                    </div>

                    <!-- Error -->
                    <div v-else-if="error" class="flex h-full flex-col items-center justify-center p-6 text-center">
                        <FileText class="h-12 w-12 text-slate-300" />

                        <p class="mt-4 max-w-md text-sm text-red-600">
                            {{ error }}
                        </p>

                        <button type="button" @click="emit('close')"
                            class="mt-5 rounded-xl bg-[#102D59] px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">
                            Close
                        </button>
                    </div>

                    <!-- PDF Preview -->
                    <iframe v-else-if="previewUrl" :src="previewUrl" title="Resume PDF Preview"
                        class="h-full min-h-[400px] w-full border-0"></iframe>
                </div>
            </div>
        </div>
    </Transition>
</template>
