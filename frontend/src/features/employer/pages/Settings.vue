<script setup lang="ts">
import { onMounted, ref } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";
import accountSettingsRepository from "../repositories/accountSettingsRepository";

const employer = ref({
  name: "",
  email: "",
});

const isLoading = ref(true);
const isSaving = ref(false);
const isChangingPassword = ref(false);

const successMessage = ref("");
const errorMessage = ref("");

const getAccount = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await accountSettingsRepository.getAccount();

    employer.value = {
      name: response.data.data.name,
      email: response.data.data.email,
    };
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message || "Failed to load account information.";
  } finally {
    isLoading.value = false;
  }
};

const handleSave = async (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  isSaving.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const response = await accountSettingsRepository.updateAccount({
      name: data.name,
      email: data.email,
    });

    employer.value = {
      name: response.data.data.name,
      email: response.data.data.email,
    };

    successMessage.value =
      response.data.message || "Account information updated successfully.";
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message || "Failed to update account information.";
  } finally {
    isSaving.value = false;
  }
};

const handleChangePassword = async (data: {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}) => {
  isChangingPassword.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const response = await accountSettingsRepository.changePassword(data);

    successMessage.value =
      response.data.message || "Password changed successfully.";
  } catch (error: any) {
    errorMessage.value =
      error.response?.data?.message || "Failed to change password.";
  } finally {
    isChangingPassword.value = false;
  }
};

onMounted(getAccount);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Settings</h1>
      <p class="mt-2 text-slate-500">Manage your employer account settings.</p>
    </div>

    <div
      v-if="isLoading"
      class="rounded-2xl bg-white p-8 text-center text-slate-500"
    >
      Loading account information...
    </div>

    <div
      v-else-if="errorMessage && !employer.email"
      class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-600"
    >
      {{ errorMessage }}
    </div>

    <template v-else>
      <div
        v-if="successMessage"
        class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700"
      >
        {{ successMessage }}
      </div>

      <div
        v-if="errorMessage"
        class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-600"
      >
        {{ errorMessage }}
      </div>

      <PersonalInformation
        :name="employer.name"
        :email="employer.email"
        :show-profile-photo="false"
        @save="handleSave"
        @change-password="handleChangePassword"
      />
    </template>
  </div>
</template>
