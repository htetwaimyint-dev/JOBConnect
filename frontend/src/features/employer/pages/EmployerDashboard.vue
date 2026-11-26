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
          <p class="text-slate-500 mt-3 text-lg font-medium max-w-2xl">
            Here is a snapshot of your recruitment pipeline, job posts, and
            upcoming activities.
          </p>
        </div>
      </div>

      <!-- ================= TOP STAT CARDS (Clickable Links) ================= -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Job Posts (Blue Theme) -->
        <a
          href="/employer/jobs"
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
                    :style="{
                      width: (jobStats.draft / jobStats.total) * 100 + '%',
                    }"
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
                    :style="{
                      width: (jobStats.pending / jobStats.total) * 100 + '%',
                    }"
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
                      :style="{
                        width:
                          (jobStats.published / jobStats.total) * 100 + '%',
                      }"
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
                      :style="{
                        width: (jobStats.rejected / jobStats.total) * 100 + '%',
                      }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 2: Applicants (Emerald Theme) -->
        <a
          href="/employer/applicants"
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
                >Applicants</span
              >
            </div>

            <!-- Main Stat -->
            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ applicantStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Total Applicants
              </p>
            </div>

            <!-- Progress Bar Style Breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <!-- Pending -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Pending</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    applicantStats.pending
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-amber-500 h-1.5 rounded-full"
                    :style="{
                      width:
                        (applicantStats.pending / applicantStats.total) * 100 +
                        '%',
                    }"
                  ></div>
                </div>
              </div>

              <!-- Shortlist -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Shortlist</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    applicantStats.shortlisted
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-teal-500 h-1.5 rounded-full"
                    :style="{
                      width:
                        (applicantStats.shortlisted / applicantStats.total) *
                          100 +
                        '%',
                    }"
                  ></div>
                </div>
              </div>

              <!-- Interview -->
              <div>
                <div class="flex justify-between items-center mb-1">
                  <span
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    >Interview</span
                  >
                  <span class="text-xs font-bold text-slate-700">{{
                    applicantStats.interview
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-purple-500 h-1.5 rounded-full"
                    :style="{
                      width:
                        (applicantStats.interview / applicantStats.total) *
                          100 +
                        '%',
                    }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 3: Direct Offers (Purple Theme) -->
        <a
          href="/employer/offers"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>
          <div class="relative z-10">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shadow-inner group-hover:bg-purple-600 group-hover:text-white transition-colors"
              >
                <Send class="w-5 h-5" />
              </div>
              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-purple-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-purple-100"
                >Offers</span
              >
            </div>

            <!-- Main Stat -->
            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ offerStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Direct Offers (Past 5 Days)
              </p>
            </div>

            <!-- Progress Bar Style Breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <!-- Accepted -->
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
                    class="bg-purple-500 h-1.5 rounded-full"
                    :style="{
                      width:
                        (offerStats.accepted / offerStats.total) * 100 + '%',
                    }"
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
                    offerStats.pending
                  }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                  <div
                    class="bg-amber-500 h-1.5 rounded-full"
                    :style="{
                      width:
                        (offerStats.pending / offerStats.total) * 100 + '%',
                    }"
                  ></div>
                </div>
              </div>

              <!-- Rejected -->
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
                      width:
                        (offerStats.rejected / offerStats.total) * 100 + '%',
                    }"
                  ></div>
                </div>
              </div>

              <!-- Date Range moved into the padding area neatly -->
              <div class="pt-2">
                <p
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                >
                  Range: {{ offerStats.dateRange }}
                </p>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 4: Schedule (Orange Theme) -->
        <a
          href="/employer/schedule"
          class="block relative overflow-hidden bg-white rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] transition-all duration-300"
        >
          <div
            class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"
          ></div>
          <div class="relative z-10">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
              <div
                class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shadow-inner group-hover:bg-orange-600 group-hover:text-white transition-colors"
              >
                <CalendarDays class="w-5 h-5" />
              </div>
              <span
                class="px-2.5 py-1 bg-white/60 backdrop-blur-sm text-orange-700 text-[10px] font-bold rounded-lg uppercase tracking-wider border border-orange-100"
                >Schedule</span
              >
            </div>

            <!-- Main Stat -->
            <div class="mb-5">
              <h2 class="text-3xl font-black text-slate-900 leading-none">
                {{ scheduleStats.total }}
              </h2>
              <p class="text-xs font-semibold text-slate-500 mt-1.5">
                Interviews (Next 5 Days)
              </p>
            </div>

            <!-- Clean Breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-100/80">
              <!-- Date Range Pill -->
              <div class="flex items-center justify-between">
                <span
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >Range</span
                >
                <span
                  class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md"
                >
                  {{ scheduleStats.dateRange }}
                </span>
              </div>

              <!-- Action Hint -->
              <div class="flex items-center justify-between pt-1">
                <span
                  class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                  >Action</span
                >
                <span
                  class="text-[10px] font-bold text-orange-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform"
                >
                  View Calendar <ArrowRight class="w-3 h-3" />
                </span>
              </div>
            </div>
          </div>
        </a>
      </div>

      <!-- ================= CHARTS SECTION ================= -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Donut Chart: Job Post Status -->
        <div
          class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col"
        >
          <div class="mb-2">
            <h3 class="text-lg font-extrabold text-slate-900">
              Job Post Status
            </h3>
            <p class="text-sm text-slate-500 font-medium mt-1">
              Understand the current status of your job posts
            </p>
          </div>

          <!-- Custom CSS Donut Chart -->
          <div
            class="flex-1 flex flex-col items-center justify-center min-h-[250px]"
          >
            <div
              class="relative w-48 h-48 rounded-full flex items-center justify-center shadow-inner"
              :style="{ background: donutGradient }"
            >
              <!-- Inner Circle to make it a Donut -->
              <div
                class="absolute w-32 h-32 bg-white rounded-full shadow-sm flex flex-col items-center justify-center"
              >
                <span class="text-3xl font-black text-slate-900">{{
                  jobStats.total
                }}</span>
                <span
                  class="text-xs font-bold text-slate-400 uppercase tracking-wider"
                  >Total</span
                >
              </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap justify-center gap-4 mt-8">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-xs font-bold text-slate-600"
                  >Draft ({{ jobStats.draft }})</span
                >
              </div>
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-xs font-bold text-slate-600"
                  >Pending ({{ jobStats.pending }})</span
                >
              </div>
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-bold text-slate-600"
                  >Published ({{ jobStats.published }})</span
                >
              </div>
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span class="text-xs font-bold text-slate-600"
                  >Rejected ({{ jobStats.rejected }})</span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Bar Chart: Applicant Status -->
        <div
          class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
        >
          <div class="mb-6">
            <h3 class="text-lg font-extrabold text-slate-900">
              Applicant Status Overview
            </h3>
            <p class="text-sm text-slate-500 font-medium mt-1">
              Compare applicants across recruitment stages
            </p>
          </div>

          <!-- Custom CSS Bar Chart -->
          <div
            class="h-64 flex items-end justify-around gap-6 border-b border-slate-100 pb-2"
          >
            <div
              v-for="item in [
                {
                  label: 'Pending',
                  count: applicantStats.pending,
                  color: 'bg-amber-500',
                },
                {
                  label: 'Shortlisted',
                  count: applicantStats.shortlisted,
                  color: 'bg-teal-500',
                },
                {
                  label: 'Interview',
                  count: applicantStats.interview,
                  color: 'bg-purple-500',
                },
              ]"
              :key="item.label"
              class="flex flex-1 flex-col items-center justify-end h-full"
            >
              <!-- Count -->
              <span class="text-sm font-bold text-slate-700 mb-2">
                {{ item.count }}
              </span>

              <!-- Bar -->
              <div
                class="w-full max-w-[70px] rounded-t-xl transition-all duration-500"
                :class="item.color"
                :style="{
                  height: (item.count / maxApplicant) * 180 + 'px',
                }"
              ></div>

              <!-- Label -->
              <span class="text-xs font-bold text-slate-600 mt-3">
                {{ item.label }}
              </span>
            </div>
          </div>
        </div>
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
              Manage your candidate schedule for the next 5 days.
            </p>
          </div>
          <a
            href="/employer/schedule"
            class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors"
          >
            View Full Calendar
            <ArrowRight class="w-4 h-4" />
          </a>
        </div>

        <div class="divide-y divide-slate-100">
          <!-- Loop through Upcoming Interviews -->
          <div
            v-for="(interview, index) in upcomingInterviews"
            :key="index"
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
              <button
                class="px-4 py-2 text-xs font-bold text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm"
              >
                Details
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from "vue";
import {
  Briefcase,
  Users,
  Send,
  CalendarDays,
  ArrowRight,
  Clock,
} from "lucide-vue-next";

