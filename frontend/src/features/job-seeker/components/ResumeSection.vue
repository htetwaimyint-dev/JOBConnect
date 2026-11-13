<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    FileText,
    File,
    Globe,
    LockKeyhole,
    Star,
    Download,
    Pencil,
    Trash2,
    Plus,
    LoaderCircle,
    CheckCircle2,
    AlertCircle,
    Sparkles,
} from "lucide-vue-next";

import ResumeForm from "./ResumeForm.vue";
import { resumeRepository } from "../repositories/resumeRepository";

import type {
    Resume,
    ResumeVisibility,
    CreateResumeData,
    UpdateResumeData,
} from "../types/resume.types";

interface ResumeFormData {
    title: string;
    file: File | null;
    is_default: boolean;
    visibility: ResumeVisibility;
}

// Resume list
const resumes = ref<Resume[]>([]);

// Modal states
const showForm = ref(false);
const editingResume = ref<Resume | null>(null);

// Loading states
const isLoading = ref(false);
const isSaving = ref(false);
const downloadingId = ref<number | null>(null);
const deletingId = ref<number | null>(null);

// Messages
const errorMessage = ref("");
const successMessage = ref("");

// Load all resumes
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

// Open create modal
const openCreateForm = () => {
    editingResume.value = null;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Open edit modal
const openEditForm = (resume: Resume) => {
    editingResume.value = resume;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Close modal
const closeForm = () => {
    if (isSaving.value) return;

    showForm.value = false;
    editingResume.value = null;
};

// Create or update resume
const saveResume = async (data: ResumeFormData) => {
    errorMessage.value = "";
    successMessage.value = "";

    isSaving.value = true;

    try {
        if (editingResume.value) {
            // Update existing resume
            const updateData: UpdateResumeData = {
                title: data.title,
                is_default: data.is_default,
                visibility: data.visibility,
            };

            if (data.file) {
                updateData.file = data.file;
            }

            await resumeRepository.update(
                editingResume.value.id,
                updateData,
            );

            successMessage.value = "Resume updated successfully.";
        } else {
            // Create new resume
            if (!data.file) {
                errorMessage.value = "Please select a resume file.";
                return;
            }

            const createData: CreateResumeData = {
                title: data.title,
                file: data.file,
                is_default: data.is_default,
                visibility: data.visibility,
            };

            await resumeRepository.create(createData);

            successMessage.value = "Resume uploaded successfully.";
        }

        showForm.value = false;
        editingResume.value = null;

        await loadResumes();
    } catch (error) {
        console.error("Failed to save resume:", error);

        errorMessage.value =
            "Unable to save resume. Please check your input and try again.";
    } finally {
        isSaving.value = false;
    }
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
const deleteResume = async (resume: Resume) => {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${resume.title}"?`,
    );

    if (!confirmed) return;

    errorMessage.value = "";
    successMessage.value = "";
    deletingId.value = resume.id;

    try {
        await resumeRepository.delete(resume.id);

        resumes.value = resumes.value.filter(
            (item) => item.id !== resume.id,
        );

        successMessage.value = "Resume deleted successfully.";
    } catch (error) {
        console.error("Failed to delete resume:", error);
        errorMessage.value = "Unable to delete resume.";
    } finally {
        deletingId.value = null;
    }
};

// Load resumes when component is mounted
onMounted(loadResumes);
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
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

            <button type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
                @click="openCreateForm">
                <Plus :size="18" />
                Add Resume
            </button>
        </div>

        <div class="space-y-5 p-5 sm:p-6">
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
                    {{ resumes.length }}
                    {{ resumes.length === 1 ? "Resume" : "Resumes" }}
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
                    Upload your first resume to keep your professional
                    documents organized.
                </p>

                <button type="button"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700"
                    @click="openCreateForm">
                    <Plus :size="17" />
                    Upload Your First Resume
                </button>
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

                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span
                                        class="rounded-lg bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                        {{ resume.file_type.toUpperCase() }}
                                    </span>

                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-medium"
                                        :class="resume.visibility === 'public'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-slate-100 text-slate-600'
                                            ">
                                        <Globe v-if="
                                            resume.visibility === 'public'
                                        " :size="12" />

                                        <LockKeyhole v-else :size="12" />

                                        {{ resume.visibility }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                            <!-- Download -->
                            <button type="button" :disabled="downloadingId === resume.id"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-violet-200 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-50 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="downloadResume(resume)">
                                <LoaderCircle v-if="downloadingId === resume.id" :size="14" class="animate-spin" />

                                <Download v-else :size="14" />

                                {{
                                    downloadingId === resume.id
                                        ? "Downloading..."
                                        : "Download"
                                }}
                            </button>

                            <!-- Edit -->
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                                @click="openEditForm(resume)">
                                <Pencil :size="14" />
                                Edit
                            </button>

                            <!-- Delete -->
                            <button type="button" :disabled="deletingId === resume.id"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="deleteResume(resume)">
                                <LoaderCircle v-if="deletingId === resume.id" :size="14" class="animate-spin" />

                                <Trash2 v-else :size="14" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-400">
                <Sparkles :size="14" />
                Keep your resume up to date for better job application
                opportunities.
            </div>
        </div>
    </section>

    <!-- Resume Modal -->
    <ResumeForm :show="showForm" :resume="editingResume" :is-saving="isSaving" @close="closeForm" @save="saveResume" />
</template>
