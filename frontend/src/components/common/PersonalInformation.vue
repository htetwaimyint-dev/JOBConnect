<script setup lang="ts">
import { onBeforeUnmount, ref, watch } from "vue";

import {
  UserRound,
  Mail,
  Pencil,
  Upload,
  Trash2,
  Check,
  LockKeyhole,
  X,
} from "lucide-vue-next";

const props = withDefaults(
  defineProps<{
    name: string;
    email: string;
    profileImage?: string | null;
    nameLabel?: string;
    emailLabel?: string;
    photoLabel?: string;
    showProfilePhoto?: boolean;
  }>(),
  {
    showProfilePhoto: true,
  },
);

const emit = defineEmits<{
  save: [
    data: {
      name: string;
      email: string;
      profileImage: File | null;
      removeProfileImage: boolean;
    },
  ];

  changePassword: [
    data: {
      current_password: string;
      new_password: string;
      new_password_confirmation: string;
    },
  ];
}>();

// Personal Information
const name = ref(props.name);
const email = ref(props.email);

const isNameEditing = ref(false);
const isEmailEditing = ref(false);

// Profile Photo
const selectedPhoto = ref<File | null>(null);
const previewUrl = ref<string | null>(null);
const photoRemoved = ref(false);
const photoError = ref("");
const fileInput = ref<HTMLInputElement | null>(null);

// Password
const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");
const passwordError = ref("");
const isPasswordEditing = ref(false);

// Watch selected photo
watch(selectedPhoto, (file) => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }

  previewUrl.value = file ? URL.createObjectURL(file) : null;
});

// Display profile image
const displayedImage = () => {
  if (photoRemoved.value) return null;

  return previewUrl.value || props.profileImage;
};

// Watch props
watch(
  () => [props.name, props.email],
  () => {
    name.value = props.name;
    email.value = props.email;
  },
);

// Open file picker
const openFilePicker = () => {
  fileInput.value?.click();
};

// Handle photo upload
const handlePhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;

  photoError.value = "";

  if (!file.type.startsWith("image/")) {
    photoError.value = "Please select an image file.";
    target.value = "";
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    photoError.value = "Image size must be less than 2 MB.";
    target.value = "";
    return;
  }

  selectedPhoto.value = file;
  photoRemoved.value = false;
};

// Remove photo
const removePhoto = () => {
  selectedPhoto.value = null;
  photoRemoved.value = true;
  photoError.value = "";

  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

// Toggle password editing
const togglePasswordEditing = () => {
  if (isPasswordEditing.value) {
    cancelPasswordEditing();
  } else {
    passwordError.value = "";
    isPasswordEditing.value = true;
  }
};

// Cancel password editing
const cancelPasswordEditing = () => {
  currentPassword.value = "";
  newPassword.value = "";
  confirmPassword.value = "";
  passwordError.value = "";
  isPasswordEditing.value = false;
};

// Validate password
const validatePassword = () => {
  passwordError.value = "";

  if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
    passwordError.value = "Please fill in all password fields.";
    return false;
  }

  if (newPassword.value.length < 8) {
    passwordError.value = "Password must be at least 8 characters.";
    return false;
  }

  if (newPassword.value === currentPassword.value) {
    passwordError.value =
      "New password must be different from current password.";
    return false;
  }

  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = "New password and confirmation do not match.";
    return false;
  }

  return true;
};

// Save Changes
const saveChanges = () => {
  if (!name.value.trim() || !email.value.trim()) {
    return;
  }

  // Password Change
  if (isPasswordEditing.value) {
    if (!validatePassword()) {
      return;
    }

    // Emit ONLY password information
    emit("changePassword", {
      current_password: currentPassword.value,
      new_password: newPassword.value,
      new_password_confirmation: confirmPassword.value,
    });

    return;
  }

  // Account Information Update
  emit("save", {
    name: name.value.trim(),
    email: email.value.trim(),
    profileImage: selectedPhoto.value,
    removeProfileImage: photoRemoved.value,
  });
};

// Cancel all changes
const cancelChanges = () => {
  name.value = props.name;
  email.value = props.email;

  selectedPhoto.value = null;
  photoRemoved.value = false;
  photoError.value = "";

  isNameEditing.value = false;
  isEmailEditing.value = false;

  cancelPasswordEditing();

  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

// Cleanup preview URL
onBeforeUnmount(() => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
});
</script>

