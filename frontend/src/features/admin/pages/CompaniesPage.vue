<script setup lang="ts">
import { ref, computed } from "vue";
import {
  Users,
  UserCheck,
  UserX,
  Search,
  Plus,
  Eye,
  RotateCcw,
  Trash2,
  X,
} from "lucide-vue-next";

// --- Mock Data ---
const users = ref([
  {
    id: 1,
    name: "TechCorp Solutions",
    email: "contact@techcorp.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1549923746-c502d488b3ea?w=100&h=100&fit=crop",
    company: "TechCorp Solutions",
    companyLogo:
      "https://images.unsplash.com/photo-1549923746-c502d488b3ea?w=100&h=100&fit=crop",
    jobCount: 12,
    isDeleted: false,
    joinedDate: "Mar 12, 2024",
  },
  {
    id: 2,
    name: "Creative Studio",
    email: "hello@creativestudio.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=100&h=100&fit=crop",
    company: "Creative Studio",
    companyLogo:
      "https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=100&h=100&fit=crop",
    jobCount: 5,
    isDeleted: false,
    joinedDate: "Mar 10, 2024",
  },
  {
    id: 3,
    name: "Global Finance",
    email: "hr@globalfinance.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop",
    company: "Global Finance",
    companyLogo:
      "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop",
    jobCount: 0,
    isDeleted: true,
    joinedDate: "Feb 28, 2024",
  },
  {
    id: 4,
    name: "Startup Inc.",
    email: "jobs@startupinc.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop",
    company: "Startup Inc.",
    companyLogo:
      "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop",
    jobCount: 3,
    isDeleted: false,
    joinedDate: "Feb 15, 2024",
  },
]);

// --- State & Computed ---
const searchQuery = ref("");
const selectedStatus = ref("All Statuses");

// Filter Logic
const filteredUsers = computed(() => {
  return users.value.filter((user) => {
    const matchesSearch =
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase());

    let matchesStatus = true;
    if (selectedStatus.value === "Active") matchesStatus = !user.isDeleted;
    if (selectedStatus.value === "Suspended") matchesStatus = user.isDeleted;

    return matchesSearch && matchesStatus;
  });
});

// Stats
const stats = computed(() => {
  const total = users.value.length;
  const active = users.value.filter((u) => !u.isDeleted).length;
  const suspended = users.value.filter((u) => u.isDeleted).length;
  return { total, active, suspended };
});

// --- Actions ---
const toggleDelete = (user: any) => {
  user.isDeleted = !user.isDeleted;
};

const viewCompanyDetail = (user: any) => {
  alert(`Viewing company: ${user.company}`);
};

