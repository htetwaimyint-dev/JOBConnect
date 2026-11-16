<script setup lang="ts">
import { ref } from "vue";

import BaseInput from "@/components/common/BaseInput.vue";
import BasePasswordInput from "@/components/common/BasePasswordInput.vue";
import BaseCheckbox from "@/components/common/BaseCheckbox.vue";
import BaseButton from "@/components/common/BaseButton.vue";

import { Mail, LogIn } from "lucide-vue-next"; // Icon များ Import လုပ်ခြင်း

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
  <form @submit.prevent="handleSubmit" class="space-y-6" novalidate>
    <!-- Email -->
    <div class="space-y-1">
      <BaseInput
        id="login-email"
        v-model="email"
        type="email"
        label="Email Address"
        placeholder="you@example.com"
        size="md"
        variant="outline"
        :error="errors.email"
        required
        autocomplete="email"
        class="[&_input]:rounded-xl [&_input]:py-3 [&_input]:shadow-sm focus-within:[&_input]:ring-4 focus-within:[&_input]:ring-blue-500/10"
      >
        <template #leading>
          <Mail class="h-5 w-5 text-gray-400" />
        </template>
      </BaseInput>
    </div>

    <!-- Password -->
    <div class="space-y-1">
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
        class="[&_input]:rounded-xl [&_input]:py-3 [&_input]:shadow-sm focus-within:[&_input]:ring-4 focus-within:[&_input]:ring-blue-500/10"
      />
    </div>

    <!-- Remember me -->
    <div class="flex items-center pt-1">
      <BaseCheckbox
        id="login-remember"
        v-model="remember"
        label="Remember me "
        size="sm"
      />
    </div>

    <!-- Submit -->
    <div class="pt-2">
      <BaseButton
        type="submit"
        color="primary"
        variant="solid"
        size="40"
        class="group w-full justify-center rounded-xl py-3.5 text-base font-semibold shadow-lg shadow-blue-600/30 transition-all hover:shadow-blue-600/40 focus:ring-4 focus:ring-blue-500/30"
      >
        <template #leading>
          <LogIn
            class="h-5 w-5 transition-transform group-hover:translate-x-1"
          />
        </template>
        Sign in
      </BaseButton>
    </div>
  </form>
</template>
