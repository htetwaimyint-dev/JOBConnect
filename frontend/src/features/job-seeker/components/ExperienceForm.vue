<script setup lang="ts">
import { ref, watch } from "vue";
import AppModal from "@/components/common/AppModal.vue";

import type {
    Experience,
    CreateExperienceData,
} from "../types/experience.types";

interface ExperienceFormData {
    job_title: string;
    company_name: string;
    employment_type: string;
    start_date: string;
    end_date: string;
    is_current: boolean;
    description: string;
}

const props = defineProps<{
    show: boolean;
    experience?: Experience | null;
    isSaving?: boolean;
}>();

const emit = defineEmits<{
    close: [];
    save: [data: CreateExperienceData];
}>();

const createEmptyForm = (): ExperienceFormData => ({
    job_title: "",
    company_name: "",
    employment_type: "",
    start_date: "",
    end_date: "",
    is_current: false,
    description: "",
});

const form = ref<ExperienceFormData>(createEmptyForm());

watch(
    () => [props.show, props.experience] as const,
    ([isOpen, experience]) => {
        if (!isOpen) return;

        if (experience) {
            form.value = {
                job_title: experience.job_title ?? "",
                company_name: experience.company_name ?? "",
                employment_type: experience.employment_type ?? "",
                start_date: experience.start_date ?? "",
                end_date: experience.end_date ?? "",
                is_current: experience.is_current ?? false,
                description: experience.description ?? "",
            };
        } else {
            resetForm();
        }
    },
    { immediate: true },
);

const resetForm = () => {
    form.value = createEmptyForm();
};

const closeForm = () => {
    resetForm();
    emit("close");
};

const handleSubmit = () => {
    emit("save", {
        job_title: form.value.job_title,
        company_name: form.value.company_name,
        employment_type: form.value.employment_type || null,
        start_date: form.value.start_date || null,
        end_date: form.value.is_current
            ? null
            : form.value.end_date || null,
        is_current: form.value.is_current,
        description: form.value.description || null,
    });
};
</script>

<template>
    <AppModal :show="show" :title="experience ? 'Edit Experience' : 'Add Experience'" :description="experience
            ? 'Update your work experience information.'
            : 'Add your work experience to your profile.'
        " max-width="max-w-xl" @close="closeForm">
        <form @submit.prevent="handleSubmit" class="space-y-5">

            <!-- Job Title -->
            <div>
                <label for="job_title" class="mb-2 block text-sm font-semibold text-slate-700">
                    Job Title <span class="text-red-500">*</span>
                </label>

                <input id="job_title" v-model.trim="form.job_title" type="text" placeholder="e.g. Software Developer"
                    required maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
            </div>

            <!-- Company Name -->
            <div>
                <label for="company_name" class="mb-2 block text-sm font-semibold text-slate-700">
                    Company Name <span class="text-red-500">*</span>
                </label>

                <input id="company_name" v-model.trim="form.company_name" type="text"
                    placeholder="e.g. ABC Technology Company" required maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
            </div>

            <!-- Employment Type -->
            <div>
                <label for="employment_type" class="mb-2 block text-sm font-semibold text-slate-700">
                    Employment Type
                </label>

                <select id="employment_type" v-model="form.employment_type"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    <option value="">Select employment type</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Contract">Contract</option>
                    <option value="Internship">Internship</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Temporary">Temporary</option>
                </select>
            </div>

            <!-- Start Date and End Date -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="experience_start_date" class="mb-2 block text-sm font-semibold text-slate-700">
                        Start Date
                    </label>

                    <input id="experience_start_date" v-model="form.start_date" type="date"
                        :max="form.end_date || undefined"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                </div>

                <div>
                    <label for="experience_end_date" class="mb-2 block text-sm font-semibold text-slate-700">
                        End Date
                    </label>

                    <input id="experience_end_date" v-model="form.end_date" type="date"
                        :min="form.start_date || undefined" :disabled="form.is_current"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" />
                </div>
            </div>

            <!-- Currently Working -->
            <div>
                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                    <input v-model="form.is_current" type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        @change="form.is_current && (form.end_date = '')" />

                    I currently work here
                </label>
            </div>

            <!-- Description -->
            <div>
                <label for="experience_description" class="mb-2 block text-sm font-semibold text-slate-700">
                    Description
                </label>

                <textarea id="experience_description" v-model.trim="form.description" rows="4"
                    placeholder="Describe your responsibilities, achievements, or work experience..."
                    class="w-full resize-none rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" @click="closeForm" :disabled="isSaving"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50">
                    Cancel
                </button>

                <button type="submit" :disabled="isSaving"
                    class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ isSaving ? "Saving..." : experience ? "Update Experience" : "Save Experience" }}
                </button>
            </div>
        </form>
    </AppModal>
</template>
