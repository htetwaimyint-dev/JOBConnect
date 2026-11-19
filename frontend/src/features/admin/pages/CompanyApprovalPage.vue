<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

import { RefreshCw, AlertCircle, X } from "lucide-vue-next";

import CompanyTable from "../components/company/CompanyTable.vue";
import CompanyDetailsModal from "../components/company/CompanyDetailsModal.vue";
import CompanyApprovalModal from "../components/company/CompanyApprovalModal.vue";

import companyApprovalRepository from "../repositories/companyApprovalRepository";
import { formatDate } from "@/utils/formatDate.ts";

import type {
  Company,
  CompanyStatusFilter,
  CompanyVerificationStatus,
} from "../types/company-approval.types";

// State
const companies = ref<Company[]>([]);

const loading = ref(false);
const submitting = ref(false);

const searchQuery = ref("");

const currentPage = ref(1);
const lastPage = ref(1);
const totalCompanies = ref(0);

const activeFilter = ref<CompanyStatusFilter>("all");

const errorMessage = ref("");

// Request control
let searchTimeout: ReturnType<typeof setTimeout>;
let requestId = 0;

// Details Modal
const selectedCompany = ref<Company | null>(null);
const showDetailsModal = ref(false);

// Approval Modal
const approvalCompany = ref<Company | null>(null);
const approvalAction = ref<"verified" | "rejected" | null>(null);
const rejectionReason = ref("");

// Filters
const filters: {
  label: string;
  value: CompanyStatusFilter;
}[] = [
  { label: "All Companies", value: "all" },
  { label: "Pending", value: "pending" },
  { label: "Approved", value: "verified" },
  { label: "Rejected", value: "rejected" },
];

// Fetch Companies
const fetchCompanies = async (page = 1) => {
  const currentRequestId = ++requestId;

  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await companyApprovalRepository.getPendingCompanies({
      status: activeFilter.value,
      search: searchQuery.value.trim(),
      page,
      per_page: perPage.value,
    });

    // Ignore outdated responses
    if (currentRequestId !== requestId) return;

    companies.value = response.data;
    currentPage.value = response.meta.current_page;
    lastPage.value = response.meta.last_page;
    totalCompanies.value = response.meta.total;
  } catch (error) {
    if (currentRequestId !== requestId) return;

    console.error("Failed to fetch companies:", error);
    errorMessage.value = "Failed to load companies.";
  } finally {
    if (currentRequestId === requestId) {
      loading.value = false;
    }
  }
};

// Backend Search with Debounce
watch(searchQuery, () => {
  clearTimeout(searchTimeout);

  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchCompanies(1);
  }, 500);
});

// Filter
const changeFilter = (status: CompanyStatusFilter) => {
  if (activeFilter.value === status) return;

  activeFilter.value = status;
  searchQuery.value = "";
  currentPage.value = 1;

  clearTimeout(searchTimeout);
  fetchCompanies(1);
};

// Pagination
const perPage = ref(5);
const goToPage = (page: number) => {
  if (page < 1 || page > lastPage.value || loading.value) return;

  fetchCompanies(page);
};

const changePerPage = (value: number) => {
  perPage.value = value;
  currentPage.value = 1;
  fetchCompanies(1);
};
// Refresh
const refreshCompanies = () => {
  if (loading.value) return;

  clearTimeout(searchTimeout);
  fetchCompanies(currentPage.value);
};

// Status Helpers
const statusLabel = (status: CompanyVerificationStatus) => {
  const labels: Record<CompanyVerificationStatus, string> = {
    pending: "Pending",
    verified: "Approved",
    rejected: "Rejected",
  };

  return labels[status];
};

const statusClass = (status: CompanyVerificationStatus) => {
  const classes: Record<CompanyVerificationStatus, string> = {
    pending: "bg-amber-50 text-amber-700 ring-amber-200",
    verified: "bg-emerald-50 text-emerald-700 ring-emerald-200",
    rejected: "bg-red-50 text-red-700 ring-red-200",
  };

  return classes[status];
};

const statusDotClass = (status: CompanyVerificationStatus) => {
  const classes: Record<CompanyVerificationStatus, string> = {
    pending: "bg-amber-500",
    verified: "bg-emerald-500",
    rejected: "bg-red-500",
  };

  return classes[status];
};

// Company Details
const openDetails = (company: Company) => {
  selectedCompany.value = company;
  showDetailsModal.value = true;
};