// ==========================================
// DUMMY DATA (Replace with API calls)
// ==========================================

// 1. Job Posts Logic
const jobStats = {
  total: 11,
  draft: 2,
  pending: 4,
  published: 3,
  rejected: 2,
};

// 2. Applicants Logic
const applicantStats = {
  total: 32,
  pending: 14,
  shortlisted: 12,
  interview: 6,
};

// 3. Direct Offers Logic (Past 5 days)
const offerStats = {
  total: 5,
  accepted: 2,
  rejected: 1,
  pending: 2,
  dateRange: "Nov 21 – Nov 26",
};

// 4. Schedule/Interviewer Logic (Next 5 days)
const scheduleStats = {
  total: 4,
  dateRange: "Nov 26 – Nov 30",
};

// 5. Upcoming Interviews List (Max 5)
const upcomingInterviews = [
  {
    month: "Nov",
    day: "26",
    candidate: "Aung Aung",
    role: "Laravel",
    level: "Senior Developer",
    time: "10:00 AM",
    tagColor: "text-blue-600 bg-blue-50",
  },
  {
    month: "Nov",
    day: "27",
    candidate: "Su Su",
    role: "UI/UX",
    level: "Mid-level Designer",
    time: "2:00 PM",
    tagColor: "text-pink-600 bg-pink-50",
  },
  {
    month: "Nov",
    day: "28",
    candidate: "Kyaw Kyaw",
    role: "Frontend",
    level: "Junior Developer",
    time: "9:30 AM",
    tagColor: "text-emerald-600 bg-emerald-50",
  },
  {
    month: "Nov",
    day: "29",
    candidate: "Mg Mg",
    role: "Backend",
    level: "Senior Developer",
    time: "1:00 PM",
    tagColor: "text-purple-600 bg-purple-50",
  },
  {
    month: "Nov",
    day: "30",
    candidate: "Hla Hla",
    role: "QA Engineer",
    level: "Mid-level",
    time: "11:00 AM",
    tagColor: "text-orange-600 bg-orange-50",
  },
];

// ==========================================
// CHART COMPUTED PROPERTIES
// ==========================================

const maxApplicant = computed(() => {
  return Math.max(
    applicantStats.pending,
    applicantStats.shortlisted,
    applicantStats.interview,
    1,
  );
});

const donutGradient = computed(() => {
  const total = jobStats.total;
  if (total === 0) return "conic-gradient(#f1f5f9 0% 100%)";

  const draftPct = (jobStats.draft / total) * 100;
  const pendingPct = (jobStats.pending / total) * 100;
  const publishedPct = (jobStats.published / total) * 100;
  const rejectedPct = (jobStats.rejected / total) * 100;

  const draftStop = draftPct;
  const pendingStop = draftStop + pendingPct;
  const publishedStop = pendingStop + publishedPct;
  const rejectedStop = publishedStop + rejectedPct;

  return `conic-gradient(
    #3b82f6 0% ${draftStop}%, 
    #f59e0b ${draftStop}% ${pendingStop}%, 
    #10b981 ${pendingStop}% ${publishedStop}%, 
    #f43f5e ${publishedStop}% ${rejectedStop}%
  )`;
});
</script>
