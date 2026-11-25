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
    profileImage: null,
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
      password: {
        current_password: string;
        new_password: string;
        new_password_confirmation: string;
      } | null;
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

// Change Password
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
const validatePassword = (): boolean => {
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

// Save changes
const saveChanges = () => {
  if (!name.value.trim() || !email.value.trim()) {
    return;
  }

  if (isPasswordEditing.value && !validatePassword()) {
    return;
  }

  const password = isPasswordEditing.value
    ? {
        current_password: currentPassword.value,
        new_password: newPassword.value,
        new_password_confirmation: confirmPassword.value,
      }
    : null;

  emit("save", {
    name: name.value.trim(),
    email: email.value.trim(),
    profileImage: props.showProfilePhoto ? selectedPhoto.value : null,
    removeProfileImage: props.showProfilePhoto ? photoRemoved.value : false,
    password,
  });

  isNameEditing.value = false;
  isEmailEditing.value = false;
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
    class="mx-auto w-full min-w-0 max-w-3xl overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:rounded-2xl sm:p-6 lg:p-7"
  >
    <form class="w-full min-w-0" @submit.prevent="saveChanges">
      <!-- Heading -->
      <div class="mb-5 min-w-0 sm:mb-6">
        <h2 class="text-lg font-semibold text-slate-800 sm:text-xl">
          Personal Information
        </h2>

        <p class="mt-1 text-xs leading-relaxed text-slate-500 sm:text-sm">
          Manage your personal information and account settings.
        </p>
      </div>

      <!-- Profile Photo -->
      <div v-if="showProfilePhoto" class="mb-6">
        <label
          class="mb-3 block text-center text-sm font-medium text-slate-700"
        >
          {{ photoLabel || "Profile Photo" }}
        </label>

        <div class="flex flex-col items-center gap-3">
          <div
            class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 sm:h-24 sm:w-24"
          >
            <img
              v-if="displayedImage()"
              :src="displayedImage()!"
              alt="Profile photo"
              class="h-full w-full object-cover"
            />

            <UserRound v-else class="h-9 w-9 text-slate-400 sm:h-10 sm:w-10" />
          </div>

          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handlePhotoChange"
          />

          <div class="flex w-full flex-wrap justify-center gap-2">
            <button
              type="button"
              @click="openFilePicker"
              class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 sm:gap-2 sm:rounded-xl sm:px-4 sm:py-2.5 sm:text-sm"
            >
              <Upload class="h-4 w-4 shrink-0" />
              Upload Photo
            </button>

            <button
              v-if="displayedImage()"
              type="button"
              @click="removePhoto"
              class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-600 transition hover:bg-red-50 sm:gap-2 sm:rounded-xl sm:px-4 sm:py-2.5 sm:text-sm"
            >
              <Trash2 class="h-4 w-4 shrink-0" />
              Remove
            </button>
          </div>
        </div>

        <p
          class="mt-2 text-center text-[11px] leading-relaxed text-slate-400 sm:text-xs"
        >
          JPG, PNG or other image formats. Maximum size 2 MB.
        </p>

        <p
          v-if="photoError"
          class="mt-1 break-words text-center text-xs text-red-500 sm:text-sm"
        >
          {{ photoError }}
        </p>
      </div>

      <!-- Full Name -->
      <div class="mb-4 min-w-0">
        <label class="mb-2 block text-xs font-medium text-slate-700 sm:text-sm">
          {{ nameLabel || "Full Name" }}
        </label>

        <div class="relative min-w-0">
          <UserRound
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 sm:left-4 sm:h-5 sm:w-5"
          />

          <input
            v-model="name"
            type="text"
            :readonly="!isNameEditing"
            placeholder="Enter your full name"
            class="block w-full min-w-0 rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:rounded-xl sm:py-3 sm:pl-12 sm:pr-12"
          />

          <button
            type="button"
            @click="isNameEditing = !isNameEditing"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600 sm:right-3"
            :aria-label="isNameEditing ? 'Finish editing name' : 'Edit name'"
          >
            <Check v-if="isNameEditing" class="h-4 w-4" />
            <Pencil v-else class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Email -->
      <div class="mb-5 min-w-0">
        <label class="mb-2 block text-xs font-medium text-slate-700 sm:text-sm">
          {{ emailLabel || "Email Address" }}
        </label>

        <div class="relative min-w-0">
          <Mail
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 sm:left-4 sm:h-5 sm:w-5"
          />

          <input
            v-model="email"
            type="email"
            :readonly="!isEmailEditing"
            placeholder="Enter your email address"
            class="block w-full min-w-0 rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:rounded-xl sm:py-3 sm:pl-12 sm:pr-12"
          />

          <button
            type="button"
            @click="isEmailEditing = !isEmailEditing"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600 sm:right-3"
            :aria-label="isEmailEditing ? 'Finish editing email' : 'Edit email'"
          >
            <Check v-if="isEmailEditing" class="h-4 w-4" />
            <Pencil v-else class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Change Password -->
      <div class="min-w-0 border-t border-slate-200 pt-4 sm:pt-5">
        <div class="mb-3 flex min-w-0 items-center justify-between gap-3">
          <div class="flex min-w-0 items-center gap-2">
            <LockKeyhole
              class="h-4 w-4 shrink-0 text-slate-600 sm:h-5 sm:w-5"
            />

            <h3 class="text-sm font-semibold text-slate-800 sm:text-base">
              Change Password
            </h3>
          </div>

          <button
            type="button"
            @click="togglePasswordEditing"
            class="shrink-0 rounded-lg p-2 text-slate-500 transition hover:bg-blue-50 hover:text-blue-600"
            :aria-label="
              isPasswordEditing ? 'Cancel password editing' : 'Edit password'
            "
          >
            <X v-if="isPasswordEditing" class="h-4 w-4 sm:h-5 sm:w-5" />
            <Pencil v-else class="h-4 w-4 sm:h-5 sm:w-5" />
          </button>
        </div>

        <div v-if="isPasswordEditing" class="space-y-3 sm:space-y-4">
          <div class="min-w-0">
            <label
              class="mb-1.5 block text-xs font-medium text-slate-700 sm:text-sm"
            >
              Current Password
            </label>

            <input
              v-model="currentPassword"
              type="password"
              autocomplete="current-password"
              placeholder="Enter current password"
              class="block w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:rounded-xl sm:px-4 sm:py-3"
            />
          </div>

          <div class="min-w-0">
            <label
              class="mb-1.5 block text-xs font-medium text-slate-700 sm:text-sm"
            >
              New Password
            </label>

            <input
              v-model="newPassword"
              type="password"
              autocomplete="new-password"
              placeholder="Enter new password"
              class="block w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:rounded-xl sm:px-4 sm:py-3"
            />
          </div>

          <div class="min-w-0">
            <label
              class="mb-1.5 block text-xs font-medium text-slate-700 sm:text-sm"
            >
              Confirm New Password
            </label>

            <input
              v-model="confirmPassword"
              type="password"
              autocomplete="new-password"
              placeholder="Confirm new password"
              class="block w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:rounded-xl sm:px-4 sm:py-3"
            />
          </div>

          <p
            v-if="passwordError"
            class="break-words text-xs text-red-500 sm:text-sm"
          >
            {{ passwordError }}
          </p>
        </div>
      </div>

      <!-- Bottom Buttons -->
      <div
        class="mt-5 grid grid-cols-2 gap-2 border-t border-slate-200 pt-4 sm:flex sm:justify-end sm:gap-3"
      >
        <button
          type="button"
          @click="cancelChanges"
          class="inline-flex min-w-0 items-center justify-center rounded-lg border border-slate-300 px-3 py-2.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 sm:rounded-xl sm:px-5 sm:text-sm"
        >
          Cancel
        </button>

        <button
          type="submit"
          class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2.5 text-xs font-medium text-white transition hover:bg-blue-700 sm:rounded-xl sm:gap-2 sm:px-5 sm:text-sm"
        >
          <Check class="h-4 w-4 shrink-0" />
          <span>Save Changes</span>
        </button>
      </div>
    </form>
  </section>
</template>
