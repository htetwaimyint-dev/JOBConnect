<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    GraduationCap,
    Plus,
    Building2,
    Award,
    BookOpen,
    CalendarDays,
    FileText,
    Pencil,
    Trash2,
    X,
    Save,
    LoaderCircle,
    AlertCircle,
    CheckCircle2,
    Sparkles,
    Clock3,
} from "lucide-vue-next";

import { educationRepository } from "../repositories/educationRepository";

import type {
    Education,
    CreateEducationData,
} from "../types/education.types";

const educations = ref<Education[]>([]);

const loading = ref(false);
const saving = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

const showForm = ref(false);
const editingId = ref<number | null>(null);

const form = ref<CreateEducationData>({
    institution: "",
    degree: "",
    field_of_study: "",
    start_date: "",
    end_date: null,
    description: "",
});

const resetForm = () => {
    form.value = {
        institution: "",
        degree: "",
        field_of_study: "",
        start_date: "",
        end_date: null,
        description: "",
    };

    editingId.value = null;
    showForm.value = false;
};

const loadEducations = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        educations.value = await educationRepository.getAll();
    } catch (error) {
        errorMessage.value = "Failed to load education records.";
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const openCreateForm = () => {
    resetForm();
    showForm.value = true;
};

const openEditForm = (education: Education) => {
    editingId.value = education.id;

    form.value = {
        institution: education.institution,
        degree: education.degree,
        field_of_study: education.field_of_study,
        start_date: education.start_date,
        end_date: education.end_date,
        description: education.description,
    };

    showForm.value = true;
};

const saveEducation = async () => {
    saving.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        if (editingId.value !== null) {
            await educationRepository.update(editingId.value, form.value);
            successMessage.value = "Education updated successfully.";
        } else {
            await educationRepository.create(form.value);
            successMessage.value = "Education added successfully.";
        }

        resetForm();
        await loadEducations();
    } catch (error) {
        errorMessage.value = "Failed to save education. Please check your inputs.";
        console.error(error);
    } finally {
        saving.value = false;
    }
};

const deleteEducation = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this education record?",
    );

    if (!confirmed) return;

    errorMessage.value = "";
    successMessage.value = "";

    try {
        await educationRepository.delete(id);

        successMessage.value = "Education deleted successfully.";

        await loadEducations();
    } catch (error) {
        errorMessage.value = "Failed to delete education.";
        console.error(error);
    }
};

