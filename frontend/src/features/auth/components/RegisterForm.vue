<script setup lang="ts">
import { ref } from "vue";

import BaseInput from "@/components/common/BaseInput.vue";
import BasePasswordInput from "@/components/common/BasePasswordInput.vue";
import BaseButton from "@/components/common/BaseButton.vue";

import type { RegisterData } from "../types/auth.types";
import { User, Mail, Lock, LogIn, Search, Briefcase } from "lucide-vue-next";

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
      label="Full Name"
      placeholder="Enter your full name"
      size="md"
      variant="outline"
      :error="errors.name"
      required
      autocomplete="name"
      class="[&_input]:rounded-xl [&_input]:py-3 [&_input]:shadow-sm focus-within:[&_input]:ring-4 focus-within:[&_input]:ring-blue-500/10"
    >
      <template #leading>
        <User class="h-5 w-5 text-gray-400" />
      </template>
    </BaseInput>

    <!-- Email -->
    <BaseInput
      id="register-email"
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
      class="[&_input]:rounded-xl [&_input]:py-3 [&_input]:shadow-sm focus-within:[&_input]:ring-4 focus-within:[&_input]:ring-blue-500/10"
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
      class="[&_input]:rounded-xl [&_input]:py-3 [&_input]:shadow-sm focus-within:[&_input]:ring-4 focus-within:[&_input]:ring-blue-500/10"
    />

    <!-- Role Selection (Compact Cards) -->
    <div class="pt-1">
      <span class="mb-2 block text-sm font-medium text-gray-700">
        I want to join as
      </span>

      <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
        <!-- Job Seeker Card -->
        <label
          class="group relative flex cursor-pointer items-center gap-3 rounded-lg border p-2.5 transition-all hover:bg-gray-50"
          :class="
            role === 'job_seeker'
              ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-600/20'
              : 'border-gray-200 bg-white'
          "
        >
          <input
            v-model="role"
            type="radio"
            value="job_seeker"
            class="sr-only"
          />
          <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-200"
          >
            <Search class="h-4 w-4" />
          </div>
          <div class="min-w-0 flex-1">
            <span class="block text-sm font-semibold text-gray-900"
              >Job Seeker</span
            >
            <p class="text-[11px] text-gray-500">Looking for a job</p>
          </div>
          <!-- Radio Indicator -->
          <div
            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 transition-all"
            :class="
              role === 'job_seeker' ? 'border-blue-600' : 'border-gray-300'
            "
          >
            <div
              v-if="role === 'job_seeker'"
              class="h-2 w-2 rounded-full bg-blue-600"
            />
          </div>
        </label>

        <!-- Employer Card -->
        <label
          class="group relative flex cursor-pointer items-center gap-3 rounded-lg border p-2.5 transition-all hover:bg-gray-50"
          :class="
            role === 'employer'
              ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-600/20'
              : 'border-gray-200 bg-white'
          "
        >
          <input v-model="role" type="radio" value="employer" class="sr-only" />
          <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-200"
          >
            <Briefcase class="h-4 w-4" />
          </div>
          <div class="min-w-0 flex-1">
            <span class="block text-sm font-semibold text-gray-900"
              >Employer</span
            >
            <p class="text-[11px] text-gray-500">Hiring talent</p>
          </div>
          <!-- Radio Indicator -->
          <div
            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 transition-all"
            :class="role === 'employer' ? 'border-blue-600' : 'border-gray-300'"
          >
            <div
              v-if="role === 'employer'"
              class="h-2 w-2 rounded-full bg-blue-600"
            />
          </div>
        </label>
      </div>
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
        Create account
      </BaseButton>
    </div>
  </form>
</template>
