<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Plus, Pencil, Trash2, GraduationCap } from "lucide-vue-next";

import EducationForm from "./EducationForm.vue";
import { educationRepository } from "../repositories/educationRepository";

import type {
    Education,
    CreateEducationData,
} from "../types/education.types";

// Education list
const educations = ref<Education[]>([]);

// Modal state
const showForm = ref(false);
const editingEducation = ref<Education | null>(null);

// Loading states
const isLoading = ref(false);
const isSaving = ref(false);
const deletingId = ref<number | null>(null);

// Error message
const errorMessage = ref("");

// Success message
const successMessage = ref("");

// Get education records from API
const fetchEducations = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        educations.value = await educationRepository.getAll();
    } catch (error: unknown) {
        console.error("Failed to load education:", error);
        errorMessage.value = "Failed to load education records. Please try again.";
    } finally {
        isLoading.value = false;
    }
};

// Load data when component is mounted
onMounted(() => {
    fetchEducations();
});

// Open create form
const openCreateForm = () => {
    editingEducation.value = null;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Open edit form
const openEditForm = (education: Education) => {
    editingEducation.value = education;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Close form
const closeForm = () => {
    showForm.value = false;
    editingEducation.value = null;
};

// Save education
const saveEducation = async (formData: CreateEducationData) => {
    isSaving.value = true;
    errorMessage.value = "";

    const payload: CreateEducationData = {
        institution: formData.institution,
        degree: formData.degree || null,
        field_of_study: formData.field_of_study || null,
        start_date: formData.start_date || null,
        end_date: formData.end_date || null,
        description: formData.description || null,
    };

    try {
        if (editingEducation.value) {
            await educationRepository.update(
                editingEducation.value.id,
                payload,
            );

            successMessage.value = "Education updated successfully.";
        } else {
            await educationRepository.create(payload);

            successMessage.value = "Education added successfully.";
        }

        closeForm();
        await fetchEducations();
    } catch (error: unknown) {
        console.error("Failed to save education:", error);
        errorMessage.value = "Failed to save education. Please check your information and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Delete education
const deleteEducation = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this education?",
    );

    if (!confirmed) return;

    deletingId.value = id;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        await educationRepository.delete(id);

        educations.value = educations.value.filter(
            (education) => education.id !== id,
        );

        successMessage.value = "Education deleted successfully.";
    } catch (error: unknown) {
        console.error("Failed to delete education:", error);
        errorMessage.value = "Failed to delete education. Please try again.";
    } finally {
        deletingId.value = null;
    }
};

// Format date
const formatDate = (date: string | null) => {
    if (!date) return "";

    return new Date(date + "T00:00:00").toLocaleDateString("en-US", {
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <!-- Section Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Education
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage your educational background.
                </p>
            </div>

            <button type="button" @click="openCreateForm"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                <Plus :size="18" />
                Add Education
            </button>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage"
            class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ successMessage }}
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {{ errorMessage }}
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-12">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600"></div>
            <span class="ml-3 text-sm text-slate-500">
                Loading education...
            </span>
        </div>

        <template v-else>

            <!-- Empty State -->
            <div v-if="educations.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                <div class="mb-4 rounded-full bg-indigo-50 p-4">
                    <GraduationCap :size="32" class="text-indigo-600" />
                </div>

                <h3 class="text-base font-semibold text-slate-800">
                    No Education Added
                </h3>

                <p class="mt-2 max-w-sm text-sm text-slate-500">
                    Add your educational background to help employers understand
                    your qualifications.
                </p>

                <button type="button" @click="openCreateForm"
                    class="mt-5 rounded-lg border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                    Add Your First Education
                </button>
            </div>

            <!-- Education List -->
            <div v-else class="space-y-4">
                <div v-for="education in educations" :key="education.id"
                    class="rounded-xl border border-slate-200 p-5 transition hover:border-indigo-200">
                    <div class="flex items-start justify-between gap-4">

                        <!-- Education Information -->
                        <div class="flex gap-4">
                            <div class="hidden rounded-xl bg-indigo-50 p-3 sm:block">
                                <GraduationCap :size="24" class="text-indigo-600" />
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800">
                                    {{ education.institution }}
                                </h3>

                                <p v-if="education.degree" class="mt-1 text-sm font-medium text-slate-600">
                                    {{ education.degree }}
                                </p>

                                <p v-if="education.field_of_study" class="mt-1 text-sm text-slate-500">
                                    {{ education.field_of_study }}
                                </p>

                                <p v-if="education.start_date || education.end_date"
                                    class="mt-2 text-xs text-slate-400">
                                    {{ formatDate(education.start_date) }}
                                    -
                                    {{ education.end_date
                                        ? formatDate(education.end_date)
                                        : "Present"
                                    }}
                                </p>

                                <p v-if="education.description"
                                    class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                                    {{ education.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" @click="openEditForm(education)" aria-label="Edit education"
                                class="rounded-lg p-2 text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600">
                                <Pencil :size="18" />
                            </button>

                            <button type="button" @click="deleteEducation(education.id)"
                                :disabled="deletingId === education.id" aria-label="Delete education"
                                class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                <Trash2 :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </template>

        <!-- Education Form Modal -->
        <EducationForm :show="showForm" :education="editingEducation" @close="closeForm" @save="saveEducation" />

    </section>
</template>
