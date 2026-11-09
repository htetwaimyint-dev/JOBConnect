<script setup lang="ts">
import { ref } from "vue";

import type { RegisterData } from "../types/auth.types";

const emit = defineEmits<{
  submit: [data: RegisterData];
}>();

const name = ref("");
const email = ref("");
const password = ref("");
const role = ref<"job_seeker" | "employer">("job_seeker");

const handleSubmit = () => {
  emit("submit", {
    name: name.value,
    email: email.value,
    password: password.value,
    role: role.value,
  });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label class="block mb-1 font-medium">Name</label>
      <input
        v-model="name"
        type="text"
        placeholder="Enter your name"
        required
        class="w-full border rounded-lg px-4 py-2"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">Email</label>
      <input
        v-model="email"
        type="email"
        placeholder="Enter your email"
        required
        class="w-full border rounded-lg px-4 py-2"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">Password</label>
      <input
        v-model="password"
        type="password"
        placeholder="Enter your password"
        minlength="8"
        required
        class="w-full border rounded-lg px-4 py-2"
      />
    </div>

    <!-- Role Selection -->
    <div>
      <label class="block mb-2 font-medium">Register As</label>

      <div class="flex gap-6">
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            v-model="role"
            type="radio"
            value="job_seeker"
            class="accent-blue-600"
          />
          <span>Find a Job</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
          <input
            v-model="role"
            type="radio"
            value="employer"
            class="accent-blue-600"
          />
          <span>Post a Job</span>
        </label>
      </div>
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg">
      Register
    </button>
  </form>
</template>