onMounted(() => {
    loadEducations();
});
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:p-6">
            <div class="flex items-start gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <GraduationCap :size="25" />
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Education
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showcase your academic background and qualifications.
                    </p>
                </div>
            </div>

            <button v-if="!showForm" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 hover:shadow-md"
                @click="openCreateForm">
                <Plus :size="18" />
                Add Education
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
                class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                <CheckCircle2 :size="19" class="mt-0.5 shrink-0" />
                <p>{{ successMessage }}</p>
            </div>

            <!-- Education Form -->
            <form v-if="showForm" class="overflow-hidden rounded-2xl border border-violet-100 bg-slate-50/70"
                @submit.prevent="saveEducation">
                <!-- Form Header -->
                <div class="flex items-center justify-between border-b border-violet-100 bg-violet-50/70 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-violet-600 shadow-sm">
                            <GraduationCap :size="21" />
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-800">
                                {{ editingId !== null ? "Edit Education" : "Add Education" }}
                            </h3>

                            <p class="text-xs text-slate-500">
                                Enter your educational information below.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                        class="rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-slate-700"
                        aria-label="Close form" @click="resetForm">
                        <X :size="20" />
                    </button>
                </div>

                <div class="space-y-5 p-5">

                    <!-- Institution -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <Building2 :size="16" class="text-violet-600" />
                            Institution / University
                            <span class="text-red-500">*</span>
                        </label>

                        <input v-model="form.institution" type="text" required
                            placeholder="e.g. University of Computer Studies"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100" />
                    </div>

                    <!-- Degree and Field -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <Award :size="16" class="text-violet-600" />
                                Degree
                                <span class="text-red-500">*</span>
                            </label>

                            <input v-model="form.degree" type="text" required
                                placeholder="e.g. Bachelor of Computer Science"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100" />
                        </div>

                        <div>
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <BookOpen :size="16" class="text-violet-600" />
                                Field of Study
                            </label>

                            <input v-model="form.field_of_study" type="text" placeholder="e.g. Computer Science"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100" />
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <CalendarDays :size="16" class="text-violet-600" />
                                Start Date
                                <span class="text-red-500">*</span>
                            </label>

                            <input v-model="form.start_date" type="date" required :max="form.end_date || undefined"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100" />
                        </div>

                        <div>
                            <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <CalendarDays :size="16" class="text-violet-600" />
                                End Date
                            </label>

                            <input v-model="form.end_date" type="date" :min="form.start_date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100" />

                            <p class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                                <Clock3 :size="13" />
                                Leave empty if you are currently studying.
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <FileText :size="16" class="text-violet-600" />
                            Description
                        </label>

                        <textarea v-model="form.description" rows="4"
                            placeholder="Describe your achievements, activities, or relevant coursework..."
                            class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
                        <button type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                            @click="resetForm">
                            <X :size="17" />
                            Cancel
                        </button>

                        <button type="submit" :disabled="saving"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <LoaderCircle v-if="saving" :size="17" class="animate-spin" />

                            <Save v-else :size="17" />

                            {{ saving ? "Saving..." : editingId !== null ? "Update Education" : "Save Education" }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Loading -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-12">
                <LoaderCircle :size="30" class="animate-spin text-violet-600" />

                <p class="mt-3 text-sm font-medium text-slate-500">
                    Loading education records...
                </p>
            </div>

            <!-- Empty State -->
            <div v-else-if="educations.length === 0 && !showForm"
                class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-5 py-12 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-50 text-violet-500">
                    <GraduationCap :size="32" />
                </div>

                <h3 class="mt-4 font-bold text-slate-800">
                    No Education Added Yet
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Add your university, degree, and academic background to help employers understand your
                    qualifications.
                </p>

                <button type="button"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700"
                    @click="openCreateForm">
                    <Plus :size="18" />
                    Add Your First Education
                </button>
            </div>

            <!-- Education List -->
            <div v-else-if="educations.length > 0" class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">
                            Your Education History
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ educations.length }} education record(s)
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                        <Sparkles :size="18" />
                    </div>
                </div>

                <div v-for="education in educations" :key="education.id"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition duration-200 hover:border-violet-200 hover:shadow-md">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row">

                        <!-- Education Details -->
                        <div class="flex min-w-0 gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition group-hover:bg-violet-100">
                                <GraduationCap :size="25" />
                            </div>

                            <div class="min-w-0">
                                <h4 class="break-words font-bold text-slate-800">
                                    {{ education.degree }}
                                </h4>

                                <p class="mt-1 flex items-center gap-2 text-sm font-medium text-slate-600">
                                    <Building2 :size="15" class="shrink-0 text-slate-400" />
                                    {{ education.institution }}
                                </p>

                                <p v-if="education.field_of_study"
                                    class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                                    <BookOpen :size="15" class="shrink-0 text-slate-400" />
                                    {{ education.field_of_study }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                                        <CalendarDays :size="13" />
                                        {{ education.start_date }} — {{ education.end_date || "Present" }}
                                    </span>

                                    <span v-if="!education.end_date"
                                        class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                                        Currently Studying
                                    </span>
                                </div>

                                <p v-if="education.description"
                                    class="mt-4 max-w-2xl whitespace-pre-line text-sm leading-6 text-slate-600">
                                    {{ education.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex shrink-0 items-start gap-2 sm:justify-end">
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700"
                                @click="openEditForm(education)">
                                <Pencil :size="14" />
                                Edit
                            </button>

                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:border-red-200 hover:bg-red-50"
                                @click="deleteEducation(education.id)">
                                <Trash2 :size="14" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
