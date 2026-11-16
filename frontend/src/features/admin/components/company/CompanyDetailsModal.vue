<script setup lang="ts">
import {
  Building2,
  X,
  BriefcaseBusiness,
  CalendarDays,
  Users,
  MapPin,
  Mail,
  Phone,
  Globe,
  Image as ImageIcon,
  CheckCircle2,
  XCircle,
} from "lucide-vue-next";

import type { Company } from "../../types/company-approval.types";

defineProps<{
  company: Company | null;
  statusClass: (status: Company["verification_status"]) => string;
  statusDotClass: (status: Company["verification_status"]) => string;
  statusLabel: (status: Company["verification_status"]) => string;
  formatDate: (date: string | null) => string;
}>();

const emit = defineEmits<{
  close: [];
  approve: [company: Company];
  reject: [company: Company];
}>();
</script>

<template>
  <Teleport to="body">
    <div
      v-if="company"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
      @click.self="emit('close')"
    >
      <div
        class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
      >
        <!-- Header -->
        <div
          class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4"
        >
          <div>
            <h2 class="text-lg font-bold text-slate-900">Company Details</h2>
            <p class="mt-1 text-xs text-slate-500">
              Complete company profile information
            </p>
          </div>

          <button
            type="button"
            @click="emit('close')"
            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
          >
            <X :size="20" />
          </button>
        </div>

        <div class="space-y-6 p-6">
          <!-- Cover -->
          <div
            v-if="company.cover_image"
            class="h-40 overflow-hidden rounded-xl bg-slate-100"
          >
            <img
              :src="company.cover_image"
              alt="Company cover"
              class="h-full w-full object-cover"
            />
          </div>

          <!-- Company Identity -->
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <img
              v-if="company.logo"
              :src="company.logo"
              :alt="company.name"
              class="h-20 w-20 rounded-2xl border border-slate-200 object-cover"
            />

            <div
              v-else
              class="flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
            >
              <Building2 :size="32" />
            </div>

            <div class="min-w-0 flex-1">
              <h3 class="text-xl font-bold text-slate-900">
                {{ company.name }}
              </h3>

              <p class="mt-1 text-sm text-slate-500">
                {{ company.tagline || company.industry || "Company profile" }}
              </p>

              <span
                class="mt-2 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                :class="statusClass(company.verification_status)"
              >
                <span
                  class="h-1.5 w-1.5 rounded-full"
                  :class="statusDotClass(company.verification_status)"
                ></span>
                {{ statusLabel(company.verification_status) }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div>
            <h4 class="mb-2 font-semibold text-slate-900">About Company</h4>
            <p class="whitespace-pre-line text-sm leading-7 text-slate-600">
              {{ company.description || "No company description provided." }}
            </p>
          </div>

          <!-- Company Information -->
          <div>
            <h4 class="mb-3 font-semibold text-slate-900">
              Company Information
            </h4>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <BriefcaseBusiness :size="15" />
                  Industry
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ company.industry || "—" }}
                </p>
              </div>

              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <CalendarDays :size="15" />
                  Founded Year
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ company.founded_year || "—" }}
                </p>
              </div>

              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <Users :size="15" />
                  Employee Count
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ company.employee_count || "—" }}
                </p>
              </div>

              <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <MapPin :size="15" />
                  Location
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-800">
                  {{ company.city || "—" }}
                </p>
                <p class="mt-1 text-xs text-slate-500">
                  {{ company.address || "" }}
                </p>
              </div>
            </div>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="mb-3 font-semibold text-slate-900">
              Contact Information
            </h4>

            <div class="space-y-3">
              <div class="flex items-center gap-3 text-sm text-slate-600">
                <Mail :size="17" class="text-slate-400" />
                {{ company.company_email || "No company email" }}
              </div>

              <div class="flex items-center gap-3 text-sm text-slate-600">
                <Phone :size="17" class="text-slate-400" />
                {{ company.phone || "No phone number" }}
              </div>

              <div class="flex items-center gap-3 text-sm text-slate-600">
                <Globe :size="17" class="text-slate-400" />

                <a
                  v-if="company.website"
                  :href="company.website"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="break-all text-blue-600 hover:underline"
                >
                  {{ company.website }}
                </a>

                <span v-else>No website</span>
              </div>
            </div>
          </div>

          <!-- Employer -->
          <div>
            <h4 class="mb-3 font-semibold text-slate-900">
              Employer Information
            </h4>

            <div class="rounded-xl border border-slate-200 p-4">
              <p class="font-semibold text-slate-800">
                {{ company.employer?.name || "Unknown Employer" }}
              </p>
              <p class="mt-1 text-sm text-slate-500">
                {{ company.employer?.email || "No employer email" }}
              </p>
            </div>
          </div>

          <!-- Gallery -->
          <div v-if="company.gallery?.length">
            <h4
              class="mb-3 flex items-center gap-2 font-semibold text-slate-900"
            >
              <ImageIcon :size="18" />
              Company Gallery
            </h4>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
              <img
                v-for="(image, index) in company.gallery"
                :key="index"
                :src="image"
                :alt="`${company.name} gallery ${index + 1}`"
                class="h-32 w-full rounded-xl border border-slate-200 object-cover"
              />
            </div>
          </div>

          <!-- Rejection Reason -->
          <div
            v-if="company.rejection_reason"
            class="rounded-xl border border-red-200 bg-red-50 p-4"
          >
            <h4 class="font-semibold text-red-800">Rejection Reason</h4>
            <p class="mt-2 whitespace-pre-line text-sm text-red-700">
              {{ company.rejection_reason }}
            </p>
          </div>

          <!-- Dates -->
          <div
            class="grid grid-cols-1 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2"
          >
            <div>
              <p class="text-xs text-slate-400">Submitted Date</p>
              <p class="mt-1 text-sm font-medium text-slate-700">
                {{ formatDate(company.created_at) }}
              </p>
            </div>

            <div>
              <p class="text-xs text-slate-400">Verified Date</p>
              <p class="mt-1 text-sm font-medium text-slate-700">
                {{ formatDate(company.verified_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="flex flex-wrap justify-end gap-3 border-t border-slate-100 px-6 py-4"
        >
          <button
            type="button"
            @click="emit('close')"
            class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
          >
            Close
          </button>

          <button
            v-if="company.verification_status !== 'verified'"
            type="button"
            @click="emit('approve', company)"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
          >
            <CheckCircle2 :size="17" />
            Approve
          </button>

          <button
            v-if="company.verification_status !== 'rejected'"
            type="button"
            @click="emit('reject', company)"
            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
          >
            <XCircle :size="17" />
            Reject
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
