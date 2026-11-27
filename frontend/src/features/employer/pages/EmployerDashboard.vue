<script setup lang="ts">
import { computed, ref } from "vue";
import { RouterLink } from "vue-router";

import {
  Briefcase,
  Users,
  Send,
  CalendarDays,
  ArrowRight,
  Clock,
} from "lucide-vue-next";

import { useEmployerJobStats } from "@/features/employer/composables/useEmployerJobStats";
import { useEmployerApplicantStats } from "@/features/employer/composables/useEmployerApplicantStats";
import { useEmployerOfferStats } from "@/features/employer/composables/useEmployerOfferStats";
import { useEmployerInterviewDashboard } from "@/features/employer/composables/useEmployerInterviewDashboard";
import { useEmployerYearlyApplicantStats } from "@/features/employer/composables/useEmployerYearlyApplicantStats";

// ==================================
// 1. JOB STATISTICS
// ==================================

const {
  data: jobStatsData,
  isPending,
  isError,
  error,
  refetch,
} = useEmployerJobStats();

const jobStats = computed(
  () =>
    jobStatsData.value ?? {
      total: 0,
      draft: 0,
      pending: 0,
      published: 0,
      rejected: 0,
      closed: 0,
    },
);

// ==================================
// 2. APPLICANT STATISTICS
// ==================================

const { data: applicantStatsData } = useEmployerApplicantStats();

const applicantStats = computed(
  () =>
    applicantStatsData.value ?? {
      total: 0,
      pending: 0,
      reviewed: 0,
      shortlisted: 0,
      interview: 0,
      accepted: 0,
      rejected: 0,
      withdrawn: 0,
      date_range: {
        from: "",
        to: "",
      },
    },
);

// ==================================
// 3. DIRECT OFFER STATISTICS
// ==================================

const { data: offerStatsData } = useEmployerOfferStats();

const offerStats = computed(
  () =>
    offerStatsData.value ?? {
      total: 0,
      pending: 0,
      accepted: 0,
      rejected: 0,
      expired: 0,
      withdrawn: 0,
      date_range: {
        from: "",
        to: "",
      },
    },
);

// ==================================
// 4. INTERVIEW DASHBOARD
// ==================================

const { data: interviewData } = useEmployerInterviewDashboard();

const todayInterviews = computed(
  () => interviewData.value?.today_interviews ?? [],
);

const upcomingInterviews = computed(
  () => interviewData.value?.upcoming_interviews ?? [],
);

// upcoming_interviews already includes today's interviews.
// Do not add todayInterviews.length to this total.
const scheduleStats = computed(() => ({
  total: upcomingInterviews.value.length,
  today: todayInterviews.value.length,
}));

// ==================================
// 5. INTERVIEW DISPLAY DATA
// ==================================

const APP_TIME_ZONE = "Asia/Yangon";

const formatInterviewMonth = (date: string) =>
  new Date(date).toLocaleDateString("en-US", {
    month: "short",
    timeZone: APP_TIME_ZONE,
  });

const formatInterviewDay = (date: string) =>
  new Date(date).toLocaleDateString("en-US", {
    day: "2-digit",
    timeZone: APP_TIME_ZONE,
  });

const formatInterviewTime = (date: string) =>
  new Date(date).toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
    timeZone: APP_TIME_ZONE,
  });

