<script setup lang="ts">
import { computed, watch } from "vue";
import { RouterView, useRouter } from "vue-router";
import { useQuery } from "@tanstack/vue-query";

import { useUiStore } from "@/stores/ui.store";
import { useAuthStore } from "@/stores/auth.store";
import { useCompanyProfileStore } from "@/stores/company-profile.store";

import { companyRepository } from "@/features/employer/repositories/companyRepository";
import { useCompanyProfile } from "@/features/employer/composables/useUpdateCompanyProfile";

import CompanyProfileRequiredModal from "@/features/employer/components/CompanyProfileRequiredModal.vue";
import CompanyBasicInfoForm from "@/features/employer/components/CompanyBasicInfoForm.vue";
import CompanyOverviewForm from "@/features/employer/components/CompanyOverviewForm.vue";
import CompanyBrandingForm from "@/features/employer/components/CompanyBrandingForm.vue";
import CompanyGalleryForm from "@/features/employer/components/CompanyGalleryForm.vue";

import AppHeader from "@/components/layout/AppHeader.vue";
import AppSidebar from "@/components/layout/AppSidebar.vue";
import AppPageContainer from "@/components/layout/AppPageContainer.vue";

const uiStore = useUiStore();
const router = useRouter();
const authStore = useAuthStore();
const companyStore = useCompanyProfileStore();

const userId = computed(() => authStore.user?.id ?? "guest");

// Company Profile Query
const {
  data: company,
  isPending,
  isError,
} = useQuery({
  queryKey: ["company", "profile", userId],
  queryFn: () => companyRepository.getCompanyProfileForForm(),
  retry: 1,
});

// Reuse existing create/update composable
const { mutation, submitForm } = useCompanyProfile();

watch(
  () => mutation.isSuccess.value,
  (success) => {
    if (success) {
      router.replace({ name: "EmployerDashboard" });
    }
  },
);

// Lock employer dashboard until company is created
const isCompanyMissing = computed(() => {
  return !isPending.value && !isError.value && company.value === null;
});

const isLocked = computed(() => isCompanyMissing.value);
</script>

<template>
  <div class="relative min-h-screen w-full bg-slate-50">
    <!-- Background Layout -->
    <div
      :inert="isLocked"
      :class="{
        'pointer-events-none select-none blur-sm': isLocked,
      }"
      class="min-h-screen transition-all duration-300"
    >
      <!-- Sidebar Overlay -->
      <Transition name="fade">
        <div
          v-if="uiStore.isSidebarOpen"
          class="fixed inset-0 z-40 bg-slate-900/50"
          @click="uiStore.closeSidebar"
        ></div>
      </Transition>

      <!-- Sidebar -->
      <AppSidebar />

      <!-- Main Layout -->
      <div class="min-h-screen w-full min-w-0">
        <!-- Header -->
        <AppHeader />

        <!-- Page Content -->
        <AppPageContainer>
          <RouterView />
        </AppPageContainer>
      </div>
    </div>

    <!-- Mandatory Company Create Modal -->
    <CompanyProfileRequiredModal :show="isLocked">
      <form v-if="isLocked" class="space-y-6" @submit.prevent="submitForm">
        <!-- Error Message -->
        <div
          v-if="companyStore.errorMessage"
          class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"
        >
          {{ companyStore.errorMessage }}
        </div>

        <!-- Basic Information -->
        <CompanyBasicInfoForm
          v-model:form="companyStore.form"
          :errors="companyStore.errors"
          :logo-preview="companyStore.logoPreview"
          @select-logo="companyStore.selectImage($event, 'logo')"
        />

        <!-- Company Description -->
        <CompanyOverviewForm
          v-model:form="companyStore.form"
          :errors="companyStore.errors"
        />

        <!-- Branding -->
        <CompanyBrandingForm
          :cover-preview="companyStore.coverPreview"
          :errors="companyStore.errors"
          @select-cover="companyStore.selectImage($event, 'cover')"
        />

        <!-- Gallery -->
        <CompanyGalleryForm
          :existing-images="companyStore.existingGallery"
          :new-images="companyStore.newGallery"
          :errors="companyStore.errors"
          @add-images="companyStore.addGallery"
          @remove-existing="companyStore.removeGallery($event)"
          @remove-new="companyStore.removeGallery($event, true)"
        />

        <!-- Create Button -->
        <div class="sticky bottom-0 bg-white pt-4 pb-2">
          <button
            type="submit"
            :disabled="mutation.isPending.value"
            class="w-full rounded-xl bg-[#0052FF] px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{
              mutation.isPending.value
                ? "Creating Company..."
                : "Create Company"
            }}
          </button>
        </div>
      </form>
    </CompanyProfileRequiredModal>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
