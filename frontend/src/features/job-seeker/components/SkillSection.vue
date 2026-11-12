<script setup lang="ts">
import { ref, onMounted } from "vue";
import {
    Sparkles,
    Code2,
    Plus,
    Pencil,
    Trash2,
    LoaderCircle,
    AlertCircle,
    CheckCircle2,
    Award,
    Lightbulb,
    Zap,
    Star,
    ShieldCheck,
} from "lucide-vue-next";

import SkillForm from "./SkillForm.vue";
import { skillsRepository } from "../repositories/skillsRepository";

import type {
    Skill,
    SkillLevel,
    CreateSkillData,
} from "../types/skill.types";

const skills = ref<Skill[]>([]);

// Modal state
const showForm = ref(false);
const editingSkill = ref<Skill | null>(null);

// Loading states
const isLoading = ref(false);
const isSaving = ref(false);
const deletingId = ref<number | null>(null);

// Messages
const errorMessage = ref("");
const successMessage = ref("");

const getLevelStyle = (level: SkillLevel) => {
    const styles: Record<SkillLevel, string> = {
        Beginner: "bg-sky-50 text-sky-700 border-sky-200",
        Intermediate: "bg-amber-50 text-amber-700 border-amber-200",
        Advanced: "bg-violet-50 text-violet-700 border-violet-200",
        Expert: "bg-emerald-50 text-emerald-700 border-emerald-200",
    };

    return styles[level];
};

const getLevelWidth = (level: SkillLevel) => {
    const widths: Record<SkillLevel, string> = {
        Beginner: "w-1/4",
        Intermediate: "w-2/4",
        Advanced: "w-3/4",
        Expert: "w-full",
    };

    return widths[level];
};

const getLevelIcon = (level: SkillLevel) => {
    const icons = {
        Beginner: Lightbulb,
        Intermediate: Zap,
        Advanced: Star,
        Expert: ShieldCheck,
    };

    return icons[level];
};

// Fetch skills
const fetchSkills = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        skills.value = await skillsRepository.getAll();
    } catch (error: unknown) {
        console.error("Failed to load skills:", error);
        errorMessage.value = "Unable to load skills. Please try again.";
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchSkills();
});

// Open create modal
const openCreateForm = () => {
    editingSkill.value = null;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Open edit modal
const openEditForm = (skill: Skill) => {
    editingSkill.value = skill;
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Close modal
const closeForm = () => {
    showForm.value = false;
    editingSkill.value = null;
};

// Save skill
const saveSkill = async (formData: CreateSkillData) => {
    isSaving.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    const payload: CreateSkillData = {
        name: formData.name.trim(),
        skill_level: formData.skill_level,
    };

    try {
        if (editingSkill.value) {
            await skillsRepository.update(
                editingSkill.value.id,
                payload,
            );

            successMessage.value = "Skill updated successfully.";
        } else {
            await skillsRepository.create(payload);

            successMessage.value = "Skill added successfully.";
        }

        closeForm();
        await fetchSkills();
    } catch (error: unknown) {
        console.error("Failed to save skill:", error);
        errorMessage.value =
            "Unable to save skill. Please check your input and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Delete skill
const deleteSkill = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this skill?",
    );

    if (!confirmed) return;

    deletingId.value = id;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        await skillsRepository.delete(id);

        skills.value = skills.value.filter(
            (skill) => skill.id !== id,
        );

        successMessage.value = "Skill deleted successfully.";
    } catch (error: unknown) {
        console.error("Failed to delete skill:", error);
        errorMessage.value = "Unable to delete skill. Please try again.";
    } finally {
        deletingId.value = null;
    }
};
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5 sm:p-6">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <Sparkles :size="25" />
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Professional Skills
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Highlight your technical and professional abilities.
                    </p>
                </div>
            </div>

            <button type="button" @click="openCreateForm"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                <Plus :size="18" />
                Add Skill
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

            <!-- Skills List Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-800">
                        <Award :size="17" class="text-emerald-600" />
                        My Skills
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ skills.length }} skill(s) added to your profile
                    </p>
                </div>

                <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    {{ skills.length }} Skills
                </span>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="flex flex-col items-center justify-center py-10">
                <LoaderCircle :size="28" class="animate-spin text-emerald-600" />

                <p class="mt-3 text-sm text-slate-500">
                    Loading skills...
                </p>
            </div>

            <template v-else>

                <!-- Empty State -->
                <div v-if="skills.length === 0"
                    class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-5 py-10 text-center">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                        <Code2 :size="30" />
                    </div>

                    <h4 class="mt-4 font-bold text-slate-800">
                        No Skills Added Yet
                    </h4>

                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                        Add your technical and professional skills to help employers understand your abilities.
                    </p>

                    <button type="button" @click="openCreateForm"
                        class="mt-5 rounded-lg border border-emerald-600 px-4 py-2 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50">
                        Add Your First Skill
                    </button>
                </div>

                <!-- Skills Grid -->
                <div v-else class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div v-for="skill in skills" :key="skill.id"
                        class="group rounded-2xl border border-slate-200 bg-white p-4 transition duration-200 hover:border-emerald-200 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">

                            <!-- Skill Information -->
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-100">
                                    <Code2 :size="21" />
                                </div>

                                <div class="min-w-0">
                                    <h4 class="break-words font-bold text-slate-800">
                                        {{ skill.name }}
                                    </h4>

                                    <span
                                        class="mt-1 inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-semibold"
                                        :class="getLevelStyle(skill.skill_level)">
                                        <component :is="getLevelIcon(skill.skill_level)" :size="13" />
                                        {{ skill.skill_level }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex shrink-0 items-center gap-1">
                                <button type="button" title="Edit skill" aria-label="Edit skill"
                                    @click="openEditForm(skill)"
                                    class="rounded-lg p-2 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-700">
                                    <Pencil :size="17" />
                                </button>

                                <button type="button" title="Delete skill" aria-label="Delete skill"
                                    :disabled="deletingId === skill.id" @click="deleteSkill(skill.id)"
                                    class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                                    <Trash2 :size="17" />
                                </button>
                            </div>
                        </div>

                        <!-- Proficiency Indicator -->
                        <div class="mt-4">
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                    :class="getLevelWidth(skill.skill_level)"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Skill Form Modal -->
        <SkillForm :show="showForm" :skill="editingSkill" :is-saving="isSaving" @close="closeForm" @save="saveSkill" />
    </section>
</template>
