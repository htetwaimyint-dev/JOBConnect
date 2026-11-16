<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";

import BaseAlert from "@/components/common/BaseAlert.vue";
import PersonalInformation from "@/components/common/PersonalInformation.vue";

import { useAuthStore } from "@/stores/auth.store";
import { accountRepository } from "@/features/shared/repositories/accountRepository";

import type { UpdateAccountPasswordData } from "@/features/shared/types/account.types";

const authStore = useAuthStore();

const employer = ref({
  name: "",
  email: "",
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
      employer.value.name = authStore.user.name;
      employer.value.email = authStore.user.email;
    }
  } catch (error) {
    console.error("Failed to load employer settings:", error);

    showAlert(
      "error",
      "Loading Failed",
      "Failed to load your account information.",
    );
  }
};

const handleSave = async (data: SaveData) => {
  if (isLoading.value) return;

  isLoading.value = true;

  try {
    // Update name and email
    const response = await accountRepository.updateProfile({
      name: data.name,
      email: data.email,
    });

    if (response.data.user) {
      authStore.user = response.data.user;
    }

    employer.value.name = data.name;
    employer.value.email = data.email;

    // Update password when provided
    if (data.password) {
      await accountRepository.updatePassword(data.password);
    }

    showAlert(
      "success",
      "Success!",
      "Your account settings have been updated successfully.",
    );
  } catch (error: unknown) {
    console.error("Failed to update employer settings:", error);

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
    <BaseAlert
      v-if="alertState.show"
      :variant="alertState.variant"
      :title="alertState.title"
      :message="alertState.message"
      :dismissible="true"
      @dismiss="dismissAlert"
    />

    <PersonalInformation
      :name="employer.name"
      :email="employer.email"
      @save="handleSave"
    />
  </div>
</template>
