<script setup lang="ts">
import { ref, onBeforeUnmount } from "vue";

import PersonalInformation from "@/components/common/PersonalInformation.vue";

const employer = ref({
  name: "Employer Name",
  email: "employer@example.com",
  profileImage: null as string | null,
});

const handleSave = (data: {
  name: string;
  email: string;
  profileImage: File | null;
  removeProfileImage: boolean;
}) => {
  const oldImage = employer.value.profileImage;

  employer.value.name = data.name;
  employer.value.email = data.email;

  if (data.profileImage) {
    employer.value.profileImage = URL.createObjectURL(data.profileImage);
  } else if (data.removeProfileImage) {
    employer.value.profileImage = null;
  }

  // Release the previous preview URL
  if (oldImage?.startsWith("blob:")) {
    URL.revokeObjectURL(oldImage);
  }

  console.log("Updated employer information:", data);
};

// Release the current preview URL when leaving the page
onBeforeUnmount(() => {
  const image = employer.value.profileImage;

  if (image?.startsWith("blob:")) {
    URL.revokeObjectURL(image);
  }
});
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-800">Account Settings</h1>

      <p class="mt-1 text-sm text-slate-500">
        Manage your company account and personal information.
      </p>
    </div>

    <PersonalInformation
      :name="employer.name"
      :email="employer.email"
      :profile-image="employer.profileImage"
      @save="handleSave"
    />
  </div>
</template>
