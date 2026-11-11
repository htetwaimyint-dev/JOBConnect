<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    Clock3,
    FileText,
    Plus,
    Pencil,
    Trash2,
    X,
    Save,
    LoaderCircle,
    CheckCircle2,
    Sparkles,
} from "lucide-vue-next";

import { experienceRepository } from "../repositories/experienceRepository";

import type {
    Experience,
    CreateExperienceData,
} from "../types/experience.types";

const experiences = ref<Experience[]>([]);

const loading = ref(false);
const saving = ref(false);

const errorMessage = ref("");
const successMessage = ref("");

const showForm = ref(false);
const editingId = ref<number | null>(null);

const form = ref<CreateExperienceData>({
    job_title: "",
    company_name: "",
    employment_type: "",
    start_date: null,
    end_date: null,
    is_current: false,
    description: "",
});

const resetForm = () => {
    form.value = {
        job_title: "",
        company_name: "",
        employment_type: "",
        start_date: null,
        end_date: null,
        is_current: false,
        description: "",
    };

    editingId.value = null;
    showForm.value = false;
};

const loadExperiences = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        experiences.value = await experienceRepository.getAll();
    } catch (error) {
        errorMessage.value = "Failed to load work experiences.";
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const openCreateForm = () => {
    resetForm();
    showForm.value = true;
};

const openEditForm = (experience: Experience) => {
    editingId.value = experience.id;

    form.value = {
        job_title: experience.job_title,
        company_name: experience.company_name,
        employment_type: experience.employment_type,
        start_date: experience.start_date,
        end_date: experience.end_date,
        is_current: experience.is_current,
        description: experience.description,
    };

    showForm.value = true;
};

const saveExperience = async () => {
    saving.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    const data: CreateExperienceData = {
        ...form.value,
        end_date: form.value.is_current ? null : form.value.end_date,
    };

    try {
        if (editingId.value !== null) {
            await experienceRepository.update(editingId.value, data);
            successMessage.value = "Work experience updated successfully.";
        } else {
            await experienceRepository.create(data);
            successMessage.value = "Work experience added successfully.";
        }

        resetForm();
        await loadExperiences();
    } catch (error) {
        errorMessage.value = "Failed to save work experience. Please check your inputs.";
        console.error(error);
    } finally {
        saving.value = false;
    }
};

const deleteExperience = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this work experience?",
    );

    if (!confirmed) return;

    errorMessage.value = "";
    successMessage.value = "";

    try {
        await experienceRepository.delete(id);

        successMessage.value = "Work experience deleted successfully.";

        await loadExperiences();
    } catch (error) {
        errorMessage.value = "Failed to delete work experience.";
        console.error(error);
    }
};

