<script setup lang="ts">
import { ref } from "vue";

import BaseInput from "@/components/common/BaseInput.vue";
import BasePasswordInput from "@/components/common/BasePasswordInput.vue";
import BaseButton from "@/components/common/BaseButton.vue";

import type { RegisterData } from "../types/auth.types";

const emit = defineEmits<{
  submit: [data: RegisterData];
}>();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const role = ref<"job_seeker" | "employer">("job_seeker");

const errors = ref<{
  name?: string;
  email?: string;
  password?: string;
  confirmPassword?: string;
}>({});

const validate = (): boolean => {
  const next: typeof errors.value = {};

  if (!name.value.trim()) {
    next.name = "Name is required";
  }

  if (!email.value.trim()) {
    next.email = "Email is required";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    next.email = "Please enter a valid email address";
  }

  if (!password.value) {
    next.password = "Password is required";
  } else if (password.value.length < 8) {
    next.password = "Password must be at least 8 characters";
  }

  if (!confirmPassword.value) {
    next.confirmPassword = "Please confirm your password";
  } else if (password.value !== confirmPassword.value) {
    next.confirmPassword = "Passwords do not match!";
  }

  errors.value = next;
  return Object.keys(next).length === 0;
};

const handleSubmit = () => {
  if (!validate()) return;

  emit("submit", {
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: confirmPassword.value,
    role: role.value,
  });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
    <!-- Name -->
    <BaseInput
      id="register-name"
      v-model="name"
      type="text"
      label="Name"
      placeholder="Enter your name"
      size="md"
      variant="outline"
      :error="errors.name"
      required
      autocomplete="name"
    >
      <template #leading>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
        </svg>
      </template>
    </BaseInput>

    <!-- Email -->
    <BaseInput
      id="register-email"
      v-model="email"
      type="email"
      label="Email"
      placeholder="you@example.com"
      size="md"
      variant="outline"
      :error="errors.email"
      required
      autocomplete="email"
    >
      <template #leading>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
          />
        </svg>
      </template>
    </BaseInput>

    <!-- Password -->
    <BasePasswordInput
      id="register-password"
      v-model="password"
      label="Password"
      placeholder="Create a password"
      size="md"
      variant="outline"
      :error="errors.password"
      required
      autocomplete="new-password"
    />

    <!-- Confirm Password -->
    <BasePasswordInput
      id="register-confirm-password"
      v-model="confirmPassword"
      label="Confirm Password"
      placeholder="Confirm your password"
      size="md"
      variant="outline"
      :error="errors.confirmPassword"
      required
      autocomplete="new-password"
    />

    <!-- Role Selection -->
    <div>
      <span class="mb-2 block text-sm font-medium text-gray-700">
        Register as
      </span>

      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <!-- Job seeker -->
        <label
          class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors"
          :class="
            role === 'job_seeker'
              ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20'
              : 'border-gray-300 bg-white hover:border-gray-400'
          "
        >
          <input
            v-model="role"
            type="radio"
            value="job_seeker"
            class="mt-0.5 h-4 w-4 accent-blue-600"
          />
          <div class="min-w-0">
            <span
              class="flex items-center gap-1.5 text-sm font-medium text-gray-900"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"
                />
              </svg>
              Find a Job
            </span>
            <p class="mt-0.5 text-xs text-gray-500">
              Browse and apply to open positions
            </p>
          </div>
        </label>

        <!-- Employer -->
        <label
          class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors"
          :class="
            role === 'employer'
              ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20'
              : 'border-gray-300 bg-white hover:border-gray-400'
          "
        >
          <input
            v-model="role"
            type="radio"
            value="employer"
            class="mt-0.5 h-4 w-4 accent-blue-600"
          />
          <div class="min-w-0">
            <span
              class="flex items-center gap-1.5 text-sm font-medium text-gray-900"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 13.255A23.93 23.93 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
              Post a Job
            </span>
            <p class="mt-0.5 text-xs text-gray-500">
              Create listings and find talent
            </p>
          </div>
        </label>
      </div>
    </div>

    <!-- Submit -->
    <BaseButton
      type="submit"
      color="primary"
      variant="solid"
      size="40"
      class="w-full justify-center"
    >
      <template #leading>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
          />
        </svg>
      </template>
      Create account
    </BaseButton>
  </form>
</template>
