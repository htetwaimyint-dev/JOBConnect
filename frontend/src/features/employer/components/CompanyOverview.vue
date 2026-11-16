<script setup lang="ts">
import type { Company } from "../types/company.types";
import { useCompanyProfileStore } from "@/stores/company-profile.store";
import CompanyOverviewPreview from "./CompanyOverviewPreview.vue";

defineProps<{
    company: Company;
}>();

const store = useCompanyProfileStore();

const availableIcons = [
    {
        id: "lightbulb",
        label: "Vision",
        svg: "M12 2a7 7 0 00-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 001 1h6a1 1 0 001-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 00-7-7z",
    },
    {
        id: "briefcase",
        label: "Work",
        svg: "M20 7h-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z",
    },
    {
        id: "star",
        label: "Perks",
        svg: "M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z",
    },
    {
        id: "building",
        label: "Culture",
        svg: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12",
    },
    {
        id: "target",
        label: "Goals",
        svg: "M12 2a10 10 0 100 20 10 10 0 000-20zm0 14a4 4 0 110-8 4 4 0 010 8z",
    },
    {
        id: "users",
        label: "Team",
        svg: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
    },
];
</script>

<template>
    <section class="pt-3">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            About {{ company.name }}
        </h2>
        <div class="text-sm text-gray-600 leading-relaxed space-y-3">
            <CompanyOverviewPreview
                :sections="store.form.sections"
                :available-icons="availableIcons"
            />
        </div>
    </section>
</template>
