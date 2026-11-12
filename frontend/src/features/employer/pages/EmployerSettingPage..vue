<script setup lang="ts">
import { ref } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";

const employer = ref({
  name: "Employer Name",
  email: "employer@example.com",
});

// Handle account information update
const handleSave = (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  employer.value.name = data.name;
  employer.value.email = data.email;

  console.log("Updated employer information:", {
    name: data.name,
    email: data.email,
  });
};

// Handle password change
const handleChangePassword = (data: {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}) => {
  console.log("Employer password change:", data);
};
</script>

<template>
  <div class="space-y-6">
    <!-- Personal Information -->
    <PersonalInformation
      :name="employer.name"
      :email="employer.email"
      :show-profile-photo="false"
      @save="handleSave"
      @change-password="handleChangePassword"
    />
  </div>
</template>
