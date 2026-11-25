<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { toast } from "vue3-toastify";
import {
    CalendarDays,
    Clock2,
    CheckCircle2,
    RefreshCw,
    AlertTriangle,
    Search,
} from "lucide-vue-next";

import { useInterviews } from "@/features/interviews/composables/useInterviews";
import type {
    JobseekerInterviewFilters,
    Interview,
} from "@/features/interviews/types/interview.types";
import JobseekerInterviewCard from "@/features/interviews/components/JobseekerInterviewCard.vue";
import JobseekerInterviewDetailsDrawer from "@/features/interviews/components/JobseekerInterviewDetailsDrawer.vue";
import JobseekerInterviewEmpty from "@/features/interviews/components/JobseekerInterviewEmpty.vue";

const filters = reactive<JobseekerInterviewFilters>({
    search: "",
    status: "all",
    interview_type: "all",
    from: "",
    to: "",
});

const { getJobseekerInterviews, requestReschedule } = useInterviews();

const query = getJobseekerInterviews(filters);

const interviews = computed(() => query.data.value ?? []);
const loading = computed(() => query.isPending.value);
const error = computed(() => query.isError.value);

const filteredInterviews = computed(() => {
    let list = interviews.value;

    if (filters.search.trim()) {
        const term = filters.search.trim().toLowerCase();
        list = list.filter(
            (i) =>
                i.application?.job?.title?.toLowerCase().includes(term) ||
                i.application?.job?.company?.name
                    ?.toLowerCase()
                    .includes(term) ||
                i.interviewers.some((p) => p.name.toLowerCase().includes(term)),
        );
    }

    if (filters.status !== "all") {
        list = list.filter((i) => i.status === filters.status);
    }

    if (filters.interview_type !== "all") {
        list = list.filter((i) => i.interview_type === filters.interview_type);
    }

    if (filters.from) {
        list = list.filter(
            (i) => new Date(i.scheduled_at) >= new Date(filters.from!),
        );
    }

    if (filters.to) {
        list = list.filter(
            (i) => new Date(i.scheduled_at) <= new Date(filters.to!),
        );
    }

    return list.sort(
        (a, b) =>
            new Date(a.scheduled_at).getTime() -
            new Date(b.scheduled_at).getTime(),
    );
});

const today = new Date();
today.setHours(0, 0, 0, 0);

const stats = computed(() => {
    const list = interviews.value;
    const upcoming = list.filter(
        (i) =>
            (i.status === "scheduled" || i.status === "rescheduled") &&
            new Date(i.scheduled_at) >= today,
    );
    const todayInterviews = upcoming.filter(
        (i) => new Date(i.scheduled_at).toDateString() === today.toDateString(),
    );
    return {
        total: list.length,
        upcoming: upcoming.length,
        today: todayInterviews.length,
        completed: list.filter((i) => i.status === "completed").length,
        cancelled: list.filter((i) => i.status === "cancelled").length,
    };
});

const selected = ref<Interview | null>(null);

const statusLabels: Record<string, string> = {
    all: "All",
    scheduled: "Scheduled",
    completed: "Completed",
    cancelled: "Cancelled",
    rescheduled: "Rescheduled",
};

function clearFilters() {
    Object.assign(filters, {
        search: "",
        status: "all",
        interview_type: "all",
        from: "",
        to: "",
    });
}

function formatDate(value: string) {
    return new Date(value).toLocaleDateString(undefined, {
        weekday: "short",
        month: "short",
        day: "numeric",
        year: "numeric",
    });
}

function timeLabel(value: string) {
    return new Date(value).toLocaleTimeString([], {
        hour: "numeric",
        minute: "2-digit",
    });
}

async function handleAttend(id: number) {
    try {
        const interview = interviews.value.find((i) => i.id === id);
        if (interview) {
            interview.status = "completed";
        }
        selected.value = null;
        toast.success("Marked as attended.", {
            position: "top-right",
            autoClose: 3000,
        });
    } catch {
        toast.error("Unable to mark as attended.", {
            position: "top-right",
            autoClose: 3000,
        });
    }
}

async function handleRequestReschedule(id: number) {
    try {
        await requestReschedule.mutateAsync({
            id,
            data: {},
        });
        toast.success("Reschedule request submitted.", {
            position: "top-right",
            autoClose: 3000,
        });
    } catch {
        toast.error("Unable to submit reschedule request.", {
            position: "top-right",
            autoClose: 3000,
        });
    }
}

function handleRefresh() {
    query.refetch();
    toast.success("Interviews refreshed.", {
        position: "top-right",
        autoClose: 2000,
    });
}
</script>

