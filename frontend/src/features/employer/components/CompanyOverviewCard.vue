<script setup lang="ts">
import type { CompanyOverviewSection } from "../types/company-form.types";

const props = defineProps<{
    section: CompanyOverviewSection;
    index: number;
    isFirst: boolean;
    isLast: boolean;
    availableIcons: Array<{ id: string; label: string; svg: string }>;
}>();

const emit = defineEmits<{
    remove: [];
    moveUp: [];
    moveDown: [];
}>();

function getIconPath(iconId?: string) {
    if (props.availableIcons[0]) {
        return (
            props.availableIcons.find((i) => i.id === iconId)?.svg ||
            props.availableIcons[0].svg
        );
    }
}
</script>

<template>
    <div
        class="group relative rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all hover:border-blue-400 hover:shadow-md hover:shadow-blue-500/5"
    >
        <!-- Compact Top Bar: Index + Controls -->
        <div
            class="mb-3.5 flex items-center justify-between border-b border-slate-100 pb-3"
        >
            <div class="flex items-center gap-2.5">
                <span
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-linear-to-r from-blue-600 to-indigo-600 text-[10px] font-bold text-white shadow-xs"
                >
                    {{ index + 1 }}
                </span>
                <span class="text-xs font-bold text-slate-800"
                    >Section Block</span
                >
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    @click="emit('moveUp')"
                    :disabled="isFirst"
                    title="Move Up"
                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 disabled:opacity-30 disabled:pointer-events-none transition"
                >
                    ↑
                </button>
                <button
                    type="button"
                    @click="emit('moveDown')"
                    :disabled="isLast"
                    title="Move Down"
                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 disabled:opacity-30 disabled:pointer-events-none transition"
                >
                    ↓
                </button>
                <button
                    type="button"
                    @click="emit('remove')"
                    class="ml-1 px-3 py-1 text-[12px]! font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition"
                >
                    Delete
                </button>
            </div>
        </div>

        <!-- Main Card Body -->
        <div class="space-y-3.5">
            <!-- Icon Selector & Title in One Row -->
            <div class="flex items-center gap-3.5">
                <!-- Icon Selector Pills with Pop Colors -->
                <div
                    class="flex shrink-0 items-center gap-1.5 border-r border-slate-200 pr-3.5"
                >
                    <button
                        v-for="icon in availableIcons"
                        :key="icon.id"
                        type="button"
                        @click="section.icon = icon.id as any"
                        :title="icon.label"
                        :class="[
                            section.icon === icon.id
                                ? 'border-blue-600 bg-linear-to-tr from-blue-600 to-indigo-600 text-white shadow-sm shadow-blue-500/30 scale-105'
                                : 'border-slate-200 bg-slate-50/80 text-slate-400 hover:border-slate-300 hover:text-slate-700',
                            'flex h-8 w-8 items-center justify-center rounded-lg border transition-all',
                        ]"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                :d="icon.svg"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Title Field -->
                <div class="flex-1">
                    <input
                        v-model="section.title"
                        type="text"
                        placeholder="Title (e.g. Company Vision and Mission)"
                        class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/40 px-3.5 text-xs! font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 transition"
                    />
                </div>
            </div>

            <!-- Description Area -->
            <div>
                <textarea
                    v-model="section.content"
                    rows="3"
                    placeholder="Detailed description for this section..."
                    class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50/40 p-3 text-xs! text-slate-900 placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 transition"
                />
            </div>
        </div>
    </div>
</template>