onMounted(() => {
    loadExperiences();
});
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:p-6">
            <div class="flex items-start gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                    <BriefcaseBusiness :size="24" />
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Work Experience
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showcase your professional journey and achievements.
                    </p>
                </div>
            </div>

            <button v-if="!showForm" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700"
                @click="openCreateForm">
                <Plus :size="18" />
                Add Experience
            </button>
        </div>

        <div class="p-5 sm:p-6">

            <!-- Error Message -->
            <div v-if="errorMessage"
                class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <X :size="18" class="mt-0.5 shrink-0" />
                <p>{{ errorMessage }}</p>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage"
                class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <CheckCircle2 :size="18" class="mt-0.5 shrink-0" />
                <p>{{ successMessage }}</p>
            </div>

            <!-- Experience Form -->
            <form v-if="showForm" class="mb-7 rounded-2xl border border-orange-100 bg-orange-50/30 p-5 sm:p-6"
                @submit.prevent="saveExperience">
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                            <Pencil v-if="editingId !== null" :size="19" />
                            <Plus v-else :size="19" />
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-800">
                                {{ editingId !== null ? "Edit Experience" : "Add Experience" }}
                            </h3>

                            <p class="text-xs text-slate-500">
                                Enter your work experience details.
                            </p>
                        </div>
                    </div>

                    <button type="button"
                        class="rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-slate-700"
                        @click="resetForm">
                        <X :size="20" />
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <!-- Job Title -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <BriefcaseBusiness :size="16" class="text-orange-600" />
                            Job Title <span class="text-red-500">*</span>
                        </label>

                        <input v-model="form.job_title" type="text" maxlength="255" required
                            placeholder="e.g. Junior Web Developer"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <Building2 :size="16" class="text-orange-600" />
                            Company Name <span class="text-red-500">*</span>
                        </label>

                        <input v-model="form.company_name" type="text" maxlength="255" required
                            placeholder="Enter company name"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                    </div>

                    <!-- Employment Type -->
                    <div class="sm:col-span-2">
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <Clock3 :size="16" class="text-orange-600" />
                            Employment Type
                        </label>

                        <select v-model="form.employment_type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                            <option value="">Select employment type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Temporary">Temporary</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <CalendarDays :size="16" class="text-orange-600" />
                            Start Date
                        </label>

                        <input v-model="form.start_date" type="date" :max="form.end_date || undefined"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <CalendarDays :size="16" class="text-orange-600" />
                            End Date
                        </label>

                        <input v-model="form.end_date" type="date" :min="form.start_date || undefined"
                            :disabled="form.is_current"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" />
                    </div>

                    <!-- Current Job -->
                    <div class="sm:col-span-2">
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-orange-100 bg-white p-4 transition hover:border-orange-300">
                            <input v-model="form.is_current" type="checkbox"
                                class="h-5 w-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500" />

                            <div>
                                <span class="block text-sm font-semibold text-slate-700">
                                    I currently work here
                                </span>

                                <span class="mt-0.5 block text-xs text-slate-500">
                                    Select this if this is your current workplace.
                                </span>
                            </div>
                        </label>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <FileText :size="16" class="text-orange-600" />
                            Description
                        </label>

                        <textarea v-model="form.description" rows="5"
                            placeholder="Describe your responsibilities, projects, and achievements..."
                            class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-100"></textarea>

                        <p class="mt-1.5 text-xs text-slate-400">
                            Highlight your main responsibilities and achievements.
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex flex-col-reverse justify-end gap-3 border-t border-orange-100 pt-5 sm:flex-row">
                    <button type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        @click="resetForm">
                        <X :size="17" />
                        Cancel
                    </button>

                    <button type="submit" :disabled="saving"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <LoaderCircle v-if="saving" :size="17" class="animate-spin" />
                        <Save v-else :size="17" />

                        {{ saving ? "Saving..." : "Save Experience" }}
                    </button>
                </div>
            </form>

            <!-- Loading -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-12">
                <LoaderCircle :size="30" class="animate-spin text-orange-500" />

                <p class="mt-3 text-sm font-medium text-slate-500">
                    Loading work experiences...
                </p>
            </div>

            <!-- Empty State -->
            <div v-else-if="experiences.length === 0 && !showForm"
                class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-5 py-12 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-500">
                    <BriefcaseBusiness :size="30" />
                </div>

                <h3 class="mt-4 font-bold text-slate-800">
                    No Work Experience Yet
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    Add your previous or current work experience to help employers understand your professional
                    background.
                </p>

                <button type="button"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
                    @click="openCreateForm">
                    <Plus :size="18" />
                    Add Your First Experience
                </button>
            </div>

            <!-- Experience List -->
            <div v-else class="space-y-4">

                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-700">
                        Your Experience
                    </p>

                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                        {{ experiences.length }} {{ experiences.length === 1 ? "Entry" : "Entries" }}
                    </span>
                </div>

                <div v-for="experience in experiences" :key="experience.id"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-orange-200 hover:shadow-md sm:p-6">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row">

                        <!-- Experience Information -->
                        <div class="flex min-w-0 gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                                <BriefcaseBusiness :size="23" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="break-words text-base font-bold text-slate-800">
                                    {{ experience.job_title }}
                                </h3>

                                <p class="mt-1 flex items-center gap-2 text-sm font-medium text-slate-600">
                                    <Building2 :size="15" class="shrink-0 text-slate-400" />
                                    {{ experience.company_name }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span v-if="experience.employment_type"
                                        class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                        {{ experience.employment_type }}
                                    </span>

                                    <span v-if="experience.is_current"
                                        class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        <CheckCircle2 :size="13" />
                                        Currently Working
                                    </span>
                                </div>

                                <!-- Dates -->
                                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                                    <CalendarDays :size="15" class="shrink-0 text-orange-500" />

                                    <span>
                                        {{ experience.start_date || "Start date not specified" }}
                                        —
                                        {{ experience.is_current ? "Present" : (experience.end_date || "Not specified")
                                        }}
                                    </span>
                                </div>

                                <!-- Description -->
                                <div v-if="experience.description"
                                    class="mt-4 flex items-start gap-2 border-t border-slate-100 pt-4">
                                    <FileText :size="16" class="mt-0.5 shrink-0 text-slate-400" />

                                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">
                                        {{ experience.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex shrink-0 items-start gap-2 sm:justify-end">
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700"
                                @click="openEditForm(experience)">
                                <Pencil :size="14" />
                                Edit
                            </button>

                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                @click="deleteExperience(experience.id)">
                                <Trash2 :size="14" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Small Footer -->
            <div class="mt-6 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-400">
                <Sparkles :size="14" />
                Keep your work experience updated to improve your job profile.
            </div>
        </div>
    </section>
</template>
