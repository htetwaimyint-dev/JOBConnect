<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { Component } from "vue";
import { RouterLink } from "vue-router";
import { Line, Doughnut } from "vue-chartjs";
import { formatDate } from "@/utils/formatDate";

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement,
} from "chart.js";

import {
  Users,
  Building2,
  BriefcaseBusiness,
  FileText,
  ClipboardCheck,
  Tags,
  ChartNoAxesCombined,
  Activity,
  ArrowUpRight,
  Clock3,
  ShieldCheck,
} from "lucide-vue-next";

import { adminRepository } from "@/features/admin/repositories/adminRepository";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement,
);

// Types
interface StatCard {
  title: string;
  value: string;
  description: string;
  icon: Component;
  iconStyle: string;
}

interface QuickAction {
  title: string;
  description: string;
  to: string;
  icon: Component;
}

interface DashboardStats {
  total_users: number;
  total_companies: number;
  total_jobs: number;
  total_applications: number;
}

interface UserGrowth {
  date: string;
  day: string;
  job_seekers: number;
  employers: number;
}

interface JobStatus {
  status: string;
  total: number;
}

interface RecentActivity {
  id: number;
  actor: string;
  action: string;
  description: string | null;
  created_at: string | null;
}

// Dashboard State
const dashboardStats = ref<DashboardStats>({
  total_users: 0,
  total_companies: 0,
  total_jobs: 0,
  total_applications: 0,
});

const userGrowth = ref<UserGrowth[]>([]);
const jobStatus = ref<JobStatus[]>([]);
const recentActivities = ref<RecentActivity[]>([]);

const isLoading = ref(false);
const errorMessage = ref("");

// Statistics Cards
const stats = computed<StatCard[]>(() => [
  {
    title: "Total Users",
    value: String(dashboardStats.value.total_users),
    description: "Job seekers and employers",
    icon: Users,
    iconStyle: "bg-blue-50 text-blue-600",
  },
  {
    title: "Total Companies",
    value: String(dashboardStats.value.total_companies),
    description: "Registered companies",
    icon: Building2,
    iconStyle: "bg-violet-50 text-violet-600",
  },
  {
    title: "Total Jobs",
    value: String(dashboardStats.value.total_jobs),
    description: "All posted job listings",
    icon: BriefcaseBusiness,
    iconStyle: "bg-emerald-50 text-emerald-600",
  },
  {
    title: "Total Applications",
    value: String(dashboardStats.value.total_applications),
    description: "Applications received",
    icon: FileText,
    iconStyle: "bg-amber-50 text-amber-600",
  },
]);

// Fetch Dashboard API
async function fetchDashboard() {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await adminRepository.getDashboard();

    // Support both Axios response and already-unwrapped repository data.
    const responseData = response.data ?? response;

    // Laravel response may contain an additional "data" wrapper.
    const data = responseData.data ?? responseData;

    dashboardStats.value = {
      total_users: Number(data.stats?.total_users ?? 0),
      total_companies: Number(data.stats?.total_companies ?? 0),
      total_jobs: Number(data.stats?.total_jobs ?? 0),
      total_applications: Number(data.stats?.total_applications ?? 0),
    };

    userGrowth.value = data.user_growth ?? [];
    jobStatus.value = data.job_status ?? [];
    recentActivities.value = data.recent_activities ?? [];

    console.log("Dashboard Data:", data);
  } catch (error) {
    console.error("Failed to fetch dashboard data:", error);
    errorMessage.value = "Failed to load dashboard data.";
  } finally {
    isLoading.value = false;
  }
}

// User Growth Line Chart
const userGrowthChartData = computed(() => ({
  labels: userGrowth.value.map((item) => item.day),
  datasets: [
    {
      label: "Job Seekers",
      data: userGrowth.value.map((item) => item.job_seekers),
      borderColor: "#3B82F6",
      backgroundColor: "rgba(59, 130, 246, 0.1)",
      pointBackgroundColor: "#3B82F6",
      pointBorderColor: "#FFFFFF",
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      borderWidth: 2.5,
      tension: 0.4,
    },
    {
      label: "Employers",
      data: userGrowth.value.map((item) => item.employers),
      borderColor: "#10B981",
      backgroundColor: "rgba(16, 185, 129, 0.1)",
      pointBackgroundColor: "#10B981",
      pointBorderColor: "#FFFFFF",
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      borderWidth: 2.5,
      tension: 0.4,
    },
  ],
}));

const userGrowthChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: "top" as const,
      align: "end" as const,
      labels: {
        usePointStyle: true,
        pointStyle: "circle" as const,
        boxWidth: 8,
        padding: 18,
        color: "#64748B",
        font: {
          size: 12,
        },
      },
    },
    tooltip: {
      mode: "index" as const,
      intersect: false,
    },
  },
  interaction: {
    mode: "index" as const,
    intersect: false,
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
      ticks: {
        color: "#94A3B8",
        font: {
          size: 11,
        },
      },
    },
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0,
        color: "#94A3B8",
        font: {
          size: 11,
        },
      },
      grid: {
        color: "#E2E8F0",
      },
    },
  },
};

// Job Status Doughnut Chart
const jobStatusTotal = computed(() =>
  jobStatus.value.reduce((total, item) => total + Number(item.total), 0),
);

const jobStatusColors: Record<string, string> = {
  draft: "#94A3B8",
  pending: "#F59E0B",
  published: "#1B0981",
  approved: "#10B981",
  rejected: "#EF4444",
  closed: "#A855F7",
};

const jobStatusChartData = computed(() => ({
  labels: jobStatus.value.map((item) => item.status),
  datasets: [
    {
      data: jobStatus.value.map((item) => Number(item.total)),
      backgroundColor: jobStatus.value.map(
        (item) => jobStatusColors[item.status.toLowerCase()] ?? "#64748B",
      ),
      borderWidth: 0,
      hoverOffset: 5,
    },
  ],
}));

const jobStatusChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: "65%",
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      enabled: true,
    },
  },
};

// Job Status Percentage
function getJobStatusPercentage(total: number): number {
  if (jobStatusTotal.value === 0) return 0;

  return Math.round((total / jobStatusTotal.value) * 100);
}

// Quick Actions
const quickActions: QuickAction[] = [
  {
    title: "Company Approvals",
    description: "Review company verification requests",
    to: "/admin/company-approvals",
    icon: ClipboardCheck,
  },
  {
    title: "Job Approvals",
    description: "Review pending job posts",
    to: "/admin/job-approvals",
    icon: BriefcaseBusiness,
  },
  {
    title: "Manage Categories",
    description: "Manage job categories",
    to: "/admin/categories",
    icon: Tags,
  },
  {
    title: "View Reports",
    description: "Explore platform analytics",
    to: "/admin/reports",
    icon: ChartNoAxesCombined,
  },
];

onMounted(fetchDashboard);
</script>

