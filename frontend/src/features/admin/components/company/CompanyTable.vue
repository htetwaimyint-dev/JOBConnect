<script setup lang="ts">
import {
  Search,
  Building2,
  LoaderCircle,
  MapPin,
  CalendarDays,
  Eye,
  CheckCircle2,
  XCircle,
} from "lucide-vue-next";

import type {
  Company,
  CompanyStatusFilter,
} from "../../types/company-approval.types";

import BasePagination from "@/components/common/BasePagination.vue";

interface FilterOption {
  label: string;
  value: CompanyStatusFilter;
}

defineProps<{
  companies: Company[];
  filteredCompanies: Company[];
  filters: FilterOption[];
  loading: boolean;
  searchQuery: string;
  activeFilter: CompanyStatusFilter;
  currentPage: number;
  lastPage: number;
  totalCompanies: number;
  perPage: number;
  statusClass: (status: Company["verification_status"]) => string;
  statusDotClass: (status: Company["verification_status"]) => string;
  statusLabel: (status: Company["verification_status"]) => string;
  formatDate: (date: string | null) => string;
}>();

const emit = defineEmits<{
  "update:searchQuery": [value: string];
  "update:per-page": [value: number];
  changeFilter: [value: CompanyStatusFilter];
  refresh: [];
  goToPage: [page: number];
  openDetails: [company: Company];
  openApproval: [company: Company, status: "verified" | "rejected"];
}>();
</script>

<template>
  <div
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
  >
    <!-- Filter Header -->
    <div class="space-y-5 border-b border-slate-100 p-5">
      <div>
        <h2 class="font-semibold text-slate-900">Companies</h2>
        <p class="mt-1 text-xs text-slate-500">
          View and manage company verification statuses.
        </p>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-2">
        <button
          v-for="filter in filters"
          :key="filter.value"
          type="button"
          @click="emit('changeFilter', filter.value)"
          :disabled="loading"
          class="rounded-xl px-4 py-2.5 text-sm font-semibold transition disabled:opacity-50"
          :class="
            activeFilter === filter.value
              ? 'bg-blue-600 text-white shadow-sm shadow-blue-200'
              : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
          "
        >
          {{ filter.label }}
        </button>
      </div>

      <!-- Search -->
      <div class="relative w-full sm:max-w-sm">
        <Search
          :size="18"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
        />

        <input
          :value="searchQuery"
          @input="
            emit(
              'update:searchQuery',
              ($event.target as HTMLInputElement).value,
            )
          "
          type="text"
          placeholder="Search company, employer, email..."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20">
      <LoaderCircle :size="32" class="animate-spin text-blue-600" />
      <p class="mt-3 text-sm text-slate-500">Loading companies...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredCompanies.length === 0"
      class="flex flex-col items-center justify-center px-5 py-20 text-center"
    >
      <div
        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
      >
        <Building2 :size="30" />
      </div>

      <h3 class="mt-4 font-semibold text-slate-800">
        {{ searchQuery ? "No matching companies" : "No companies found" }}
      </h3>

      <p class="mt-1 max-w-sm text-sm text-slate-500">
        {{
          searchQuery
            ? "Try another company name, industry or email."
            : "There are no companies under this filter."
        }}
      </p>
    </div>

    <!-- Company Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full min-w-[1450px] text-left">
        <thead class="bg-slate-50">
          <tr
            class="text-xs font-semibold uppercase tracking-wider text-slate-500"
          >
            <th class="px-5 py-4">Company</th>
            <th class="px-5 py-4">Employer</th>
            <th class="px-5 py-4">Location</th>
            <th class="px-5 py-4">Contact</th>
            <th class="px-5 py-4">Founded</th>
            <th class="px-5 py-4">Employees</th>
            <th class="px-5 py-4">Status</th>
            <th class="px-5 py-4">Submitted</th>
            <th class="px-5 py-4 text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="company in filteredCompanies"
            :key="company.id"
            class="transition hover:bg-slate-50/70"
          >
            <!-- Company -->
            <td class="px-5 py-4">
              <div class="flex items-center gap-3">
                <img
                  v-if="company.logo"
                  :src="company.logo"
                  :alt="company.name"
                  class="h-11 w-11 rounded-xl border border-slate-200 object-cover"
                />

                <div
                  v-else
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                >
                  <Building2 :size="20" />
                </div>

                <div class="max-w-[180px]">
                  <p class="truncate font-semibold text-slate-800">
                    {{ company.name }}
                  </p>
                  <p class="mt-1 truncate text-xs text-slate-500">
                    {{ company.industry || "Industry not specified" }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Employer -->
            <td class="px-5 py-4">
              <p class="text-sm font-medium text-slate-700">
                {{ company.employer?.name || "Unknown" }}
              </p>
              <p class="mt-1 text-xs text-slate-500">
                {{ company.employer?.email || "—" }}
              </p>
            </td>

            <!-- Location -->
            <td class="px-5 py-4">
              <div class="flex items-center gap-1.5 text-sm text-slate-600">
                <MapPin :size="15" class="shrink-0 text-slate-400" />
                {{ company.city || "Not specified" }}
              </div>
              <p class="mt-1 max-w-[160px] truncate text-xs text-slate-400">
                {{ company.address || "" }}
              </p>
            </td>

            <!-- Contact -->
            <td class="px-5 py-4">
              <p class="text-sm text-slate-700">
                {{ company.company_email || "—" }}
              </p>
              <p class="mt-1 text-xs text-slate-500">
                {{ company.phone || "No phone" }}
              </p>
            </td>

            <!-- Founded -->
            <td class="px-5 py-4">
              <span class="text-sm text-slate-600">
                {{ company.founded_year || "—" }}
              </span>
            </td>

            <!-- Employees -->
            <td class="px-5 py-4">
              <span class="text-sm text-slate-600">
                {{ company.employee_count || "—" }}
              </span>
            </td>

            <!-- Status -->
            <td class="px-5 py-4">
              <span
                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset"
                :class="statusClass(company.verification_status)"
              >
                <span
                  class="h-1.5 w-1.5 rounded-full"
                  :class="statusDotClass(company.verification_status)"
                ></span>
                {{ statusLabel(company.verification_status) }}
              </span>
            </td>

            <!-- Submitted -->
            <td class="px-5 py-4">
              <div class="flex items-center gap-1.5 text-sm text-slate-600">
                <CalendarDays :size="15" class="text-slate-400" />
                {{ formatDate(company.created_at) }}
              </div>
            </td>

            <!-- Actions -->
            <td class="px-5 py-4">
              <div class="flex items-center justify-end gap-1">
                <button
                  type="button"
                  @click="emit('openDetails', company)"
                  title="View company details"
                  class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                >
                  <Eye :size="18" />
                </button>

                <button
                  v-if="company.verification_status !== 'verified'"
                  type="button"
                  @click="emit('openApproval', company, 'verified')"
                  title="Approve company"
                  class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50"
                >
                  <CheckCircle2 :size="18" />
                </button>

                <button
                  v-if="company.verification_status !== 'rejected'"
                  type="button"
                  @click="emit('openApproval', company, 'rejected')"
                  title="Reject company"
                  class="rounded-lg p-2 text-red-500 transition hover:bg-red-50"
                >
                  <XCircle :size="18" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <BasePagination
      v-if="!loading && totalCompanies > 0"
      :current-page="currentPage"
      :last-page="lastPage"
      :total="totalCompanies"
      :per-page="perPage"
      :loading="loading"
      @change="emit('goToPage', $event)"
      @update:per-page="emit('update:per-page', $event)"
    />
  </div>
</template>
