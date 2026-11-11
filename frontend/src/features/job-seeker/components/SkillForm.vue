<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    Sparkles,
    Code2,
    Plus,
    Pencil,
    Trash2,
    X,
    Save,
    LoaderCircle,
    AlertCircle,
    CheckCircle2,
    Award,
    Zap,
    Star,
    ShieldCheck,
    Lightbulb,
} from "lucide-vue-next";

import { skillsRepository } from "../repositories/skillsRepository";

import type {
    Skill,
    SkillLevel,
    CreateSkillData,
} from "../types/skill.types";

const skills = ref<Skill[]>([]);

const form = ref<CreateSkillData>({
    name: "",
    skill_level: "Beginner",
});

const editingSkillId = ref<number | null>(null);

const isLoading = ref(false);
const isSaving = ref(false);

const errorMessage = ref("");
const successMessage = ref("");

const skillLevels: SkillLevel[] = [
    "Beginner",
    "Intermediate",
    "Advanced",
    "Expert",
];

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

// Load skills
const loadSkills = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        skills.value = await skillsRepository.getAll();
    } catch (error) {
        console.error("Failed to load skills:", error);
        errorMessage.value = "Unable to load skills.";
    } finally {
        isLoading.value = false;
    }
};

// Reset form
const resetForm = () => {
    form.value = {
        name: "",
        skill_level: "Beginner",
    };

    editingSkillId.value = null;
};

// Add or Update skill
const handleSubmit = async () => {
    errorMessage.value = "";
    successMessage.value = "";

    const name = form.value.name.trim();

    if (name.length < 2) {
        errorMessage.value = "Skill name must be at least 2 characters.";
        return;
    }

    isSaving.value = true;

    try {
        if (editingSkillId.value !== null) {
            const updatedSkill = await skillsRepository.update(
                editingSkillId.value,
                {
                    name,
                    skill_level: form.value.skill_level,
                },
            );

            const index = skills.value.findIndex(
                (skill) => skill.id === updatedSkill.id,
            );

            if (index !== -1) {
                skills.value[index] = updatedSkill;
            }

            successMessage.value = "Skill updated successfully.";
        } else {
            const newSkill = await skillsRepository.create({
                name,
                skill_level: form.value.skill_level,
            });

            skills.value.unshift(newSkill);

            successMessage.value = "Skill added successfully.";
        }

        resetForm();
    } catch (error) {
        console.error("Failed to save skill:", error);
        errorMessage.value =
            "Unable to save skill. Please check your input and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Edit skill
const editSkill = (skill: Skill) => {
    form.value = {
        name: skill.name,
        skill_level: skill.skill_level,
    };

    editingSkillId.value = skill.id;

    errorMessage.value = "";
    successMessage.value = "";
};

// Delete skill
const deleteSkill = async (id: number) => {
    const confirmed = window.confirm(
        "Are you sure you want to delete this skill?",
    );

    if (!confirmed) return;

    errorMessage.value = "";
    successMessage.value = "";

    try {
        await skillsRepository.delete(id);

        skills.value = skills.value.filter(
            (skill) => skill.id !== id,
        );

        if (editingSkillId.value === id) {
            resetForm();
        }

        successMessage.value = "Skill deleted successfully.";
    } catch (error) {
        console.error("Failed to delete skill:", error);
        errorMessage.value = "Unable to delete skill.";
    }
};

onMounted(loadSkills);
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div class="flex items-center gap-3 border-b border-slate-100 p-5 sm:p-6">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
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

            <!-- Skill Form -->
            <form class="overflow-hidden rounded-2xl border border-emerald-100 bg-slate-50/70"
                @submit.prevent="handleSubmit">
                <!-- Form Header -->
                <div class="flex items-center gap-3 border-b border-emerald-100 bg-emerald-50/70 px-5 py-4">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm">
                        <Code2 :size="21" />
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800">
                            {{ editingSkillId !== null ? "Update Your Skill" : "Add a New Skill" }}
                        </h3>

                        <p class="text-xs text-slate-500">
                            Enter a skill and choose your proficiency level.
                        </p>
                    </div>
                </div>

                <div class="space-y-5 p-5">

                    <!-- Skill Name -->
                    <div>
                        <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <Code2 :size="16" class="text-emerald-600" />
                            Skill Name
                            <span class="text-red-500">*</span>
                        </label>

                        <input v-model="form.name" type="text" placeholder="e.g. Laravel, Vue.js, PHP, MySQL"
                            maxlength="100" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />

                        <p class="mt-2 text-xs text-slate-400">
                            Enter a technical or professional skill.
                        </p>
                    </div>

                    <!-- Skill Level -->
                    <div>
                        <label class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <Award :size="16" class="text-emerald-600" />
                            Proficiency Level
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <button v-for="level in skillLevels" :key="level" type="button"
                                :aria-pressed="form.skill_level === level" @click="form.skill_level = level" :class="[
                                    'flex flex-col items-center gap-2 rounded-xl border p-3 text-center transition duration-200',
                                    form.skill_level === level
                                        ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-100'
                                        : 'border-slate-200 bg-white hover:border-emerald-200 hover:bg-emerald-50/40'
                                ]">
                                <component :is="getLevelIcon(level)" :size="21"
                                    :class="form.skill_level === level ? 'text-emerald-600' : 'text-slate-400'" />

                                <span class="text-xs font-semibold"
                                    :class="form.skill_level === level ? 'text-emerald-700' : 'text-slate-600'">
                                    {{ level }}
                                </span>
                            </button>
                        </div>

                        <!-- Selected Level Preview -->
                        <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-500">
                                    Selected proficiency
                                </span>

                                <span class="rounded-lg border px-2.5 py-1 text-xs font-semibold"
                                    :class="getLevelStyle(form.skill_level)">
                                    {{ form.skill_level }}
                                </span>
                            </div>

                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                    :class="getLevelWidth(form.skill_level)"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
                        <button v-if="editingSkillId !== null" type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                            @click="resetForm">
                            <X :size="17" />
                            Cancel
                        </button>

                        <button type="submit" :disabled="isSaving"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <LoaderCircle v-if="isSaving" :size="17" class="animate-spin" />

                            <Save v-else-if="editingSkillId !== null" :size="17" />
                            <Plus v-else :size="17" />

                            {{
                                isSaving
                                    ? "Saving..."
                                    : editingSkillId !== null
                                        ? "Update Skill"
                                        : "Add Skill"
                            }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Skills List -->
            <div>
                <div class="mb-4 flex items-center justify-between">
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

                <!-- Empty State -->
                <div v-else-if="skills.length === 0"
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
                </div>

                <!-- Skills Grid -->
                <div v-else class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div v-for="skill in skills" :key="skill.id"
                        class="group rounded-2xl border border-slate-200 bg-white p-4 transition duration-200 hover:border-emerald-200 hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
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
                                    class="rounded-lg p-2 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-700"
                                    @click="editSkill(skill)">
                                    <Pencil :size="17" />
                                </button>

                                <button type="button" title="Delete skill" aria-label="Delete skill"
                                    class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                                    @click="deleteSkill(skill.id)">
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
            </div>
        </div>
    </section>
</template>
