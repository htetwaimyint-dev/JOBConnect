<script setup lang="ts">
import { ref } from "vue";

import BaseInput from "@/components/common/BaseInput.vue";
import BasePasswordInput from "@/components/common/BasePasswordInput.vue";
import BaseCheckbox from "@/components/common/BaseCheckbox.vue";
import BaseButton from "@/components/common/BaseButton.vue";

import type { LoginData } from "../types/auth.types";

const emit = defineEmits<{
  submit: [data: LoginData];
}>();

const email = ref("");
const password = ref("");
const remember = ref(false);
const errors = ref<{ email?: string; password?: string }>({});

const validate = (): boolean => {
  const next: { email?: string; password?: string } = {};

  if (!email.value.trim()) {
    next.email = "Email is required";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    next.email = "Please enter a valid email address";
  }

  if (!password.value) {
    next.password = "Password is required";
  } else if (password.value.length < 6) {
    next.password = "Password must be at least 6 characters";
  }

  errors.value = next;
  return Object.keys(next).length === 0;
};

const handleSubmit = () => {
  if (!validate()) return;

  emit("submit", {
    email: email.value,
    password: password.value,
    remember: remember.value,
  });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
    <!-- Email -->
    <BaseInput
      id="login-email"
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
      id="login-password"
      v-model="password"
      label="Password"
      placeholder="Enter your password"
      size="md"
      variant="outline"
      :error="errors.password"
      required
      autocomplete="current-password"
    />

    <!-- Remember me -->
    <BaseCheckbox
      id="login-remember"
      v-model="remember"
      label="Remember me"
      size="sm"
    />

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
            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
          />
        </svg>
      </template>
      Sign in
    </BaseButton>
  </form>
</template>
