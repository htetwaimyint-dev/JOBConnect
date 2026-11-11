<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

import PersonalInformation from "@/components/common/PersonalInformation.vue";
import accountSettingsRepository from "../repositories/accountSettingsRepository";

const router = useRouter();

const seeker = ref({
  name: "",
  email: "",
  profileImage: null as string | null,
});

const isLoading = ref(true);
const errorMessage = ref("");
const successMessage = ref("");

// Get Job Seeker Profile
const getProfile = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await accountSettingsRepository.getProfile();

    const profile = response.data.data;

    seeker.value.name = profile.name ?? "";
    seeker.value.email = profile.email ?? "";
    seeker.value.profileImage = profile.profile_photo ?? null;
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

  const formData = new FormData();

  formData.append("_method", "PUT");
  formData.append("name", data.name);
  formData.append("email", data.email);

  if (data.profileImage) {
    formData.append("profile_photo", data.profileImage);
  }

  if (data.removeProfileImage) {
    formData.append("remove_profile_photo", "1");
  }

  try {
    // Update account information
    const response = await accountSettingsRepository.updateAccount(formData);

    const profile = response.data.data;

    seeker.value.name = profile.name ?? data.name;
    seeker.value.email = profile.email ?? data.email;
    seeker.value.profileImage = profile.profile_photo ?? null;

    // Show success message
    successMessage.value = "Account information updated successfully.";

    // Wait for 2 seconds before navigating
    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Navigate only after successful update
    await router.push("/jobseeker/dashboard");
  } catch (error) {
    console.error("Failed to update account:", error);

    // Stay on Settings Page when an error occurs
    if (axios.isAxiosError(error)) {
      const validationErrors = error.response?.data?.errors;

      if (validationErrors) {
        errorMessage.value = Object.values(validationErrors).flat().join("\n");
      } else {
        errorMessage.value =
          error.response?.data?.message ??
          "Failed to update account information.";
      }
    } else {
      errorMessage.value = "Something went wrong.";
    }
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
  }
};

// Load Profile When Page Opens
onMounted(() => {
  getProfile();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Page Heading -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Account Settings</h1>

      <p class="mt-1 text-sm text-slate-500">
        Manage your profile and account security.
      </p>
    </div>

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
