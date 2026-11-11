<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    FileText,
    FileUp,
    FileCheck,
    LockKeyhole,
    Globe,
    Star,
    Download,
    Pencil,
    Trash2,
    Save,
    X,
    LoaderCircle,
    CheckCircle2,
    AlertCircle,
    Upload,
    File,
    Sparkles,
} from "lucide-vue-next";

import { resumeRepository } from "../repositories/resumeRepository";

import type {
    Resume,
    ResumeVisibility,
    CreateResumeData,
    UpdateResumeData,
} from "../types/resume.types";

const resumes = ref<Resume[]>([]);

const form = ref<{
    title: string;
    file: File | null;
    is_default: boolean;
    visibility: ResumeVisibility;
}>({
    title: "",
    file: null,
    is_default: false,
    visibility: "private",
});

const editingResumeId = ref<number | null>(null);

const fileInput = ref<HTMLInputElement | null>(null);

const isLoading = ref(false);
const isSaving = ref(false);
const downloadingId = ref<number | null>(null);

const errorMessage = ref("");
const successMessage = ref("");

// Load resumes
const loadResumes = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        resumes.value = await resumeRepository.getAll();
    } catch (error) {
        console.error("Failed to load resumes:", error);
        errorMessage.value = "Unable to load resumes.";
    } finally {
        isLoading.value = false;
    }
};