const upcomingInterviewItems = computed(() =>
  upcomingInterviews.value
    // 1. Remove interviews without scheduled date
    .filter((interview: any) => {
      if (!interview.scheduled_at) return false;

      // Only future interviews
      return new Date(interview.scheduled_at).getTime() > Date.now();
    })

    // 2. Sort by nearest interview date
    .sort(
      (a: any, b: any) =>
        new Date(a.scheduled_at).getTime() - new Date(b.scheduled_at).getTime(),
    )

    // 3. Show only 5 upcoming interviews
    .slice(0, 5)

    // 4. Format interview data for template
    .map((interview: any) => {
      const scheduledAt = interview.scheduled_at;

      const interviewType =
        interview.interview_type === "online"
          ? "Online"
          : interview.interview_type === "onsite"
            ? "Onsite"
            : (interview.interview_type ?? "Interview");

      return {
        id: interview.id,

        month: formatInterviewMonth(scheduledAt),

        day: formatInterviewDay(scheduledAt),

        candidate:
          interview.application?.applicant_name ??
          interview.application?.job_seeker?.name ??
          "Applicant",

        role: interview.application?.job?.title ?? "Job title unavailable",

        level: interviewType,

        tagColor:
          interview.interview_type === "online"
            ? "bg-blue-100 text-blue-700"
            : "bg-orange-100 text-orange-700",

        time: formatInterviewTime(scheduledAt),
      };
    }),
);

// ==================================
// 6. COMMON HELPERS
// ==================================

const getPercentage = (value: number, total: number = jobStats.value.total) => {
  if (total === 0) return "0%";

  return `${Math.min((value / total) * 100, 100)}%`;
};

const formatDateRange = (range: { from: string; to: string }) => {
  if (!range?.from || !range?.to) return "";

  const formatDate = (date: string) =>
    new Date(`${date}T00:00:00Z`).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      timeZone: "UTC",
    });

  return `${formatDate(range.from)} – ${formatDate(range.to)}, ${range.to.slice(0, 4)}`;
};

// ==================================
// 7. YEARLY APPLICANT STATISTICS
// ==================================

// Default year = current year
const currentYear = new Date().getFullYear();

const selectedYear = ref<number>(currentYear);

// Year options: current year back to 2020
const yearOptions = computed(() =>
  Array.from({ length: currentYear - 2019 }, (_, index) => currentYear - index),
);

// Fetch yearly applicant statistics
const {
  data: yearlyApplicantStatsData,
  isPending: isYearlyApplicantLoading,
  isError: isYearlyApplicantError,
  refetch: refetchYearlyApplicantStats,
} = useEmployerYearlyApplicantStats(selectedYear);

// ==================================
// 8. MONTHLY APPLICANT DATA
// ==================================

const defaultMonths = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];

const monthlyStats = computed(() => {
  const apiStats = yearlyApplicantStatsData.value?.monthly_stats ?? [];

  // Always display all 12 months.
  // If a month is missing from API, its count will be 0.
  return defaultMonths.map((label, index) => {
    const monthData = apiStats.find(
      (item: { month: number; count: number }) => item.month === index + 1,
    );

    return {
      month: index + 1,
      label,
      count: monthData?.count ?? 0,
    };
  });
});

// ==================================
// 9. YEARLY APPLICANT TOTAL
// ==================================

const yearlyApplicantTotal = computed(() =>
  monthlyStats.value.reduce((total, month) => total + month.count, 0),
);

// ==================================
// 10. CHART Y AXIS
// ==================================

// Maximum value for the chart
const chartMax = computed(() => {
  const maxCount = Math.max(
    ...monthlyStats.value.map((month) => month.count),
    0,
  );

  // Minimum chart scale = 4
  // Round up to a multiple of 4
  return Math.max(4, Math.ceil(maxCount / 4) * 4);
});

// Y-axis labels
const yAxisTicks = computed(() => [
  chartMax.value,
  (chartMax.value * 3) / 4,
  chartMax.value / 2,
  chartMax.value / 4,
  0,
]);
</script>

