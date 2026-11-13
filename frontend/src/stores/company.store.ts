import { defineStore } from "pinia";
import { ref } from "vue";

export type CompanyTab = "about" | "jobs" | "gallery";

export const useCompanyStore = defineStore("company", () => {
    const activeTab = ref<CompanyTab>("about");
    const isFollowing = ref(false);

    function setActiveTab(tab: CompanyTab) {
        activeTab.value = tab;
    }

    function toggleFollow() {
        isFollowing.value = !isFollowing.value;
    }

    return {
        activeTab,
        isFollowing,
        setActiveTab,
        toggleFollow,
    };
});