const clearSearch = () => {
  searchQuery.value = "";
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-6 sm:p-8">
    <div class="mx-auto max-w-7xl">
      <!-- 1. Header Section -->
      <div
        class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
      >
        <div>
          <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
          <p class="mt-1 text-sm text-slate-500">
            Manage website users, roles and permissions.
          </p>
        </div>
        <button
          class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
        >
          <Plus class="h-4 w-4" />
          Add New User
        </button>
      </div>

      <!-- 2. Stats Cards -->
      <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-4">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600"
            >
              <Users class="h-6 w-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500">Total Users</p>
              <h3 class="text-2xl font-bold text-slate-900">
                {{ stats.total }}
              </h3>
              <p class="text-xs text-green-600 font-medium mt-1">
                ▲ +12% from last month
              </p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-4">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"
            >
              <UserCheck class="h-6 w-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500">Active Users</p>
              <h3 class="text-2xl font-bold text-slate-900">
                {{ stats.active }}
              </h3>
              <p class="text-xs text-green-600 font-medium mt-1">
                ▲ +8% from last month
              </p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center gap-4">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600"
            >
              <UserX class="h-6 w-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-slate-500">Suspended Users</p>
              <h3 class="text-2xl font-bold text-slate-900">
                {{ stats.suspended }}
              </h3>
              <p class="text-xs text-red-500 font-medium mt-1">
                ▼ -4% from last month
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Main Table Container -->
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <!-- Toolbar -->
        <div
          class="border-b border-slate-200 p-5 flex flex-col sm:flex-row gap-4 justify-between items-center bg-white rounded-t-xl"
        >
          <!-- Search -->
          <div class="relative w-full max-w-md">
            <Search
              class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
            />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search users by name, email..."
              class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-10 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
            >
              <X class="h-4 w-4" />
            </button>
          </div>

          <!-- Status Filter -->
          <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <select
              v-model="selectedStatus"
              class="rounded-lg border border-slate-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none"
            >
              <option>All Statuses</option>
              <option>Active</option>
              <option>Suspended</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600">
            <thead
              class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200"
            >
              <tr>
                <th class="px-6 py-4 font-medium">No.</th>
                <th class="px-6 py-4 font-medium">Name</th>
                <th class="px-6 py-4 font-medium">Email</th>
                <th class="px-6 py-4 font-medium">Company</th>
                <th class="px-6 py-4 font-medium text-center">Job Count</th>
                <th class="px-6 py-4 font-medium text-center">Status</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="(user, index) in filteredUsers"
                :key="user.id"
                class="hover:bg-slate-50 transition-colors"
              >
                <!-- No. -->
                <td class="px-6 py-4 text-slate-500">{{ index + 1 }}</td>

                <!-- Name (Logo + Name) -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      :src="user.logo"
                      alt="Logo"
                      class="h-10 w-10 rounded-full object-cover border border-slate-200"
                    />
                    <div>
                      <p
                        class="font-semibold text-slate-900"
                        :class="{ 'line-through opacity-50': user.isDeleted }"
                      >
                        {{ user.name }}
                      </p>
                      <p class="text-xs text-slate-500">{{ user.role }}</p>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td class="px-6 py-4">{{ user.email }}</td>

                <!-- Company (Logo + Name + View Button) -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <!-- Company Logo လေး ထည့်ထားတယ် -->
                    <img
                      :src="user.companyLogo"
                      alt="Company Logo"
                      class="h-6 w-6 rounded object-cover border border-slate-200"
                    />
                    <span class="font-medium text-slate-800">{{
                      user.company
                    }}</span>
                    <button
                      @click="viewCompanyDetail(user)"
                      class="p-1.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors"
                      title="View Company"
                    >
                      <Eye class="h-4 w-4" />
                    </button>
                  </div>
                </td>

                <!-- Job Count -->
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex items-center justify-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700"
                  >
                    {{ user.jobCount }}
                  </span>
                </td>

                <!-- Status (Active/Suspended) -->
                <td class="px-6 py-4 text-center">
                  <span
                    v-if="!user.isDeleted"
                    class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700"
                  >
                    Active
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700"
                  >
                    Suspended
                  </span>
                </td>

                <!-- Actions (Soft Delete / Restore) -->
                <td class="px-6 py-4 text-right">
                  <!-- Restore Button -->
                  <button
                    v-if="user.isDeleted"
                    @click="toggleDelete(user)"
                    class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100 transition-colors"
                  >
                    <RotateCcw class="h-3.5 w-3.5" /> Restore
                  </button>

                  <!-- Delete Button -->
                  <button
                    v-else
                    @click="toggleDelete(user)"
                    class="inline-flex items-center gap-1 rounded-md bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors"
                  >
                    <Trash2 class="h-3.5 w-3.5" /> Delete
                  </button>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="filteredUsers.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                  No users found matching your search.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer (Pagination) -->
        <div
          class="border-t border-slate-200 p-5 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50 rounded-b-xl"
        >
          <p class="text-sm text-slate-500">
            Showing <span class="font-medium text-slate-900">1</span> to
            <span class="font-medium text-slate-900">{{
              filteredUsers.length
            }}</span>
            of
            <span class="font-medium text-slate-900">{{ stats.total }}</span>
            users
          </p>

          <div class="flex items-center gap-1">
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-slate-500 hover:bg-slate-100 disabled:opacity-50"
              disabled
            >
              &lt;
            </button>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md bg-indigo-600 text-sm font-medium text-white"
            >
              1
            </button>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100"
            >
              2
            </button>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100"
            >
              3
            </button>
            <span class="px-1 text-slate-400">...</span>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-100"
            >
              24
            </button>
            <button
              class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 text-slate-500 hover:bg-slate-100"
            >
              &gt;
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
