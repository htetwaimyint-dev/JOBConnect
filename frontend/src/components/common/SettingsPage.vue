<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import {
  CheckCircle2,
  AlertCircle,
  LoaderCircle,
  ArrowLeft,
} from "lucide-vue-next";

import PersonalInformation from "@/components/common/PersonalInformation.vue";
import { useAuthStore } from "@/stores/auth.store";

import { accountRepository } from "@/features/shared/repositories/accountRepository";
import { profileRepository } from "@/features/job-seeker/repositories/profileRepository";

import type { UpdateAccountPasswordData } from "@/features/shared/types/account.types";

type AlertType = "success" | "error" | "warning";

interface SaveData {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
  password: UpdateAccountPasswordData | null;
}

const authStore = useAuthStore();
const route = useRoute();
const router = useRouter();

const user = ref({
  name: "",
  email: "",
  profileImage: null as string | null,
});

const role = computed(() => {
  return String(authStore.user?.role ?? route.meta.role ?? "");
});

const isJobSeeker = computed(() => role.value === "job_seeker");

const isLoading = ref(false);
const isSaving = ref(false);

const alertMessage = ref("");
const alertType = ref<AlertType>("success");

let alertTimer: ReturnType<typeof setTimeout> | null = null;

const showAlert = (message: string, type: AlertType = "success") => {
  alertMessage.value = message;
  alertType.value = type;

  if (alertTimer) {
    clearTimeout(alertTimer);
  }

  alertTimer = setTimeout(() => {
    alertMessage.value = "";
  }, 3000);
};

const loadSettings = async () => {
  isLoading.value = true;

  try {
    await authStore.initializeAuth();

    if (!authStore.user) {
      throw new Error("Unable to load account information.");
    }

    user.value.name = authStore.user.name ?? "";
    user.value.email = authStore.user.email ?? "";

    if (isJobSeeker.value) {
      const profile = await profileRepository.getProfile();
      user.value.profileImage = profile.profile_photo ?? null;
    }
  } catch (error) {
    console.error("Failed to load settings:", error);
    showAlert("Failed to load settings. Please try again.", "error");
  } finally {
    isLoading.value = false;
  }
};

const handleSave = async (data: SaveData) => {
  if (isSaving.value) return;

  isSaving.value = true;

  try {
    const accountResponse = await accountRepository.updateProfile({
      name: data.name,
      email: data.email,
    });

    const updatedUser = accountResponse.data.user;

    if (updatedUser) {
      authStore.user = updatedUser;
    }

    user.value.name = data.name;
    user.value.email = data.email;

    if (isJobSeeker.value) {
      if (data.profileImage) {
        const profile = await profileRepository.updateProfile({
          profile_photo: data.profileImage,
        });

        user.value.profileImage = profile.profile_photo ?? null;
      } else if (data.removeProfileImage) {
        showAlert(
          "Profile photo removal is not supported yet. Other changes were saved.",
          "warning",
        );
      }
    }

    if (data.password) {
      await accountRepository.updatePassword(data.password);
    }

    if (!(isJobSeeker.value && data.removeProfileImage && !data.profileImage)) {
      showAlert("Settings updated successfully.", "success");
    }
  } catch (error: any) {
    console.error("Failed to update settings:", error);

    const message =
      error?.response?.data?.message ??
      "Failed to update settings. Please try again.";

    showAlert(message, "error");
  } finally {
    isSaving.value = false;
  }
};

const goBack = () => {
  const dashboardRoutes: Record<string, string> = {
    admin: "/admin/dashboard",
    employer: "/employer/dashboard",
    job_seeker: "/jobseeker/dashboard",
  };

  const dashboard = dashboardRoutes[role.value];

  if (window.history.state?.back) {
    router.back();
  } else {
    router.push(dashboard || "/");
  }
};

onMounted(loadSettings);

onBeforeUnmount(() => {
  if (alertTimer) {
    clearTimeout(alertTimer);
  }
});
</script>

<template>
  <div
    class="mx-auto w-full min-w-0 max-w-3xl space-y-4 px-4 py-5 sm:space-y-5 sm:px-6 sm:py-6"
  >
    <!-- Back Button -->
    <button
      type="button"
      @click="goBack"
      class="group inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700 hover:shadow-md sm:gap-2.5 sm:rounded-xl sm:px-4 sm:py-2.5"
    >
      <span
        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 transition group-hover:bg-blue-50 sm:h-8 sm:w-8 sm:rounded-lg"
      >
        <ArrowLeft
          class="h-4 w-4 transition-transform group-hover:-translate-x-1"
        />
      </span>

      <span>Back</span>
    </button>

    <!-- Alert -->
    <div
      v-if="alertMessage"
      class="flex min-w-0 items-start gap-2.5 rounded-lg border p-3 text-xs sm:items-center sm:gap-3 sm:rounded-xl sm:p-4 sm:text-sm"
      :class="{
        'border-green-200 bg-green-50 text-green-700': alertType === 'success',
        'border-red-200 bg-red-50 text-red-700': alertType === 'error',
        'border-amber-200 bg-amber-50 text-amber-700': alertType === 'warning',
      }"
    >
      <CheckCircle2
        v-if="alertType === 'success'"
        class="mt-0.5 h-4 w-4 shrink-0 sm:mt-0 sm:h-5 sm:w-5"
      />

      <AlertCircle
        v-else
        class="mt-0.5 h-4 w-4 shrink-0 sm:mt-0 sm:h-5 sm:w-5"
      />

      <span class="min-w-0 break-words">{{ alertMessage }}</span>
    </div>

    <!-- Loading -->
    <div
      v-if="isLoading"
      class="flex min-h-56 items-center justify-center rounded-xl border border-slate-200 bg-white sm:min-h-64"
    >
      <div class="flex items-center gap-2.5 text-slate-500 sm:gap-3">
        <LoaderCircle
          class="h-5 w-5 animate-spin text-blue-600 sm:h-6 sm:w-6"
        />
        <span class="text-xs sm:text-sm">Loading settings...</span>
      </div>
    </div>

    <!-- Personal Information -->
    <div v-else class="w-full min-w-0">
      <PersonalInformation
        :name="user.name"
        :email="user.email"
        :profile-image="user.profileImage"
        :show-profile-photo="isJobSeeker"
        @save="handleSave"
      />
    </div>

    <!-- Saving Indicator -->
    <div
      v-if="isSaving"
      class="flex items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 py-2.5 text-xs font-medium text-blue-600 sm:text-sm"
    >
      <LoaderCircle class="h-4 w-4 animate-spin" />
      <span>Saving changes...</span>
    </div>
  </div>
</template>