<template>
  <div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/40 to-purple-50/30 font-sans text-slate-800 selection:bg-indigo-500 selection:text-white pb-12"
  >
    <main class="max-w-7xl mx-auto px-6 pt-12 md:pt-5">
      <!-- Page Title & Actions -->
      <div
        class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6"
      >
        <div>
          <div class="flex items-center gap-3 mb-2">
            <span class="h-1.5 w-8 rounded-full bg-indigo-500"></span>
            <span
              class="text-xs font-bold text-indigo-600 tracking-widest uppercase"
              >Dashboard</span
            >
          </div>
          <h1
            class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight"
          >
            Employer Overview
          </h1>
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="isPending"
        class="mb-4 rounded-xl bg-blue-50 p-4 text-blue-700"
      >
        Loading job statistics...
      </div>

      <!-- Error -->
      <div
        v-else-if="isError"
        class="mb-4 rounded-xl bg-rose-50 p-4 text-rose-700"
      >
        <p>{{ error?.message || "Failed to load job statistics." }}</p>

        <button
          @click="refetch()"
          class="mt-2 rounded-lg bg-rose-600 px-4 py-2 text-white"
        >
          Retry
        </button>
      </div>
      <!-- ================= TOP STAT CARDS (Clickable Links) ================= -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Job Posts (Blue Theme) -->
        <RouterLink
          to="/employer/jobs"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>
          <div class="relative z-10">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-colors"
              >
                <Briefcase class="w-5 h-5" />
              </div>
              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-blue-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-blue-100"
                >Jobs</span
              >
            </div>

            <!-- Main Stat -->
            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ jobStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Total Job Posts
              </p>
            </div>

            <!-- Progress Bar Style Breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <!-- Draft -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Draft</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    jobStats.draft
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-blue-500 h-1.5 rounded-full"
                    :style="{ width: getPercentage(jobStats.draft) }"
                  ></div>
                </div>
              </div>

              <!-- Pending -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Pending</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    jobStats.pending
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-amber-500 h-1.5 rounded-full"
                    :style="{ width: getPercentage(jobStats.pending) }"
                  ></div>
                </div>
              </div>

              <!-- Published & Rejected -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <span
                      class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                      >Pub</span
                    >
                    <span class="text-xs font-bold text-slate-700">{{
                      jobStats.published
                    }}</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div
                      class="bg-emerald-500 h-1.5 rounded-full"
                      :style="{ width: getPercentage(jobStats.published) }"
                    ></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <span
                      class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                      >Rej</span
                    >
                    <span class="text-xs font-bold text-slate-700">{{
                      jobStats.rejected
                    }}</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div
                      class="bg-rose-500 h-1.5 rounded-full"
                      :style="{ width: getPercentage(jobStats.rejected) }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </RouterLink>

        <!-- Card 2: Applicants (Emerald Theme) -->

        <RouterLink
          to="/employer/applicants"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>

          <div class="relative z-10">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-colors"
              >
                <Users class="w-5 h-5" />
              </div>

              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-emerald-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-emerald-100"
              >
                Applicants
              </span>
            </div>

            <!-- Main Stat -->
            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ applicantStats.total }}
              </h2>

              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Applicants (This Month)
              </p>
            </div>

            <!-- All Applicant Status Breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <!-- Pending -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Pending
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.pending }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-amber-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.pending,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Reviewed -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Reviewed
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.reviewed }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-teal-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.reviewed,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Shortlisted -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Shortlisted
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.shortlisted }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-blue-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.shortlisted,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Interview -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Interview
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.interview }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-purple-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.interview,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Accepted -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Accepted
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.accepted }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-emerald-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.accepted,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Rejected -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Rejected
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.rejected }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-rose-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.rejected,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Withdrawn -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >
                    Withdrawn
                  </span>
                  <span class="text-xs font-bold text-slate-700">
                    {{ applicantStats.withdrawn }}
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-slate-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        applicantStats.withdrawn,
                        applicantStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <!-- Date Range -->
              <div class="pt-2">
                <p
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Range: {{ formatDateRange(applicantStats.date_range) }}
                </p>
              </div>
            </div>
          </div>
        </RouterLink>

        <!-- Card 3: Direct Offers (Purple Theme) -->
        <RouterLink
          to="/employer/job-offers"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>
          <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shadow-inner group-hover:bg-purple-600 group-hover:text-white transition-colors"
              >
                <Send class="w-5 h-5" />
              </div>
              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-purple-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-purple-100"
              >
                Offers
              </span>
            </div>

            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ offerStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Direct Offers (This Month)
              </p>
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Pending</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    offerStats.pending
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-amber-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        offerStats.pending,
                        offerStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Accepted</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    offerStats.accepted
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-emerald-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        offerStats.accepted,
                        offerStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Rejected</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    offerStats.rejected
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-rose-500 h-1.5 rounded-full"
                    :style="{
                      width: getPercentage(
                        offerStats.rejected,
                        offerStats.total,
                      ),
                    }"
                  ></div>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="flex justify-between items-center">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Expired</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    offerStats.expired
                  }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Withdrawn</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    offerStats.withdrawn
                  }}</span>
                </div>
              </div>

              <div class="pt-2">
                <p
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Range: {{ formatDateRange(offerStats.date_range) }}
                </p>
              </div>
            </div>
          </div>
        </RouterLink>

        <!-- Card 4: Schedule (Orange Theme) -->
        <RouterLink
          to="/employer/interviews"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>
          <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shadow-inner group-hover:bg-orange-600 group-hover:text-white transition-colors"
              >
                <CalendarDays class="w-5 h-5" />
              </div>
              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-orange-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-orange-100"
              >
                Schedule
              </span>
            </div>

            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ scheduleStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Today & Upcoming Interviews
              </p>
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <div class="flex items-center justify-between">
                <span
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Today
                </span>
                <span
                  class="text-xs font-bold text-slate-700 bg-orange-50 px-2 py-1 rounded-md"
                >
                  {{ scheduleStats.today }}
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Upcoming
                </span>
                <span
                  class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md"
                >
                  {{ scheduleStats.total }}
                </span>
              </div>

              <div class="flex items-center justify-between pt-1">
                <span
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Action
                </span>
                <span
                  class="text-[10px] font-bold text-orange-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform"
                >
                  View Calendar <ArrowRight class="w-3 h-3" />
                </span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- Yearly Applicant Statistics Chart -->
      <div
        class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col"
      >
        <!-- Chart Header -->
        <div
          class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8"
        >
          <div>
            <h3 class="text-lg font-extrabold text-slate-900">
              Yearly Applicant Statistics
            </h3>
            <p class="text-sm text-slate-500 font-medium mt-1">
              Monthly applicant overview
            </p>
          </div>

          <!-- Year Selector -->
          <div class="flex items-center gap-2">
            <label
              for="applicant-year"
              class="text-xs font-bold text-slate-500"
            >
              Year
            </label>

            <select
              id="applicant-year"
              v-model.number="selectedYear"
              class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
            >
              <option v-for="year in yearOptions" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
        </div>

        <!-- Total Applicants -->
        <div class="mb-6">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
            Total Applicants in {{ selectedYear }}
          </p>

          <h2 class="text-3xl font-black text-slate-900 mt-1">
            {{ yearlyApplicantTotal }}
          </h2>
        </div>

        <!-- Loading -->
        <div
          v-if="isYearlyApplicantLoading"
          class="h-[280px] flex items-center justify-center text-sm font-semibold text-slate-400"
        >
          Loading applicant statistics...
        </div>

        <!-- Error -->
        <div
          v-else-if="isYearlyApplicantError"
          class="h-[280px] flex flex-col items-center justify-center text-center"
        >
          <p class="text-sm font-semibold text-rose-500">
            Failed to load applicant statistics.
          </p>
          <button
            @click="refetchYearlyApplicantStats()"
            class="mt-3 rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700"
          >
            Retry
          </button>
        </div>

        <!-- Chart -->
        <div v-else class="w-full">
          <div class="flex h-[250px]">
            <!-- Y Axis -->
            <div
              class="w-10 shrink-0 flex flex-col justify-between text-right pr-2"
            >
              <span
                v-for="tick in yAxisTicks"
                :key="tick"
                class="text-[10px] font-semibold text-slate-400"
              >
                {{ tick }}
              </span>
            </div>

            <!-- Chart Area -->
            <div class="relative flex-1 min-w-0">
              <!-- Horizontal Grid Lines -->
              <div
                v-for="tick in yAxisTicks"
                :key="`grid-${tick}`"
                class="absolute left-0 right-0 border-t border-dashed border-slate-200"
                :style="{
                  top: `${100 - (tick / chartMax) * 100}%`,
                }"
              ></div>

              <!-- Bars -->
              <div
                class="absolute inset-0 flex items-end justify-around gap-1 sm:gap-3"
              >
                <div
                  v-for="month in monthlyStats"
                  :key="month.month"
                  class="relative flex-1 min-w-0 h-full flex flex-col justify-end items-center"
                >
                  <!-- Count -->
                  <span
                    class="text-[10px] sm:text-xs font-bold text-slate-600 mb-1"
                  >
                    {{ month.count }}
                  </span>

                  <!-- Bar -->
                  <div
                    class="w-full max-w-10 bg-indigo-500 rounded-t-lg hover:bg-indigo-600 transition-all duration-300"
                    :style="{
                      height: `${(month.count / chartMax) * 100}%`,
                      minHeight: month.count > 0 ? '4px' : '0px',
                    }"
                    :title="`${month.label}: ${month.count} applicants`"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- X Axis: Month Labels -->
          <div class="flex ml-10 mt-2">
            <div
              v-for="month in monthlyStats"
              :key="`label-${month.month}`"
              class="flex-1 min-w-0 text-center text-[9px] sm:text-[11px] font-bold text-slate-400"
            >
              {{ month.label }}
            </div>
          </div>

          <!-- X Axis Label -->
          <p
            class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-3"
          >
            Months
          </p>
        </div>

        <!-- Empty Information -->
        <p
          v-if="
            !isYearlyApplicantLoading &&
            !isYearlyApplicantError &&
            yearlyApplicantTotal === 0
          "
          class="text-center text-xs text-slate-400 mt-4"
        >
          No applicants found for {{ selectedYear }}.
        </p>
      </div>

      <!-- ================= BOTTOM SECTION: UPCOMING INTERVIEWS ================= -->
      <div
        class="bg-white rounded-3xl border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden"
      >
        <!-- List Header -->
        <div
          class="px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50"
        >
          <div>
            <h3 class="text-xl font-extrabold text-slate-900">
              Upcoming Interviews
            </h3>
            <p class="text-sm font-medium text-slate-500 mt-1">
              Five nearest scheduled interviews
            </p>
          </div>
          <RouterLink
            to="/employer/interviews"
            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors"
          >
            View Full Schedule
            <ArrowRight class="w-4 h-4" />
          </RouterLink>
        </div>

        <div class="divide-y divide-slate-100">
          <div
            v-if="upcomingInterviewItems.length === 0"
            class="px-8 py-12 text-center text-sm font-medium text-slate-400"
          >
            No upcoming interviews.
          </div>

          <!-- Loop through Upcoming Interviews -->
          <div
            v-for="interview in upcomingInterviewItems"
            :key="interview.id"
            class="px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-slate-50/80 transition-colors group cursor-pointer gap-4"
          >
            <div class="flex items-center gap-5">
              <!-- Date Box -->
              <div
                class="flex flex-col items-center justify-center w-14 h-14 bg-white border-2 border-slate-100 rounded-2xl shadow-sm group-hover:border-indigo-400 transition-colors"
              >
                <span
                  class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
                  >{{ interview.month }}</span
                >
                <span class="text-xl font-black text-slate-900 leading-none">{{
                  interview.day
                }}</span>
              </div>
              <!-- Candidate Info -->
              <div>
                <h4
                  class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"
                >
                  {{ interview.candidate }}
                </h4>
                <div class="flex items-center gap-2 mt-1">
                  <span
                    :class="`text-xs font-bold px-2 py-0.5 rounded ${interview.tagColor}`"
                  >
                    {{ interview.role }}
                  </span>
                  <span class="text-xs font-medium text-slate-400">{{
                    interview.level
                  }}</span>
                </div>
              </div>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-4 pl-[76px] sm:pl-0">
              <div
                class="flex items-center gap-2 text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg"
              >
                <Clock class="w-4 h-4 text-slate-400" /> {{ interview.time }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
