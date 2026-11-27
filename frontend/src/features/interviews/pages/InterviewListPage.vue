<script setup lang="ts">
import { computed, reactive, ref } from "vue";
import InterviewFilters from "../components/InterviewFilters.vue";
import InterviewCalendarToolbar from "../components/InterviewCalendarToolbar.vue";
import InterviewCalendar from "../components/InterviewCalendar.vue";
import InterviewStats from "../components/InterviewStats.vue";
import InterviewDetailsDrawer from "../components/InterviewDetailsDrawer.vue";
import ScheduleInterviewModal from "../components/ScheduleInterviewModal.vue";
import InterviewerManagementModal from "../components/InterviewerManagementModal.vue";
import { useInterviews } from "../composables/useInterviews";
import type {
    Interview,
    InterviewFilters as FilterState,
    InterviewFormData,
    InterviewStatus,
    InterviewerFormData,
} from "../types/interview.types";
import { toast } from "vue3-toastify";

const filters = reactive<FilterState>({
    search: "",
    status: "all",
    interview_type: "all",
    from: "",
    to: "",
});

const hasActiveFilters = computed(() => {
    return (
        filters.search.trim() !== "" ||
        filters.status !== "all" ||
        filters.interview_type !== "all" ||
        filters.from !== "" ||
        filters.to !== ""
    );
});

const {
    getInterviews,
    getInterviewers,
    getAvailableApplications,
    createInterview,
    updateInterviewStatus,
    createInterviewer,
    updateInterviewer,
    deleteInterviewer,
} = useInterviews();

const query = getInterviews(filters);
const statsQuery = getInterviews();
const interviewersQuery = getInterviewers();
const applicationsQuery = getAvailableApplications();

const interviews = computed(() => query.data.value ?? []);
const allInterviews = computed(() => statsQuery.data.value ?? []);
const interviewers = computed(() => interviewersQuery.data.value ?? []);
const applications = computed(() => applicationsQuery.data.value ?? []);

const selected = ref<Interview | null>(null);
const currentDate = ref(new Date());
const showScheduleModal = ref(false);
const showInterviewerModal = ref(false);

const savingInterview = computed(() => createInterview.isPending.value);
const savingInterviewer = computed(
    () =>
        createInterviewer.isPending.value || updateInterviewer.isPending.value,
);
const loading = computed(() => query.isFetching.value);

function clearFilters() {
    Object.assign(filters, {
        search: "",
        status: "all",
        interview_type: "all",
        from: "",
        to: "",
    });
}

const selectedScheduleSlot = ref<{
    date: Date;
    hour: number;
} | null>(null);

function openScheduleModal(slot?: { date: Date; hour: number }) {
    if (slot) {
        const selectedDateTime = new Date(slot.date);
        selectedDateTime.setHours(slot.hour, 0, 0, 0);

        if (selectedDateTime.getTime() < Date.now()) {
            toast.error("You cannot schedule an interview in the past.", {
                position: "top-right",
                autoClose: 3000,
            });

            return;
        }
    }

    selectedScheduleSlot.value = slot ?? null;
    showScheduleModal.value = true;
}

function moveWeek(amount: number) {
    const next = new Date(currentDate.value);
    next.setDate(next.getDate() + amount * 7);
    currentDate.value = next;
}

async function refreshCalendar() {
    await Promise.all([query.refetch()]);
}

async function scheduleInterview(data: InterviewFormData) {
    await createInterview.mutateAsync(data);
    showScheduleModal.value = false;
}

async function changeStatus(id: number, status: InterviewStatus) {
    await updateInterviewStatus.mutateAsync({ id, status });
    selected.value = null;
}

async function addInterviewer(data: InterviewerFormData) {
    await createInterviewer.mutateAsync(data);
}

async function editInterviewer(id: number, data: InterviewerFormData) {
    await updateInterviewer.mutateAsync({ id, data });
}

async function removeInterviewer(id: number) {
    await deleteInterviewer.mutateAsync(id);
}
</script>

<template>
    <div
        class="screen-locked flex h-full min-h-0 flex-1 flex-col overflow-hidden text-slate-800"
    >
        <!-- Header -->
        <div class="mb-2.5 flex shrink-0 items-center justify-between gap-3">
            <div class="min-w-0">
                <h1
                    class="text-lg font-bold tracking-tight text-slate-900 sm:text-xl"
                >
                    Interview schedule
                </h1>
                <p class="mt-0.5 hidden text-[11px] text-slate-500 sm:block">
                    Plan interviews and keep your hiring team aligned
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                    @click="showInterviewerModal = true"
                >
                    Manage interviewers
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700"
                    @click="openScheduleModal()"
                >
                    <span class="text-base leading-none">+</span> Schedule
                    interview
                </button>
            </div>
        </div>

        <!-- Middle Content -->
        <div
            class="grid min-h-0 flex-1 grid-cols-1 gap-3 overflow-hidden lg:grid-cols-[360px_minmax(0,1fr)]"
        >
            <div class="hidden min-h-0 overflow-hidden pr-1 lg:block">
                <InterviewFilters
                    :filters="filters"
                    @update:filters="Object.assign(filters, $event)"
                    @reset="clearFilters"
                />
            </div>

            <section
                class="flex min-h-0 min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
            >
                <div class="shrink-0 border-b border-slate-200 px-4 py-2">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-xs font-bold text-slate-900">
                                Weekly calendar
                            </h2>
                            <InterviewCalendarToolbar
                                :current-date="currentDate"
                                @previous="moveWeek(-1)"
                                @next="moveWeek(1)"
                                @today="currentDate = new Date()"
                                @refresh="refreshCalendar"
                            />
                        </div>
                        <span
                            v-if="hasActiveFilters"
                            class="hidden rounded-md bg-slate-50 px-2 py-1 text-[10px] font-medium text-slate-500 sm:inline"
                        >
                            {{ interviews.length }} matching interviews
                        </span>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-auto">
                    <InterviewCalendar
                        :interviews="interviews"
                        :current-date="currentDate"
                        :loading="loading"
                        @select="selected = $event"
                        @add-schedule="openScheduleModal"
                        ><div
                            v-if="hasActiveFilters && !interviews.length"
                            class="sticky bottom-3 mx-auto -mt-12 w-fit rounded-lg border border-slate-200 bg-white/95 px-3 py-2 text-[11px] font-medium text-slate-500 shadow-sm"
                        >
                            No interviews match these filters.
                        </div>
                    </InterviewCalendar>
                </div>
            </section>
        </div>

        <!-- Footer Stats -->
        <section class="mt-2.5 shrink-0" aria-label="Interview statistics">
            <InterviewStats :interviews="allInterviews" />
        </section>

        <!-- Drawers & Modals -->
        <InterviewDetailsDrawer
            :interview="selected"
            @close="selected = null"
            @status="changeStatus"
        />

        <ScheduleInterviewModal
            :open="showScheduleModal"
            :applications="applications"
            :interviewers="interviewers"
            :saving="savingInterview"
            :selected-slot="selectedScheduleSlot"
            @close="showScheduleModal = false"
            @submit="scheduleInterview"
            @manage-interviewers="showInterviewerModal = true"
        />

        <InterviewerManagementModal
            :open="showInterviewerModal"
            :interviewers="interviewers"
            :saving="savingInterviewer"
            @close="showInterviewerModal = false"
            @create="addInterviewer"
            @update="editInterviewer"
            @delete="removeInterviewer"
        />
    </div>
</template>
