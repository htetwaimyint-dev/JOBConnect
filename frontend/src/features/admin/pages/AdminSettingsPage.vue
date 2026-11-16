<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";

import BaseAlert from "@/components/common/BaseAlert.vue";
import PersonalInformation from "@/components/common/PersonalInformation.vue";

import { useAuthStore } from "@/stores/auth.store";
import { accountRepository } from "@/features/shared/repositories/accountRepository";

import type { UpdateAccountPasswordData } from "@/features/shared/types/account.types";

const authStore = useAuthStore();

const admin = ref({
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
  if (alertTimer) clearTimeout(alertTimer);

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
      admin.value.name = authStore.user.name;
      admin.value.email = authStore.user.email;
    }
  } catch (error) {
    showAlert("error", "Error", "Failed to load admin information.");
  }
};

const handleSave = async (data: SaveData) => {
  if (isLoading.value) return;

  isLoading.value = true;

  try {
    const profileResponse = await accountRepository.updateProfile({
      name: data.name,
      email: data.email,
    });

    if (profileResponse.data.user) {
      authStore.user = profileResponse.data.user;
    }

    admin.value.name = data.name;
    admin.value.email = data.email;

    if (data.password) {
      await accountRepository.updatePassword(data.password);
    }

    showAlert(
      "success",
      "Success",
      "Your account settings have been updated successfully.",
    );
  } catch (error: any) {
    const message =
      error.response?.data?.message ||
      "Something went wrong. Please try again.";

    showAlert("error", "Update Failed", message);
  } finally {
    isLoading.value = false;
  }
};

onMounted(loadSettings);

onUnmounted(() => {
  if (alertTimer) clearTimeout(alertTimer);
});
</script>

<template>
  <BaseAlert
    v-if="alertState.show"
    :variant="alertState.variant"
    :title="alertState.title"
    :message="alertState.message"
    :dismissible="true"
    @dismiss="dismissAlert"
  />

  <PersonalInformation
    :name="admin.name"
    :email="admin.email"
    :show-profile-photo="false"
    @save="handleSave"
  />
</template>
