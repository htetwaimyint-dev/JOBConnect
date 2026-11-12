<script setup lang="ts">
import { onMounted, ref } from "vue";
import axios from "axios";

import PersonalInformation from "@/components/common/PersonalInformation.vue";
import accountSettingsRepository from "../repositories/accountSettingsRepository";

const seeker = ref({
  name: "",
  email: "",
  profileImage: null as string | null,
});

const isLoading = ref(true);
const isSaving = ref(false);
const isChangingPassword = ref(false);

const errorMessage = ref("");
const successMessage = ref("");

// Get Job Seeker Profile
const getProfile = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await accountSettingsRepository.getProfile();
    const profile = response.data.data;

    seeker.value = {
      name: profile.name ?? "",
      email: profile.email ?? "",
      profileImage: profile.profile_photo ?? null,
    };
  } catch (error) {
    console.error("Failed to load profile:", error);

    if (axios.isAxiosError(error)) {
      errorMessage.value =
        error.response?.data?.message ?? "Failed to load account information.";
    } else {
      errorMessage.value = "Something went wrong.";
    }
  } finally {
    isLoading.value = false;
  }
};

// Update Account Information
const handleSave = async (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  errorMessage.value = "";
  successMessage.value = "";
  isSaving.value = true;

  const formData = new FormData();

  formData.append("name", data.name);
  formData.append("email", data.email);

  if (data.profileImage) {
    formData.append("profile_photo", data.profileImage);
  }

  if (data.removeProfileImage) {
    formData.append("remove_profile_photo", "1");
  }

  try {
    const response = await accountSettingsRepository.updateAccount(formData);

    const profile = response.data.data;

    seeker.value = {
      name: profile.name ?? data.name,
      email: profile.email ?? data.email,
      profileImage: profile.profile_photo ?? null,
    };

    successMessage.value =
      response.data.message ?? "Account information updated successfully.";
  } catch (error) {
    console.error("Failed to update account:", error);

    if (axios.isAxiosError(error)) {
      const validationErrors = error.response?.data?.errors;

      errorMessage.value = validationErrors
        ? Object.values(validationErrors).flat().join("\n")
        : (error.response?.data?.message ??
          "Failed to update account information.");
    } else {
      errorMessage.value = "Something went wrong.";
    }
  } finally {
    isSaving.value = false;
  }
};

// Change Password
const handleChangePassword = async (data: {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}) => {
  errorMessage.value = "";
  successMessage.value = "";
  isChangingPassword.value = true;

  try {
    const response = await accountSettingsRepository.changePassword(data);

    successMessage.value =
      response.data.message ?? "Password changed successfully.";
  } catch (error) {
    console.error("Failed to change password:", error);

    if (axios.isAxiosError(error)) {
      const validationErrors = error.response?.data?.errors;

      errorMessage.value = validationErrors
        ? Object.values(validationErrors).flat().join("\n")
        : (error.response?.data?.message ?? "Failed to change password.");
    } else {
      errorMessage.value = "Something went wrong.";
    }
  } finally {
    isChangingPassword.value = false;
  }
};

// Load Profile When Page Opens
onMounted(() => {
  getProfile();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Loading -->
    <div
      v-if="isLoading"
      class="rounded-xl border border-slate-200 bg-white p-6 text-center text-sm text-slate-500"
    >
      Loading account information...
    </div>

    <!-- Profile Loading Error -->
    <div
      v-else-if="errorMessage && !seeker.name"
      class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600"
    >
      <p class="whitespace-pre-line">
        {{ errorMessage }}
      </p>

      <button
        type="button"
        @click="getProfile"
        class="ml-2 mt-2 font-semibold underline"
      >
        Retry
      </button>
    </div>

    <!-- Account Settings -->
    <template v-else>
      <!-- Success Message -->
      <div
        v-if="successMessage"
        class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-700"
      >
        {{ successMessage }}
      </div>

      <!-- Error Message -->
      <div
        v-if="errorMessage"
        class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600"
      >
        <p class="whitespace-pre-line">
          {{ errorMessage }}
        </p>
      </div>

      <!-- Personal Information -->
      <PersonalInformation
        :name="seeker.name"
        :email="seeker.email"
        :profile-image="seeker.profileImage"
        @save="handleSave"
        @change-password="handleChangePassword"
      />
    </template>
  </div>
</template>
