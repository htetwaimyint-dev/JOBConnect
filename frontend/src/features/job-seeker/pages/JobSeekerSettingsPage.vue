<script setup lang="ts">
import { onMounted, ref, onUnmounted } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";
import BaseAlert from "@/components/common/BaseAlert.vue";

import { useAuthStore } from "@/stores/auth.store";
import { accountRepository } from "@/features/shared/repositories/accountRepository";
import { profileRepository } from "@/features/job-seeker/repositories/profileRepository";

import type { UpdateAccountPasswordData } from "@/features/shared/types/account.types";

const authStore = useAuthStore();

const seeker = ref({
  name: "",
  email: "",
  profileImage: null as string | null,
});

const isLoading = ref(false);

interface SaveData {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
  password: UpdateAccountPasswordData | null;
}

type AlertVariant = "success" | "error" | "warning" | "info";

const alertState = ref({
  show: false,
  variant: "success" as AlertVariant,
  title: "",
  message: "",
});

let alertTimer: ReturnType<typeof setTimeout> | null = null;

const showAlert = (variant: AlertVariant, title: string, message: string) => {
  if (alertTimer) {
    clearTimeout(alertTimer);
  }

  alertState.value = {
    show: true,
    variant,
    title,
    message,
  };

  alertTimer = setTimeout(() => {
    alertState.value.show = false;
  }, 3000);
};

const dismissAlert = () => {
  alertState.value.show = false;

  if (alertTimer) {
    clearTimeout(alertTimer);
    alertTimer = null;
  }
};

const loadSettings = async () => {
  try {
    await authStore.initializeAuth();

    if (authStore.user) {
      seeker.value.name = authStore.user.name;
      seeker.value.email = authStore.user.email;
    }

    const profile = await profileRepository.getProfile();

    seeker.value.profileImage = profile.profile_photo;
  } catch (error) {
    console.error("Failed to load settings:", error);

    showAlert(
      "error",
      "Loading Failed",
      "Failed to load your profile information.",
    );
  }
};

const handleSave = async (data: SaveData) => {
  if (isLoading.value) return;

  isLoading.value = true;

  try {
    // 1. Update account information
    const accountResponse = await accountRepository.updateProfile({
      name: data.name,
      email: data.email,
    });

    if (accountResponse.data.user) {
      authStore.user = accountResponse.data.user;
    }

    seeker.value.name = data.name;
    seeker.value.email = data.email;

    // 2. Upload profile photo
    if (data.profileImage) {
      const updatedProfile = await profileRepository.updateProfile({
        profile_photo: data.profileImage,
      });

      seeker.value.profileImage = updatedProfile.profile_photo;
    }

    // 3. Update password
    if (data.password) {
      await accountRepository.updatePassword(data.password);
    }

    // 4. Show warning if photo removal was requested
    if (data.removeProfileImage && !data.profileImage) {
      showAlert(
        "warning",
        "Partially Saved",
        "Your account information was saved, but profile photo removal is not supported yet.",
      );
      return;
    }

    showAlert(
      "success",
      "Success!",
      "Your settings have been updated successfully.",
    );
  } catch (error: unknown) {
    console.error("Failed to update settings:", error);

    let message = "Something went wrong. Please try again.";

    if (typeof error === "object" && error !== null && "response" in error) {
      const response = (
        error as {
          response?: {
            data?: {
              message?: string;
            };
          };
        }
      ).response;

      message = response?.data?.message ?? message;
    }

    showAlert("error", "Update Failed", message);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadSettings();
});

onUnmounted(() => {
  if (alertTimer) {
    clearTimeout(alertTimer);
  }
});
</script>

<template>
  <div class="space-y-6">
    <!-- Success / Error / Warning Alert -->
    <BaseAlert
      v-if="alertState.show"
      :variant="alertState.variant"
      :title="alertState.title"
      :message="alertState.message"
      :dismissible="true"
      @dismiss="dismissAlert"
    />

    <PersonalInformation
      :name="seeker.name"
      :email="seeker.email"
      :profile-image="seeker.profileImage"
      @save="handleSave"
    />
  </div>
</template>
