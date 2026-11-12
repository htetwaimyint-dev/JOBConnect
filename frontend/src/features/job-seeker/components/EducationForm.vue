<script setup lang="ts">
import { ref, watch } from "vue";
import AppModal from "@/components/common/AppModal.vue";

// Education data from Backend
interface Education {
    id: number;
    institution: string;
    degree: string | null;
    field_of_study: string | null;
    start_date: string | null;
    end_date: string | null;
    description: string | null;
}

// Form data sent to Parent component
interface EducationFormData {
    institution: string;
    degree: string;
    field_of_study: string;
    start_date: string;
    end_date: string;
    description: string;
}

// Props
const props = defineProps<{
    show: boolean;
    education?: Education | null;
}>();

// Events
const emit = defineEmits<{
    close: [];
    save: [data: EducationFormData];
}>();

// Create empty form
const createEmptyForm = (): EducationFormData => ({
    institution: "",
    degree: "",
    field_of_study: "",
    start_date: "",
    end_date: "",
    description: "",
});

// Form state
const form = ref<EducationFormData>(createEmptyForm());

// Watch modal visibility and selected education
watch(
    () => [props.show, props.education] as const,
    ([isOpen, education]) => {
        if (!isOpen) return;

        if (education) {
            // Edit existing education
            form.value = {
                institution: education.institution ?? "",
                degree: education.degree ?? "",
                field_of_study: education.field_of_study ?? "",
                start_date: education.start_date ?? "",
                end_date: education.end_date ?? "",
                description: education.description ?? "",
            };
        } else {
            // Create new education
            resetForm();
        }
    },
    { immediate: true },
);

// Reset form
const resetForm = () => {
    form.value = createEmptyForm();
};

// Close modal
const closeForm = () => {
    resetForm();
    emit("close");
};

// Submit form
const handleSubmit = () => {
    emit("save", { ...form.value });
};
</script>

<template>
    <AppModal :show="show" :title="education ? 'Edit Education' : 'Add Education'" :description="education
        ? 'Update your education information.'
        : 'Add your educational background to your profile.'
        " max-width="max-w-xl" @close="closeForm">
        <form @submit.prevent="handleSubmit" class="space-y-5">

            <!-- Institution -->
            <div>
                <label for="institution" class="mb-2 block text-sm font-semibold text-slate-700">
                    Institution Name
                    <span class="text-red-500">*</span>
                </label>

                <input id="institution" v-model.trim="form.institution" type="text"
                    placeholder="e.g. University of Yangon" required maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
            </div>

            <!-- Degree -->
            <div>
                <label for="degree" class="mb-2 block text-sm font-semibold text-slate-700">
                    Degree
                </label>

                <input id="degree" v-model.trim="form.degree" type="text"
                    placeholder="e.g. Bachelor of Computer Science" maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
            </div>

            <!-- Field of Study -->
            <div>
                <label for="field_of_study" class="mb-2 block text-sm font-semibold text-slate-700">
                    Field of Study
                </label>

                <input id="field_of_study" v-model.trim="form.field_of_study" type="text"
                    placeholder="e.g. Information Technology" maxlength="255"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
            </div>

            <!-- Start Date and End Date -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="mb-2 block text-sm font-semibold text-slate-700">
                        Start Date
                    </label>

                    <input id="start_date" v-model="form.start_date" type="date" :max="form.end_date || undefined"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="mb-2 block text-sm font-semibold text-slate-700">
                        End Date
                    </label>

                    <input id="end_date" v-model="form.end_date" type="date" :min="form.start_date || undefined"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />

                    <p class="mt-1 text-xs text-slate-400">
                        Leave empty if you are still studying.
                    </p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">
                    Description
                </label>

                <textarea id="description" v-model.trim="form.description" rows="4"
                    placeholder="Describe your education, achievements, or activities..."
                    class="w-full resize-none rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">

                <button type="button" @click="closeForm"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    {{ education ? "Update Education" : "Save Education" }}
                </button>

            </div>
        </form>
    </AppModal>
</template>
