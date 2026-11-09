<script setup lang="ts">
const props = defineProps<{
    activeTab: string;
}>();

const emit = defineEmits<{
    change: [tab: string];
}>();

const tabs = ["About", "Jobs", "Life at Company", "Reviews"];
</script>

<template>
    <nav class="flex gap-6 overflow-x-auto border-b border-gray-200 bg-white">
        <button
            v-for="tab in tabs"
            :key="tab"
            @click="emit('change', tab)"
            class="shrink-0 border-b-2 px-2 py-4 text-sm font-semibold transition"
            :class="
                props.activeTab === tab
                    ? 'border-blue-700 text-blue-700'
                    : 'border-transparent text-gray-500 hover:text-gray-900'
            "
        >
            {{ tab }}
        </button>
    </nav>
</template>
