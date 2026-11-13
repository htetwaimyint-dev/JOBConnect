<script setup lang="ts">
import { ref, watch } from "vue";
import {
    FileUp,
    FileCheck,
    LockKeyhole,
    Globe,
    Star,
    Upload,
    LoaderCircle,
    X,
    AlertCircle,
} from "lucide-vue-next";

import AppModal from "@/components/common/AppModal.vue";
import type { Resume, ResumeVisibility } from "../types/resume.types";

interface ResumeFormData {
    title: string;
    file: File | null;
    is_default: boolean;
    visibility: ResumeVisibility;
}

const props = defineProps<{
    show: boolean;
    resume?: Resume | null;
    isSaving?: boolean;
}>();

const emit = defineEmits<{
    close: [];
    save: [data: ResumeFormData];
}>();

const form = ref<ResumeFormData>({
    title: "",
    file: null,
    is_default: false,
    visibility: "private",
});

const fileInput = ref<HTMLInputElement | null>(null);
const errorMessage = ref("");

const isEditing = () => props.resume !== null && props.resume !== undefined;

watch(
    () => [props.show, props.resume] as const,
    ([show, resume]) => {
        if (!show) return;

        errorMessage.value = "";

        form.value = {
            title: resume?.title ?? "",
            file: null,
            is_default: resume?.is_default ?? false,
            visibility: resume?.visibility ?? "private",
        };

        if (fileInput.value) {
            fileInput.value.value = "";
        }
    },
    { immediate: true },
);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    const allowedExtensions = ["pdf", "doc", "docx"];
    const extension = file.name.split(".").pop()?.toLowerCase();

    if (!extension || !allowedExtensions.includes(extension)) {
        errorMessage.value = "Only PDF, DOC, and DOCX files are allowed.";
        target.value = "";
        form.value.file = null;
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        errorMessage.value = "File size must not exceed 5 MB.";
        target.value = "";
        form.value.file = null;
        return;
    }

    errorMessage.value = "";
    form.value.file = file;
};

const handleSubmit = () => {
    errorMessage.value = "";

    const title = form.value.title.trim();

    if (!title) {
        errorMessage.value = "Resume title is required.";
        return;
    }

    if (!isEditing() && !form.value.file) {
        errorMessage.value = "Please select a resume file.";
        return;
    }

    emit("save", {
        title,
        file: form.value.file,
        is_default: form.value.is_default,
        visibility: form.value.visibility,
    });
};
</script>

<template>
    <AppModal :show="show" :title="isEditing() ? 'Edit Resume' : 'Upload New Resume'" :description="isEditing()
            ? 'Update your resume information.'
            : 'Upload your professional resume to your profile.'
        " max-width="max-w-xl" @close="emit('close')">
        <form class="space-y-5" @submit.prevent="handleSubmit">

            <!-- Error -->
            <div v-if="errorMessage"
                class="flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <AlertCircle :size="18" class="mt-0.5 shrink-0" />
                <p>{{ errorMessage }}</p>
            </div>

            <!-- Resume Title -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Resume Title <span class="text-red-500">*</span>
                </label>

                <input v-model="form.title" type="text" maxlength="255" required
                    placeholder="e.g. Software Developer Resume"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-violet-400 focus:ring-4 focus:ring-violet-100" />
            </div>

            <!-- File Upload -->
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <FileUp :size="17" class="text-violet-600" />
                    Resume File
                    <span v-if="!isEditing()" class="text-red-500">*</span>
                </label>

                <label
                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-violet-200 bg-white px-5 py-8 text-center transition hover:border-violet-400 hover:bg-violet-50/50">
                    <div
                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                        <Upload :size="23" />
                    </div>

                    <span class="text-sm font-semibold text-slate-700">
                        Click to select your resume
                    </span>

                    <span class="mt-1 text-xs text-slate-400">
                        PDF, DOC or DOCX · Maximum 5 MB
                    </span>

                    <input ref="fileInput" type="file" accept=".pdf,.doc,.docx" class="hidden"
                        @change="handleFileChange" />
                </label>

                <!-- Selected File -->
                <div v-if="form.file"
                    class="mt-3 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                    <FileCheck :size="22" class="shrink-0 text-emerald-600" />

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-emerald-800">
                            {{ form.file.name }}
                        </p>

                        <p class="text-xs text-emerald-600">
                            {{ (form.file.size / 1024 / 1024).toFixed(2) }} MB
                        </p>
                    </div>
                </div>

                <p v-if="isEditing() && !form.file" class="mt-2 text-xs text-violet-600">
                    Leave empty to keep the current resume file.
                </p>
            </div>

            <!-- Visibility -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Resume Visibility
                </label>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition" :class="form.visibility === 'private'
                            ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-100'
                            : 'border-slate-200 bg-white hover:border-slate-300'
                        ">
                        <input v-model="form.visibility" type="radio" value="private" class="h-4 w-4 text-violet-600" />

                        <LockKeyhole :size="20" class="shrink-0 text-slate-600" />

                        <div>
                            <span class="block text-sm font-semibold text-slate-700">
                                Private
                            </span>
                            <span class="text-xs text-slate-500">
                                Keep your resume private.
                            </span>
                        </div>
                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition" :class="form.visibility === 'public'
                            ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-100'
                            : 'border-slate-200 bg-white hover:border-slate-300'
                        ">
                        <input v-model="form.visibility" type="radio" value="public" class="h-4 w-4 text-violet-600" />

                        <Globe :size="20" class="shrink-0 text-violet-600" />

                        <div>
                            <span class="block text-sm font-semibold text-slate-700">
                                Public
                            </span>
                            <span class="text-xs text-slate-500">
                                Make it publicly visible.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Default Resume -->
            <label
                class="flex cursor-pointer items-center gap-3 rounded-xl border border-amber-200 bg-amber-50/60 p-4 transition hover:bg-amber-50">
                <input v-model="form.is_default" type="checkbox"
                    class="h-5 w-5 rounded border-amber-300 text-amber-600 focus:ring-amber-500" />

                <Star :size="21" class="shrink-0 text-amber-600" />

                <div>
                    <span class="block text-sm font-semibold text-slate-700">
                        Set as Default Resume
                    </span>
                    <span class="text-xs text-slate-500">
                        Mark this as your primary resume.
                    </span>
                </div>
            </label>

            <!-- Actions -->
            <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                <button type="button" :disabled="isSaving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50"
                    @click="emit('close')">
                    <X :size="17" />
                    Cancel
                </button>

                <button type="submit" :disabled="isSaving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <LoaderCircle v-if="isSaving" :size="17" class="animate-spin" />
                    <Upload v-else :size="17" />

                    {{
                        isSaving
                            ? "Saving..."
                            : isEditing()
                                ? "Update Resume"
                                : "Upload Resume"
                    }}
                </button>
            </div>
        </form>
    </AppModal>
</template>