<template>
  <section
    class="mx-auto w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6"
  >
    <form @submit.prevent="saveChanges">
      Heading
      <div class="mb-5">
        <h2 class="text-xl font-semibold text-slate-800">
          Personal Information
        </h2>

        <p class="mt-1 text-sm text-slate-500">
          Manage your personal information and account settings.
        </p>
      </div>

      <!-- Profile Photo -->
      <div v-if="props.showProfilePhoto" class="mb-5">
        <label
          class="mb-3 block text-center text-sm font-medium text-slate-700"
        >
          {{ photoLabel || "Profile Photo" }}
        </label>

        <div class="flex flex-col items-center gap-3">
          <div
            class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100"
          >
            <img
              v-if="displayedImage()"
              :src="displayedImage()!"
              alt="Profile photo"
              class="h-full w-full object-cover"
            />

            <UserRound v-else class="h-9 w-9 text-slate-400" />
          </div>

          <div class="flex flex-wrap justify-center gap-2">
            <input
              ref="fileInput"
              type="file"
              accept="image/*"
              class="hidden"
              @change="handlePhotoChange"
            />

            <button
              type="button"
              @click="openFilePicker"
              class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
              <Upload class="h-4 w-4" />
              Upload Photo
            </button>

            <button
              v-if="displayedImage()"
              type="button"
              @click="removePhoto"
              class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
            >
              <Trash2 class="h-4 w-4" />
              Remove
            </button>
          </div>
        </div>

        <p class="mt-2 text-center text-xs text-slate-400">
          JPG, PNG or other image formats. Maximum size 2 MB.
        </p>

        <p v-if="photoError" class="mt-1 text-center text-sm text-red-500">
          {{ photoError }}
        </p>
      </div>

      <!-- Full Name -->
      <div class="mb-4">
        <label class="mb-2 block text-sm font-medium text-slate-700">
          {{ nameLabel || "Full Name" }}
        </label>

        <div class="relative">
          <UserRound
            class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
          />

          <input
            v-model="name"
            type="text"
            :readonly="!isNameEditing"
            placeholder="Enter your full name"
            class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-12 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />

          <button
            type="button"
            @click="isNameEditing = !isNameEditing"
            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
            :aria-label="isNameEditing ? 'Finish editing name' : 'Edit name'"
          >
            <Check v-if="isNameEditing" class="h-4 w-4" />
            <Pencil v-else class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Email -->
      <div class="mb-5">
        <label class="mb-2 block text-sm font-medium text-slate-700">
          {{ emailLabel || "Email Address" }}
        </label>

        <div class="relative">
          <Mail
            class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
          />

          <input
            v-model="email"
            type="email"
            :readonly="!isEmailEditing"
            placeholder="Enter your email address"
            class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-12 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />

          <button
            type="button"
            @click="isEmailEditing = !isEmailEditing"
            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
            :aria-label="isEmailEditing ? 'Finish editing email' : 'Edit email'"
          >
            <Check v-if="isEmailEditing" class="h-4 w-4" />
            <Pencil v-else class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Change Password -->
      <div class="border-t border-slate-200 pt-4">
        <div class="mb-2 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <LockKeyhole class="h-5 w-5 text-slate-600" />

            <h3 class="text-base font-semibold text-slate-800">
              Change Password
            </h3>
          </div>

          <button
            type="button"
            @click="togglePasswordEditing"
            class="rounded-xl p-2 text-slate-500 transition hover:bg-blue-50 hover:text-blue-600"
            :aria-label="
              isPasswordEditing ? 'Cancel password editing' : 'Edit password'
            "
          >
            <X v-if="isPasswordEditing" class="h-5 w-5" />
            <Pencil v-else class="h-5 w-5" />
          </button>
        </div>

        <div v-if="isPasswordEditing" class="space-y-4">
          <!-- Current Password -->
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
              Current Password
            </label>

            <input
              v-model="currentPassword"
              type="password"
              autocomplete="current-password"
              placeholder="Enter current password"
              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- New Password -->
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
              New Password
            </label>

            <input
              v-model="newPassword"
              type="password"
              autocomplete="new-password"
              placeholder="Enter new password"
              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <!-- Confirm New Password -->
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
              Confirm New Password
            </label>

            <input
              v-model="confirmPassword"
              type="password"
              autocomplete="new-password"
              placeholder="Confirm new password"
              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <p v-if="passwordError" class="text-sm text-red-500">
            {{ passwordError }}
          </p>
        </div>
      </div>

      <!-- Bottom Buttons -->
      <div class="mt-5 flex justify-end gap-3 border-t border-slate-200 pt-4">
        <button
          type="button"
          @click="cancelChanges"
          class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
        >
          Cancel
        </button>

        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700"
        >
          <Check class="h-4 w-4" />
          Save Changes
        </button>
      </div>
    </form>
  </section>
</template>
