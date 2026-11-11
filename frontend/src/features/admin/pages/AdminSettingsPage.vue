<script setup lang="ts">
import { ref, onBeforeUnmount } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";

const admin = ref({
  name: "Admin",
  email: "admin@jobconnect.com",
  profileImage: null as string | null,
});

const handleSave = (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  admin.value.name = data.name;
  admin.value.email = data.email;

  if (data.profileImage) {
    if (admin.value.profileImage) {
      URL.revokeObjectURL(admin.value.profileImage);
    }

    admin.value.profileImage = URL.createObjectURL(data.profileImage);
  } else if (data.removeProfileImage) {
    if (admin.value.profileImage) {
      URL.revokeObjectURL(admin.value.profileImage);
    }

    admin.value.profileImage = null;
  }

  console.log("Updated admin information:", data);
};

onBeforeUnmount(() => {
  if (admin.value.profileImage) {
    URL.revokeObjectURL(admin.value.profileImage);
  }
});
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Account Settings</h1>

      <p class="mt-1 text-sm text-slate-500">
        Manage your admin account and personal information.
      </p>
    </div>

    <PersonalInformation
      :name="admin.name"
      :email="admin.email"
      :profile-image="admin.profileImage"
      @save="handleSave"
    />
  </div>
</template>
