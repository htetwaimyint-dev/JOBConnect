<script setup lang="ts">
import { ref } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";

const admin = ref({
  name: "Admin",
  email: "admin@jobconnect.com",
});

// Handle account information update
const handleSave = (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  admin.value.name = data.name;
  admin.value.email = data.email;

  console.log("Updated admin information:", {
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
  console.log("Admin password change:", data);
};
</script>

<template>
  <div class="space-y-6">
    <!-- Personal Information -->
    <PersonalInformation
      :name="admin.name"
      :email="admin.email"
      :show-profile-photo="false"
      @save="handleSave"
      @change-password="handleChangePassword"
    />
  </div>
</template>
