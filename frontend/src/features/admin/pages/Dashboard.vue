<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { Component } from "vue";
import { RouterLink } from "vue-router";
import { Line, Doughnut, Pie, Bar } from "vue-chartjs";
import { formatDate } from "@/utils/formatDate";
import { useDashboardCharts } from "@/features/admin/composables/useDashboardCharts";
import { adminRepository } from "@/features/admin/repositories/adminRepository";

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
  BarElement,
} from "chart.js";

import {
  Users,
  Building2,
  BriefcaseBusiness,
  FileText,
  ChartNoAxesCombined,
  Activity,
  Clock3,
  ShieldCheck,
  PieChart,
  BarChart3,
} from "lucide-vue-next";

// Register Chart.js components
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement,
  BarElement,
);

// Types
interface StatCard {
  title: string;
  value: string;
  description: string;
  icon: Component;
  iconStyle: string;
  to?: string;
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

interface JobsByCategory {
  category: string;
  total: number;
}

interface MonthlyJob {
  date: string;
  month: string;
  total: number;
}

// Dashboard States
const dashboardStats = ref<DashboardStats>({
  total_users: 0,
  total_companies: 0,
  total_jobs: 0,
  total_applications: 0,
});

const userGrowth = ref<UserGrowth[]>([]);
const jobStatus = ref<JobStatus[]>([]);
const recentActivities = ref<RecentActivity[]>([]);
const jobsByCategory = ref<JobsByCategory[]>([]);
const monthlyJobs = ref<MonthlyJob[]>([]);

const isLoading = ref(false);
const errorMessage = ref("");

// Chart Composable
const {
  userGrowthChartData,
  userGrowthChartOptions,
  jobStatusTotal,
  jobStatusColors,
  jobStatusChartData,
  jobStatusChartOptions,
  getJobStatusPercentage,
  jobsByCategoryChartData,
  jobsByCategoryChartOptions,
  monthlyJobsChartData,
  monthlyJobsChartOptions,
} = useDashboardCharts(userGrowth, jobStatus, jobsByCategory, monthlyJobs);

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
    const responseData = response.data ?? response;
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
    jobsByCategory.value = data.jobs_by_category ?? [];
    monthlyJobs.value = data.monthly_jobs ?? [];
  } catch (error) {
    console.error("Failed to fetch dashboard data:", error);
    errorMessage.value = "Failed to load dashboard data.";
  } finally {
    isLoading.value = false;
  }
}

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
        <template v-for="stat in stats" :key="stat.title">
          <RouterLink
            v-if="stat.to"
            :to="stat.to"
            class="block rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50/30 hover:shadow-md"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600">
                  {{ stat.title }}
                </p>

                <p
                  class="mt-3 text-3xl font-bold tracking-tight text-slate-800"
                >
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
          </RouterLink>

          <article
            v-else
            class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600">
                  {{ stat.title }}
                </p>

                <p
                  class="mt-3 text-3xl font-bold tracking-tight text-slate-800"
                >
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
        </template>
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
              Daily registration activity over the last 7 days
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
    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
      <!-- Recent Activities -->

      <!-- Recent Activities -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="font-semibold text-slate-800">Recent Activities</h2>
            <p class="mt-1 text-xs text-slate-400">
              Latest 5 activities across the platform
            </p>
          </div>

          <RouterLink
            to="/admin/activity-logs"
            class="shrink-0 text-sm font-semibold text-blue-600 transition hover:text-blue-700"
          >
            View All
          </RouterLink>
        </div>

        <div class="mt-5 space-y-4">
          <div
            v-for="activity in recentActivities.slice(0, 5)"
            :key="activity.id"
            class="flex items-start gap-3 border-b border-slate-100 pb-4 last:border-0 last:pb-0"
          >
            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"
            >
              <Activity :size="17" />
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <p class="text-sm font-semibold text-slate-800">
                  {{ activity.action }}
                </p>

                <span class="text-xs text-slate-400">
                  {{ formatDate(activity.created_at) }}
                </span>
              </div>

              <p class="mt-1 text-sm text-slate-600">
                {{ activity.description || "No description available." }}
              </p>

              <p class="mt-1 text-xs text-slate-400">
                By {{ activity.actor || "System" }}
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

      <!-- Jobs By Category Pie Chart -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-slate-800">Jobs By Category</h2>
            <p class="mt-1 text-xs text-slate-400">
              Job distribution by category
            </p>
          </div>

          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600"
          >
            <PieChart :size="20" />
          </div>
        </div>

        <div class="mt-5 h-64 w-full">
          <Pie
            v-if="jobsByCategory.length > 0"
            :data="jobsByCategoryChartData"
            :options="jobsByCategoryChartOptions"
          />

          <div
            v-else-if="!isLoading"
            class="flex h-full items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-400"
          >
            No category data available.
          </div>

          <div
            v-else
            class="flex h-full items-center justify-center text-sm text-slate-400"
          >
            Loading chart...
          </div>
        </div>
      </article>

      <!-- Monthly Jobs Bar Chart -->
      <article
        class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-slate-800">Monthly Job Posts</h2>
            <p class="mt-1 text-xs text-slate-400">
              Jobs posted over the last 6 months
            </p>
          </div>

          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
          >
            <BarChart3 :size="20" />
          </div>
        </div>

        <div class="mt-5 h-64 w-full">
          <Bar
            v-if="monthlyJobs.length > 0"
            :data="monthlyJobsChartData"
            :options="monthlyJobsChartOptions"
          />

          <div
            v-else-if="!isLoading"
            class="flex h-full items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-400"
          >
            No monthly job data available.
          </div>

          <div
            v-else
            class="flex h-full items-center justify-center text-sm text-slate-400"
          >
            Loading chart...
          </div>
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
