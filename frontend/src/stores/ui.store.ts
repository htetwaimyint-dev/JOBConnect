
import { defineStore } from "pinia";
import { ref } from "vue";

export const useUiStore = defineStore("ui", () => {
    // Sidebar state
    const isSidebarOpen = ref(false);

    // Open sidebar
    const openSidebar = () => {
        isSidebarOpen.value = true;
    };

    // Close sidebar
    const closeSidebar = () => {
        isSidebarOpen.value = false;
    };

    // Toggle sidebar
    const toggleSidebar = () => {
        isSidebarOpen.value = !isSidebarOpen.value;
    };

    return {
        isSidebarOpen,
        openSidebar,
        closeSidebar,
        toggleSidebar,
    };
});