<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <section
      class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
    >
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-800">
          Dashboard Overview
        </h1>
        <p class="mt-1 text-sm text-slate-500">
          Welcome back! Here's what's happening with JobConnect AI.
        </p>
      </div>

      <div
        class="flex w-fit items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600"
      >
        <Activity :size="17" class="text-blue-600" />
        <span>Platform Overview</span>
      </div>
    </section>

    <!-- API Error -->
    <div
      v-if="errorMessage"
      class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-600"
    >
      {{ errorMessage }}
      <button class="ml-2 font-semibold underline" @click="fetchDashboard">
        Retry
      </button>
    </div>

    <!-- Statistics -->
    <section>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">
          Platform Statistics
        </h2>
        <span class="text-xs text-slate-400">
          {{ isLoading ? "Loading..." : "Overview" }}
        </span>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article
          v-for="stat in stats"
          :key="stat.title"
          class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">
                {{ stat.title }}
              </p>
              <p class="mt-3 text-3xl font-bold tracking-tight text-slate-800">
                {{ isLoading ? "..." : stat.value }}
              </p>
            </div>

            <div
              class="flex h-11 w-11 items-center justify-center rounded-xl"
              :class="stat.iconStyle"
            >
              <component :is="stat.icon" :size="22" />
            </div>
          </div>

          <p class="mt-4 text-xs text-slate-500">
            {{ stat.description }}
          </p>
        </article>
      </div>
    </section>

    <!-- Analytics -->
    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
      <!-- User Growth -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2"
      >
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="font-semibold text-slate-800">User Growth Overview</h2>
            <p class="mt-1 text-xs text-slate-400">
              Monthly registration activity over the last 6 months
            </p>
          </div>

          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
          >
            <ChartNoAxesCombined :size="20" />
          </div>
        </div>

        <div class="mt-6 h-72 w-full">
          <Line
            v-if="userGrowth.length > 0"
            :data="userGrowthChartData"
            :options="userGrowthChartOptions"
          />

          <div
            v-else-if="!isLoading"
            class="flex h-full items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-400"
          >
            No user growth data available.
          </div>

          <div
            v-else
            class="flex h-full items-center justify-center text-sm text-slate-400"
          >
            Loading chart...
          </div>
        </div>
      </article>

      <!-- Job Status -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-slate-800">Job Status</h2>
            <p class="mt-1 text-xs text-slate-400">Overview of job listings</p>
          </div>

          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"
          >
            <BriefcaseBusiness :size="20" />
          </div>
        </div>

        <div class="relative mx-auto mt-5 h-52 w-full max-w-[230px]">
          <Doughnut
            v-if="jobStatus.length > 0"
            :data="jobStatusChartData"
            :options="jobStatusChartOptions"
          />

          <div
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center"
          >
            <span class="text-3xl font-bold text-slate-800">
              {{ jobStatusTotal }}
            </span>
            <span class="mt-1 text-xs text-slate-400">Total Jobs</span>
          </div>

          <div
            v-if="!isLoading && jobStatus.length === 0"
            class="absolute inset-0 flex items-center justify-center rounded-full text-xs text-slate-400"
          >
            No data
          </div>
        </div>

        <div class="mt-5 space-y-3">
          <div
            v-for="item in jobStatus"
            :key="item.status"
            class="flex items-center justify-between gap-3"
          >
            <div class="flex min-w-0 items-center gap-2">
              <span
                class="h-2.5 w-2.5 shrink-0 rounded-full"
                :style="{
                  backgroundColor:
                    jobStatusColors[item.status.toLowerCase()] ?? '#64748B',
                }"
              ></span>

              <span class="truncate text-sm capitalize text-slate-600">
                {{ item.status }}
              </span>
            </div>

            <div class="flex shrink-0 items-center gap-2">
              <span class="text-sm font-semibold text-slate-800">
                {{ item.total }}
              </span>
              <span class="w-10 text-right text-xs text-slate-400">
                {{ getJobStatusPercentage(item.total) }}%
              </span>
            </div>
          </div>

          <p
            v-if="!isLoading && jobStatus.length === 0"
            class="py-3 text-center text-sm text-slate-400"
          >
            No job statistics available.
          </p>
        </div>
      </article>
    </section>

    <!-- Bottom Section -->
    <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
      <!-- Recent Activities -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-slate-800">Recent Activities</h2>
            <p class="mt-1 text-xs text-slate-400">
              Latest activities across the platform
            </p>
          </div>

          <Clock3 :size="19" class="text-slate-400" />
        </div>

        <div class="mt-5 space-y-4">
          <div
            v-for="activity in recentActivities"
            :key="activity.id"
            class="flex items-start gap-3 border-b border-slate-100 pb-4 last:border-0"
          >
            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"
            >
              <Activity :size="17" />
            </div>

            <div class="min-w-0 flex-1">
              <div class="text-sm font-semibold text-slate-800">
                {{ activity.actor }}

                <span class="mt-1 text-xs text-slate-400">
                  ({{ formatDate(activity.created_at) }})
                </span>
              </div>

              <p class="mt-1 text-sm text-slate-600">
                {{ activity.description || "No description available." }}
              </p>
            </div>
          </div>

          <p
            v-if="!isLoading && recentActivities.length === 0"
            class="py-8 text-center text-sm text-slate-400"
          >
            No recent activities available.
          </p>

          <p v-if="isLoading" class="py-8 text-center text-sm text-slate-400">
            Loading activities...
          </p>
        </div>
      </article>

      <!-- Quick Actions -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-slate-800">Quick Actions</h2>
            <p class="mt-1 text-xs text-slate-400">
              Frequently used admin pages
            </p>
          </div>

          <ArrowUpRight :size="19" class="text-slate-400" />
        </div>

        <div class="mt-5 space-y-2">
          <RouterLink
            v-for="action in quickActions"
            :key="action.to"
            :to="action.to"
            class="group flex items-center gap-3 rounded-lg border border-transparent p-3 transition hover:border-blue-100 hover:bg-blue-50/70"
          >
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition group-hover:bg-blue-100 group-hover:text-blue-600"
            >
              <component :is="action.icon" :size="19" />
            </div>

            <div class="min-w-0 flex-1">
              <p
                class="text-sm font-medium text-slate-700 transition group-hover:text-blue-700"
              >
                {{ action.title }}
              </p>
              <p class="mt-0.5 text-xs text-slate-400">
                {{ action.description }}
              </p>
            </div>

            <ArrowUpRight
              :size="16"
              class="shrink-0 text-slate-300 transition group-hover:text-blue-600"
            />
          </RouterLink>
        </div>
      </article>
    </section>

    <!-- System Status -->
    <section
      class="flex flex-col gap-3 rounded-xl border border-blue-100 bg-blue-50/60 p-4 sm:flex-row sm:items-center"
    >
      <div
        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600"
      >
        <ShieldCheck :size="21" />
      </div>

      <div>
        <p class="text-sm font-semibold text-slate-700">
          JobConnect AI Administration
        </p>
        <p class="mt-1 text-xs text-slate-500">
          Manage users, companies, job approvals, and platform activities from
          your admin dashboard.
        </p>
      </div>
    </section>
  </div>
</template>