const closeDetails = () => {
  showDetailsModal.value = false;
  selectedCompany.value = null;
};

// Approval Dialog
const openApprovalDialog = (
  company: Company,
  action: "verified" | "rejected",
) => {
  approvalCompany.value = company;
  approvalAction.value = action;
  rejectionReason.value = "";
};

const closeApprovalDialog = () => {
  if (submitting.value) return;

  approvalCompany.value = null;
  approvalAction.value = null;
  rejectionReason.value = "";
};

// Confirm Approval / Rejection
const confirmApproval = async () => {
  if (!approvalCompany.value || !approvalAction.value) return;

  if (approvalAction.value === "rejected" && !rejectionReason.value.trim()) {
    errorMessage.value = "Please provide a rejection reason.";
    return;
  }

  submitting.value = true;
  errorMessage.value = "";

  try {
    await companyApprovalRepository.updateCompanyApproval(
      approvalCompany.value.id,
      {
        verification_status: approvalAction.value,

        ...(approvalAction.value === "rejected"
          ? {
              rejection_reason: rejectionReason.value.trim(),
            }
          : {}),
      },
    );

    approvalCompany.value = null;
    approvalAction.value = null;
    rejectionReason.value = "";

    const nextPage =
      companies.value.length === 1 && currentPage.value > 1
        ? currentPage.value - 1
        : currentPage.value;

    await fetchCompanies(nextPage);
  } catch (error) {
    console.error("Failed to update company:", error);
    errorMessage.value = "Unable to update company status. Please try again.";
  } finally {
    submitting.value = false;
  }
};

// Summary Label
const totalLabel = computed(() =>
  activeFilter.value === "all"
    ? "Total Companies"
    : `${statusLabel(activeFilter.value)} Companies`,
);

// Initial Load
onMounted(() => {
  fetchCompanies(1);
});

// Cleanup
onUnmounted(() => {
  clearTimeout(searchTimeout);
  requestId++;
});
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div
      class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="min-w-0">
        <h1 class="text-2xl font-bold text-slate-800">Company Management</h1>

        <p class="mt-1 text-sm text-slate-500">
          Manage and review company registrations
        </p>
      </div>

      <button
        type="button"
        @click="refreshCompanies"
        :disabled="loading"
        class="mr-auto flex items-center justify-center gap-2 self-end rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-50 sm:self-auto"
      >
        <RefreshCw :size="14" :class="{ 'animate-spin': loading }" />
        Refresh
      </button>
    </div>

    <!-- Error Message -->
    <div
      v-if="errorMessage"
      class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
    >
      <AlertCircle :size="18" class="mt-0.5 shrink-0" />

      <p class="flex-1">
        {{ errorMessage }}
      </p>

      <button
        type="button"
        @click="errorMessage = ''"
        class="text-red-500 hover:text-red-700"
      >
        <X :size="18" />
      </button>
    </div>

    <!-- Company Table -->
    <CompanyTable
      :companies="companies"
      :filtered-companies="companies"
      :filters="filters"
      :loading="loading"
      v-model:search-query="searchQuery"
      :active-filter="activeFilter"
      :current-page="currentPage"
      :last-page="lastPage"
      :total-companies="totalCompanies"
      :per-page="perPage"
      :status-class="statusClass"
      :status-dot-class="statusDotClass"
      :status-label="statusLabel"
      :format-date="formatDate"
      @update:per-page="changePerPage"
      @change-filter="changeFilter"
      @go-to-page="goToPage"
      @open-details="openDetails"
      @open-approval="openApprovalDialog"
    />

    <!-- Company Details Modal -->
    <CompanyDetailsModal
      :company="showDetailsModal ? selectedCompany : null"
      :status-class="statusClass"
      :status-dot-class="statusDotClass"
      :status-label="statusLabel"
      :format-date="formatDate"
      @close="closeDetails"
      @approve="
        (company) => {
          openApprovalDialog(company, 'verified');
          closeDetails();
        }
      "
      @reject="
        (company) => {
          openApprovalDialog(company, 'rejected');
          closeDetails();
        }
      "
    />

    <!-- Approval Modal -->
    <CompanyApprovalModal
      :company="approvalCompany"
      :action="approvalAction"
      v-model:rejection-reason="rejectionReason"
      :submitting="submitting"
      @close="closeApprovalDialog"
      @confirm="confirmApproval"
    />
  </div>
</template>
