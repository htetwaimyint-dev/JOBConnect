<script setup lang="ts">
import { ref } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";

const seeker = ref({
  name: "Aung Aung",
  email: "aung@example.com",
  profileImage: null as string | null,
});

const handleSave = (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  seeker.value.name = data.name;
  seeker.value.email = data.email;

  if (data.profileImage) {
    seeker.value.profileImage = URL.createObjectURL(data.profileImage);
  } else if (data.removeProfileImage) {
    seeker.value.profileImage = null;
  }

  console.log("Updated information:", data);
};
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Account Settings</h1>

      <p class="mt-1 text-sm text-slate-500">
        Manage your profile and account security.
      </p>
    </div>

    <PersonalInformation
      :name="seeker.name"
      :email="seeker.email"
      :profile-image="seeker.profileImage"
      @save="handleSave"
    />
  </div>
</template>
