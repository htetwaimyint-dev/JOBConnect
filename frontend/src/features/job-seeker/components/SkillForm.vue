<script setup lang="ts">
import { ref, watch } from "vue";
import {
    Code2,
    Award,
    Lightbulb,
    Zap,
    Star,
    ShieldCheck,
} from "lucide-vue-next";

import AppModal from "@/components/common/AppModal.vue";

import type {
    Skill,
    SkillLevel,
    CreateSkillData,
} from "../types/skill.types";

const props = defineProps<{
    show: boolean;
    skill?: Skill | null;
    isSaving?: boolean;
}>();

const emit = defineEmits<{
    close: [];
    save: [data: CreateSkillData];
}>();

const skillLevels: SkillLevel[] = [
    "Beginner",
    "Intermediate",
    "Advanced",
    "Expert",
];

const createEmptyForm = (): CreateSkillData => ({
    name: "",
    skill_level: "Beginner",
});

const form = ref<CreateSkillData>(createEmptyForm());

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

const resetForm = () => {
    form.value = createEmptyForm();
};

watch(
    () => [props.show, props.skill] as const,
    ([isOpen, skill]) => {
        if (!isOpen) return;

        if (skill) {
            form.value = {
                name: skill.name,
                skill_level: skill.skill_level,
            };
        } else {
            resetForm();
        }
    },
    { immediate: true },
);

const closeForm = () => {
    resetForm();
    emit("close");
};

const handleSubmit = () => {
    const name = form.value.name.trim();

    if (name.length < 2) return;

    emit("save", {
        name,
        skill_level: form.value.skill_level,
    });
};
</script>

<template>
    <AppModal :show="show" :title="skill ? 'Edit Skill' : 'Add Skill'" :description="skill
            ? 'Update your skill and proficiency level.'
            : 'Add a technical or professional skill to your profile.'
        " max-width="max-w-xl" @close="closeForm">
        <form @submit.prevent="handleSubmit" class="space-y-5">

            <!-- Skill Name -->
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <Code2 :size="16" class="text-emerald-600" />
                    Skill Name
                    <span class="text-red-500">*</span>
                </label>

                <input v-model.trim="form.name" type="text" placeholder="e.g. Laravel, Vue.js, PHP, MySQL"
                    maxlength="100" minlength="2" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" />

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
                                : 'border-slate-200 bg-white hover:border-emerald-200 hover:bg-emerald-50/40',
                        ]">
                        <component :is="getLevelIcon(level)" :size="21" :class="form.skill_level === level
                                ? 'text-emerald-600'
                                : 'text-slate-400'
                            " />

                        <span class="text-xs font-semibold" :class="form.skill_level === level
                                ? 'text-emerald-700'
                                : 'text-slate-600'
                            ">
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

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" @click="closeForm" :disabled="isSaving"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50">
                    Cancel
                </button>

                <button type="submit" :disabled="isSaving || form.name.trim().length < 2"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                    {{
                        isSaving
                            ? "Saving..."
                            : skill
                                ? "Update Skill"
                                : "Save Skill"
                    }}
                </button>
            </div>
        </form>
    </AppModal>
</template>
