<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Plus, Pencil, Trash2, BriefcaseBusiness } from "lucide-vue-next";

import ExperienceForm from "./ExperienceForm.vue";
import { experienceRepository } from "../repositories/experienceRepository";

import type {
    Experience,
    CreateExperienceData,
} from "../types/experience.types";

const experiences = ref<Experience[]>([]);

// Modal state
const showForm = ref(false);
const editingExperience = ref<Experience | null>(null);

// Loading states
const isLoading = ref(false);
const isSaving = ref(false);
const deletingId = ref<number | null>(null);

// Messages
const errorMessage = ref("");
const successMessage = ref("");

// Fetch experiences
const fetchExperiences = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        experiences.value = await experienceRepository.getAll();
    } catch (error: unknown) {
        console.error("Failed to load experiences:", error);
        errorMessage.value = "Failed to load experience records. Please try again.";
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchExperiences();
});

// Open create modal
const openCreateForm = () => {
    editingExperience.value = null;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Open edit modal
const openEditForm = (experience: Experience) => {
    editingExperience.value = experience;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Close modal
const closeForm = () => {
    showForm.value = false;
    editingExperience.value = null;
};

// Save experience
const saveExperience = async (formData: CreateExperienceData) => {
    isSaving.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    const payload: CreateExperienceData = {
        job_title: formData.job_title,
        company_name: formData.company_name,
        employment_type: formData.employment_type || null,
        start_date: formData.start_date || null,
        end_date: formData.is_current
            ? null
            : formData.end_date || null,
        is_current: formData.is_current ?? false,
        description: formData.description || null,
    };

    try {
        if (editingExperience.value) {
            await experienceRepository.update(
                editingExperience.value.id,
                payload,
            );

            successMessage.value = "Experience updated successfully.";
        } else {
            await experienceRepository.create(payload);

            successMessage.value = "Experience added successfully.";
        }

        closeForm();
        await fetchExperiences();
    } catch (error: unknown) {
        console.error("Failed to save experience:", error);
        errorMessage.value = "Failed to save experience. Please check your information and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Delete experience
const deleteExperience = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this experience?",
    );

    if (!confirmed) return;

    deletingId.value = id;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        await experienceRepository.delete(id);

        experiences.value = experiences.value.filter(
            (experience) => experience.id !== id,
        );

        successMessage.value = "Experience deleted successfully.";
    } catch (error: unknown) {
        console.error("Failed to delete experience:", error);
        errorMessage.value = "Failed to delete experience. Please try again.";
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
                    Work Experience
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage your professional work history.
                </p>
            </div>

            <button type="button" @click="openCreateForm"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                <Plus :size="18" />
                Add Experience
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
                Loading experiences...
            </span>
        </div>

        <template v-else>

            <!-- Empty State -->
            <div v-if="experiences.length === 0"
                class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                <div class="mb-4 rounded-full bg-indigo-50 p-4">
                    <BriefcaseBusiness :size="32" class="text-indigo-600" />
                </div>

                <h3 class="text-base font-semibold text-slate-800">
                    No Experience Added
                </h3>

                <p class="mt-2 max-w-sm text-sm text-slate-500">
                    Add your work experience to help employers understand
                    your professional background.
                </p>

                <button type="button" @click="openCreateForm"
                    class="mt-5 rounded-lg border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                    Add Your First Experience
                </button>
            </div>

            <!-- Experience List -->
            <div v-else class="space-y-4">
                <div v-for="experience in experiences" :key="experience.id"
                    class="rounded-xl border border-slate-200 p-5 transition hover:border-indigo-200">
                    <div class="flex items-start justify-between gap-4">

                        <!-- Experience Information -->
                        <div class="flex gap-4">
                            <div class="hidden rounded-xl bg-indigo-50 p-3 sm:block">
                                <BriefcaseBusiness :size="24" class="text-indigo-600" />
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800">
                                    {{ experience.job_title }}
                                </h3>

                                <p class="mt-1 text-sm font-medium text-slate-600">
                                    {{ experience.company_name }}
                                </p>

                                <p v-if="experience.employment_type" class="mt-1 text-sm text-slate-500">
                                    {{ experience.employment_type }}
                                </p>

                                <p v-if="experience.start_date || experience.end_date || experience.is_current"
                                    class="mt-2 text-xs text-slate-400">
                                    {{ formatDate(experience.start_date) }}
                                    -
                                    {{ experience.is_current
                                        ? "Present"
                                        : formatDate(experience.end_date) || "Not specified"
                                    }}
                                </p>

                                <p v-if="experience.description"
                                    class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                                    {{ experience.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" @click="openEditForm(experience)" aria-label="Edit experience"
                                class="rounded-lg p-2 text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600">
                                <Pencil :size="18" />
                            </button>

                            <button type="button" @click="deleteExperience(experience.id)"
                                :disabled="deletingId === experience.id" aria-label="Delete experience"
                                class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                <Trash2 :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Experience Form Modal -->
        <ExperienceForm :show="showForm" :experience="editingExperience" :is-saving="isSaving" @close="closeForm"
            @save="saveExperience" />

    </section>
</template>