// Reset form
const resetForm = () => {
    form.value = {
        title: "",
        file: null,
        is_default: false,
        visibility: "private",
    };

    editingResumeId.value = null;

    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

// Handle file selection
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    const allowedTypes = [
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    ];

    if (!allowedTypes.includes(file.type)) {
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

// Add or Update resume
const handleSubmit = async () => {
    errorMessage.value = "";
    successMessage.value = "";

    const title = form.value.title.trim();

    if (!title) {
        errorMessage.value = "Resume title is required.";
        return;
    }

    if (editingResumeId.value === null && !form.value.file) {
        errorMessage.value = "Please select a resume file.";
        return;
    }

    isSaving.value = true;

    try {
        if (editingResumeId.value !== null) {
            const updateData: UpdateResumeData = {
                title,
                is_default: form.value.is_default,
                visibility: form.value.visibility,
            };

            if (form.value.file) {
                updateData.file = form.value.file;
            }

            const updatedResume = await resumeRepository.update(
                editingResumeId.value,
                updateData,
            );

            const index = resumes.value.findIndex(
                (resume) => resume.id === updatedResume.id,
            );

            if (index !== -1) {
                resumes.value[index] = updatedResume;
            }

            successMessage.value = "Resume updated successfully.";
        } else {
            const createData: CreateResumeData = {
                title,
                file: form.value.file!,
                is_default: form.value.is_default,
                visibility: form.value.visibility,
            };

            const newResume = await resumeRepository.create(createData);

            resumes.value.unshift(newResume);

            successMessage.value = "Resume uploaded successfully.";
        }

        // Refresh list to reflect default resume changes
        resumes.value = await resumeRepository.getAll();

        resetForm();
    } catch (error) {
        console.error("Failed to save resume:", error);
        errorMessage.value =
            "Unable to save resume. Please check your input and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Edit resume
const editResume = (resume: Resume) => {
    form.value = {
        title: resume.title,
        file: null,
        is_default: resume.is_default,
        visibility: resume.visibility,
    };

    editingResumeId.value = resume.id;

    if (fileInput.value) {
        fileInput.value.value = "";
    }

    errorMessage.value = "";
    successMessage.value = "";
};

// Download resume
const downloadResume = async (resume: Resume) => {
    errorMessage.value = "";
    downloadingId.value = resume.id;

    try {
        const blob = await resumeRepository.download(resume.id);

        const url = window.URL.createObjectURL(blob);

        const link = document.createElement("a");
        link.href = url;
        link.download = `${resume.title}.${resume.file_type}`;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Failed to download resume:", error);
        errorMessage.value = "Unable to download resume.";
    } finally {
        downloadingId.value = null;
    }
};

// Delete resume
const deleteResume = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this resume?",
    );

    if (!confirmed) return;

    errorMessage.value = "";
    successMessage.value = "";

    try {
        await resumeRepository.delete(id);

        resumes.value = resumes.value.filter(
            (resume) => resume.id !== id,
        );

        if (editingResumeId.value === id) {
            resetForm();
        }

        successMessage.value = "Resume deleted successfully.";
    } catch (error) {
        console.error("Failed to delete resume:", error);
        errorMessage.value = "Unable to delete resume.";
    }
};

onMounted(loadResumes);
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div class="flex items-center gap-4 border-b border-slate-100 p-5 sm:p-6">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <FileText :size="25" />
            </div>

            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    My Resumes
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Upload and manage your professional resumes.
                </p>
            </div>
        </div>

        <div class="space-y-6 p-5 sm:p-6">

            <!-- Error Message -->
            <div v-if="errorMessage"
                class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <AlertCircle :size="19" class="mt-0.5 shrink-0" />
                <p>{{ errorMessage }}</p>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage"
                class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <CheckCircle2 :size="19" class="mt-0.5 shrink-0" />
                <p>{{ successMessage }}</p>
            </div>

            <!-- Upload / Edit Form -->
            <form class="rounded-2xl border border-violet-100 bg-violet-50/30 p-5 sm:p-6"
                @submit.prevent="handleSubmit">
                <!-- Form Heading -->
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                        <Pencil v-if="editingResumeId !== null" :size="19" />
                        <FileUp v-else :size="20" />
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800">
                            {{ editingResumeId !== null ? "Edit Resume" : "Upload New Resume" }}
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            {{
                                editingResumeId !== null
                                    ? "Update your resume information."
                                    : "Add your CV to your professional profile."
                            }}
                        </p>
                    </div>
                </div>

                <div class="space-y-5">

                    <!-- Resume Title -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Resume Title <span class="text-red-500">*</span>
                        </label>

                        <input v-model="form.title" type="text" maxlength="255" required
                            placeholder="e.g. Software Developer Resume"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-violet-400 focus:ring-4 focus:ring-violet-100" />
                    </div>

                    <!-- Resume File -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <FileUp :size="17" class="text-violet-600" />
                            Resume File
                            <span v-if="editingResumeId === null" class="text-red-500">*</span>
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
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-600">
                                <FileCheck :size="21" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-emerald-800">
                                    {{ form.file.name }}
                                </p>

                                <p class="text-xs text-emerald-600">
                                    {{ (form.file.size / 1024 / 1024).toFixed(2) }} MB · Selected
                                </p>
                            </div>

                            <CheckCircle2 :size="19" class="shrink-0 text-emerald-600" />
                        </div>

                        <p v-if="editingResumeId !== null && !form.file"
                            class="mt-2 flex items-center gap-1.5 text-xs text-violet-600">
                            <FileCheck :size="14" />
                            Leave empty to keep the current file.
                        </p>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Resume Visibility
                        </label>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            <!-- Private -->
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                                :class="form.visibility === 'private'
                                    ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-100'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input v-model="form.visibility" type="radio" value="private"
                                    class="h-4 w-4 text-violet-600 focus:ring-violet-500" />

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                    <LockKeyhole :size="19" />
                                </div>

                                <div>
                                    <span class="block text-sm font-semibold text-slate-700">
                                        Private
                                    </span>

                                    <span class="mt-0.5 block text-xs text-slate-500">
                                        Keep your resume private.
                                    </span>
                                </div>
                            </label>

                            <!-- Public -->
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                                :class="form.visibility === 'public'
                                    ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-100'
                                    : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input v-model="form.visibility" type="radio" value="public"
                                    class="h-4 w-4 text-violet-600 focus:ring-violet-500" />

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                                    <Globe :size="19" />
                                </div>

                                <div>
                                    <span class="block text-sm font-semibold text-slate-700">
                                        Public
                                    </span>

                                    <span class="mt-0.5 block text-xs text-slate-500">
                                        Make it publicly visible.
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Default Resume -->
                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-amber-200 bg-amber-50/60 p-4 transition hover:bg-amber-50">
                        <input id="is-default" v-model="form.is_default" type="checkbox"
                            class="h-5 w-5 rounded border-amber-300 text-amber-600 focus:ring-amber-500" />

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                            <Star :size="20" />
                        </div>

                        <div>
                            <span class="block text-sm font-semibold text-slate-700">
                                Set as Default Resume
                            </span>

                            <span class="mt-0.5 block text-xs text-slate-500">
                                Mark this as your primary resume.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex flex-col-reverse justify-end gap-3 border-t border-violet-100 pt-5 sm:flex-row">
                    <button v-if="editingResumeId !== null" type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        @click="resetForm">
                        <X :size="17" />
                        Cancel
                    </button>

                    <button type="submit" :disabled="isSaving"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <LoaderCircle v-if="isSaving" :size="17" class="animate-spin" />
                        <Save v-else-if="editingResumeId !== null" :size="17" />
                        <Upload v-else :size="17" />

                        {{
                            isSaving
                                ? "Saving..."
                                : editingResumeId !== null
                                    ? "Update Resume"
                                    : "Upload Resume"
                        }}
                    </button>
                </div>
            </form>

            <!-- Resume List Heading -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-bold text-slate-800">
                        Your Uploaded Resumes
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Manage your resume files.
                    </p>
                </div>

                <span class="rounded-full bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700">
                    {{ resumes.length }} {{ resumes.length === 1 ? "Resume" : "Resumes" }}
                </span>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
                <LoaderCircle :size="30" class="animate-spin text-violet-500" />

                <p class="mt-3 text-sm font-medium text-slate-500">
                    Loading resumes...
                </p>
            </div>

            <!-- Empty State -->
            <div v-else-if="resumes.length === 0"
                class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-5 py-10 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-50 text-violet-500">
                    <FileText :size="30" />
                </div>

                <h3 class="mt-4 font-bold text-slate-800">
                    No Resumes Uploaded Yet
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    Upload your first resume to keep your professional documents organized.
                </p>
            </div>

            <!-- Resume List -->
            <div v-else class="space-y-3">
                <div v-for="resume in resumes" :key="resume.id"
                    class="group rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-violet-200 hover:shadow-md sm:p-5">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

                        <!-- Resume Information -->
                        <div class="flex min-w-0 items-start gap-3">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                                <File :size="24" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="break-words font-bold text-slate-800">
                                        {{ resume.title }}
                                    </h4>

                                    <span v-if="resume.is_default"
                                        class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        <Star :size="12" />
                                        Default
                                    </span>
                                </div>

                                <!-- Resume Badges -->
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span
                                        class="rounded-lg bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                        {{ resume.file_type.toUpperCase() }}
                                    </span>

                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-medium"
                                        :class="resume.visibility === 'public'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-slate-100 text-slate-600'">
                                        <Globe v-if="resume.visibility === 'public'" :size="12" />
                                        <LockKeyhole v-else :size="12" />
                                        {{ resume.visibility }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                            <button type="button" :disabled="downloadingId === resume.id"
                                @click="downloadResume(resume)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-violet-200 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-50 disabled:cursor-not-allowed disabled:opacity-50">
                                <LoaderCircle v-if="downloadingId === resume.id" :size="14" class="animate-spin" />

                                <Download v-else :size="14" />

                                {{
                                    downloadingId === resume.id
                                        ? "Downloading..."
                                        : "Download"
                                }}
                            </button>

                            <button type="button" @click="editResume(resume)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                <Pencil :size="14" />
                                Edit
                            </button>

                            <button type="button" @click="deleteResume(resume.id)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                <Trash2 :size="14" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-400">
                <Sparkles :size="14" />
                Keep your resume up to date for better job application opportunities.
            </div>
        </div>
    </section>
</template>
