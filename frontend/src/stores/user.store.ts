
import { defineStore } from "pinia";
import { ref } from "vue";

export const useUserStore = defineStore("user", () => {
    // Current user's profile photo
    const profilePhoto = ref<string | null>(null);

    // Update profile photo
    const setProfilePhoto = (photo: string | null) => {
        profilePhoto.value = photo;
    };

    // Clear profile photo
    const clearProfilePhoto = () => {
        profilePhoto.value = null;
    };

    return {
        profilePhoto,
        setProfilePhoto,
        clearProfilePhoto,
    };
});
