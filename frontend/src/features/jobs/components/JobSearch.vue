<script setup lang="ts">
import { ref, watch } from "vue";

const props = defineProps<{
    search: string;
    city: string;
    country: string;
}>();

const emit = defineEmits<{
    (e: "search", value: {
        search: string;
        city: string;
        country: string;
    }): void;
}>();

const keyword = ref(props.search);
const cityValue = ref(props.city);
const countryValue = ref(props.country);

watch(() => props.search, (value) => {
    keyword.value = value;
});

watch(() => props.city, (value) => {
    cityValue.value = value;
});

watch(() => props.country, (value) => {
    countryValue.value = value;
});

const submitSearch = () => {
    emit("search", {
        search: keyword.value.trim(),
        city: cityValue.value.trim(),
        country: countryValue.value.trim(),
    });
};
</script>

<template>
    <form
        class="grid grid-cols-1 gap-3 rounded-2xl bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_auto]"
        @submit.prevent="submitSearch">
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Job title or keyword
            </label>
            <input v-model="keyword" type="text" placeholder="e.g. Laravel Developer"
                class="h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                City
            </label>
            <input v-model="cityValue" type="text" placeholder="e.g. Mandalay"
                class="h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Country
            </label>
            <input v-model="countryValue" type="text" placeholder="e.g. Myanmar"
                class="h-12 w-full rounded-xl border border-slate-200 px-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
        </div>

        <div class="flex items-end">
            <button type="submit"
                class="h-12 w-full rounded-xl bg-blue-600 px-7 text-sm font-semibold text-white transition hover:bg-blue-700">
                Search Jobs
            </button>
        </div>
    </form>
</template>