<template>
    <main class="min-h-screen bg-slate-50">
        <!-- Page Header -->
        <section class="relative isolate overflow-hidden bg-[#102D59]">
            <div
                class="absolute -right-20 -top-28 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"
            ></div>
            <div
                class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"
            ></div>
            <div
                class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16"
            >
                <div
                    class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end"
                >
                    <div>
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-100"
                        >
                            <CalendarDays class="h-4 w-4 text-cyan-300" />
                            My Interviews
                        </div>
                        <h1
                            class="mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl"
                        >
                            Interviews
                        </h1>
                        <p
                            class="mt-3 max-w-xl text-sm leading-7 text-blue-100/80 sm:text-base"
                        >
                            Track your upcoming and past interviews. Join online
                            meetings, mark attendance, and prepare ahead.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
            <div
                class="group rounded-2xl border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                    >
                        <CalendarDays class="h-6 w-6" />
                    </div>
                    <span
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                    >
                        All
                    </span>
                </div>
                <p class="mt-5 text-3xl font-extrabold text-slate-900">
                    {{ stats.total }}
                </p>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    Total Interviews
                </p>
            </div>

            <div
                class="group rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"
                    >
                        <Clock2 class="h-6 w-6" />
                    </div>
                    <span
                        class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700"
                    >
                        Upcoming
                    </span>
                </div>
                <p class="mt-5 text-3xl font-extrabold text-slate-900">
                    {{ stats.upcoming }}
                </p>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    Scheduled &amp; rescheduled
                </p>
            </div>

            <div
                class="group rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"
                    >
                        <CalendarDays class="h-6 w-6" />
                    </div>
                    <span
                        class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700"
                    >
                        Today
                    </span>
                </div>
                <p class="mt-5 text-3xl font-extrabold text-slate-900">
                    {{ stats.today }}
                </p>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    Interviews today
                </p>
            </div>

            <div
                class="group rounded-2xl border border-green-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600"
                    >
                        <CheckCircle2 class="h-6 w-6" />
                    </div>
                    <span
                        class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700"
                    >
                        Completed
                    </span>
                </div>
                <p class="mt-5 text-3xl font-extrabold text-slate-900">
                    {{ stats.completed }}
                </p>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    Attended interviews
                </p>
            </div>
        </div>

        <!-- Filters & List -->
        <div class="mt-10 grid grid-cols-1 items-start gap-8 lg:grid-cols-4">
            <!-- Filters -->
            <div class="lg:col-span-1 space-y-6">
                <div
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >
                    <div class="border-b border-slate-100 px-4 py-3">
                        <h2
                            class="text-xs font-bold uppercase tracking-wider text-slate-500"
                        >
                            Filters
                        </h2>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold text-slate-700"
                                >Search</label
                            >
                            <div class="relative">
                                <Search
                                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                />
                                <input
                                    v-model="filters.search"
                                    type="search"
                                    placeholder="Job title or company"
                                    class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold text-slate-700"
                                >Status</label
                            >
                            <div class="space-y-2">
                                <label
                                    v-for="s in [
                                        'all',
                                        'scheduled',
                                        'rescheduled',
                                        'completed',
                                        'cancelled',
                                    ]"
                                    :key="s"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-slate-600 hover:text-slate-900"
                                >
                                    <input
                                        :checked="filters.status === s"
                                        :value="s"
                                        type="radio"
                                        name="interview-status"
                                        class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        @change="filters.status = s"
                                    />
                                    <span>{{ statusLabels[s] }}</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-2 block text-xs font-semibold text-slate-700"
                                >Type</label
                            >
                            <select
                                v-model="filters.interview_type"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                            >
                                <option value="all">All types</option>
                                <option value="online">Online</option>
                                <option value="onsite">On-site</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs text-slate-500"
                                >From date</label
                            >
                            <input
                                v-model="filters.from"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                            />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs text-slate-500"
                                >To date</label
                            >
                            <input
                                v-model="filters.to"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                            />
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button
                                type="button"
                                @click="clearFilters"
                                class="flex-1 py-2.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition"
                            >
                                Clear
                            </button>
                            <button
                                type="button"
                                @click="handleRefresh"
                                class="flex-1 py-2.5 rounded-lg border border-indigo-200 bg-indigo-50 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition"
                            >
                                <RefreshCw class="h-4 w-4" /> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interview List -->
            <div class="lg:col-span-3 min-w-0 space-y-6">
                <!-- Loading -->
                <div
                    v-if="loading"
                    class="flex items-center justify-center min-h-[200px]"
                >
                    <RefreshCw class="animate-spin h-8 w-8 text-indigo-600" />
                </div>

                <!-- Error -->
                <div
                    v-else-if="error"
                    class="rounded-xl border border-red-200 bg-red-50 p-6 text-center"
                >
                    <AlertTriangle class="mx-auto h-8 w-8 text-red-600 mb-3" />
                    <h3 class="text-sm font-semibold text-red-800">
                        Unable to load interviews
                    </h3>
                    <p class="mt-1 text-sm text-red-700">
                        Please try again later.
                    </p>
                    <button
                        type="button"
                        class="mt-4 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-100 transition"
                        @click="handleRefresh"
                    >
                        Try again
                    </button>
                </div>

                <!-- List -->
                <div
                    v-else-if="filteredInterviews.length === 0"
                    class="min-h-[300px]"
                >
                    <JobseekerInterviewEmpty />
                </div>

                <div v-else class="space-y-3">
                    <JobseekerInterviewCard
                        v-for="interview in filteredInterviews"
                        :key="interview.id"
                        :interview="interview"
                        @select="selected = interview"
                    />
                </div>
            </div>
        </div>

        <!-- Prep Tips Sidebar (mobile hidden) -->
        <aside class="lg:hidden mt-8 space-y-6 px-4">
            <div
                class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
            >
                <div class="border-b border-slate-100 px-4 py-3">
                    <h2
                        class="text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Interview prep tips
                    </h2>
                </div>
                <div class="p-4 space-y-3">
                    <p
                        class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-3"
                    >
                        Before the interview
                    </p>
                    <ul
                        class="text-sm text-slate-600 space-y-2 list-disc list-inside"
                    >
                        <li>Research the company and role.</li>
                        <li>Prepare answers to common questions.</li>
                        <li>Test your camera, mic, and internet.</li>
                        <li>Choose a quiet, well-lit space.</li>
                        <li>Keep your resume nearby.</li>
                    </ul>
                </div>
            </div>
        </aside>
        <JobseekerInterviewDetailsDrawer
            :interview="selected"
            @close="selected = null"
            @attend="handleAttend"
            @request-reschedule="handleRequestReschedule"
        />
    </main>
</template>
