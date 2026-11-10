<script setup lang="ts">
import { ref } from "vue";

import type { LoginData } from "../types/auth.types";

const emit = defineEmits<{
  submit: [data: LoginData];
}>();

const email = ref("");
const password = ref("");
const remember = ref(false);

const handleSubmit = () => {
  emit("submit", {
    email: email.value,
    password: password.value,
    remember: remember.value,
  });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
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
        required
        class="w-full border rounded-lg px-4 py-2"
      />
    </div>

    <!-- Remember Me -->
    <div class="flex items-center gap-2">
      <input id="remember" v-model="remember" type="checkbox" class="w-4 h-4" />

      <label for="remember" class="text-sm"> Remember Me </label>
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg">
      Login
    </button>
  </form>
</template>
