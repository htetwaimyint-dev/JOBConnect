<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useNotifications } from "../composables/useNotifications";
import type { AppNotification } from "../types/notification.types";

const router = useRouter();
const isOpen = ref(false);
const panel = ref<HTMLElement | null>(null);

const {
    getNotifications,
    getUnreadCount,
    markAsRead,
    markAllAsRead,
    listenForNotifications,
} = useNotifications();

const {
    data: notificationData,
    isPending,
    isError,
    refetch,
} = getNotifications(isOpen);
const { data: unreadSummary } = getUnreadCount();
const unreadCount = computed(() => unreadSummary.value?.unread_count ?? 0);
let stopListening: (() => void) | null = null;

watch(
    () => unreadSummary.value?.user_id,
    (userId) => {
        stopListening?.();
        stopListening = null;

        if (userId) {
            stopListening = listenForNotifications(userId);
        }
    },
    { immediate: true },
);

const notifications = computed(
    () => notificationData.value?.notifications ?? [],
);

function togglePanel() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) void refetch();
}

function formatDate(value: string | null): string {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";
    return new Intl.DateTimeFormat(undefined, {
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
    }).format(date);
}

/**
 * Resolve only routes that exist in the supplied router configuration.
 * Notifications without a supported destination are still marked as read.
 */
function getNotificationDestination(notification: AppNotification) {
    const data = notification.data;
    const applicationId = data.application_id;
    const interviewId = data.interview_id;

    switch (data.type) {
        case "application_submitted":
        case "application_status_changed":
            return applicationId
                ? {
                      name: "JobSeekerApplicationDetail",
                      params: { id: String(applicationId) },
                  }
                : { name: "JobSeekerApplications" };

        case "new_application_received":
        case "application_withdrawn":
            return { name: "EmployerApplicants" };

        case "interview_scheduled":
        case "interview_rescheduled":
        case "interview_cancelled":
            return {
                name: "JobSeekerInterviews",
                query: interviewId ? { interview_id: String(interviewId) } : {},
            };

        case "interview_reschedule_requested":
            return {
                name: "employer.interviews",
                query: interviewId ? { interview_id: String(interviewId) } : {},
            };

        default:
            return null;
    }
}

async function openNotification(notification: AppNotification) {
    try {
        // Mark it read first. The unread-only list/count are invalidated by the mutation.
        await markAsRead.mutateAsync(notification.id);
        isOpen.value = false;

        const destination = getNotificationDestination(notification);
        if (destination) {
            await router.push(destination);
        }
    } catch (error) {
        // Keep the panel open if marking as read fails so the user can retry.
        console.error("Unable to open notification:", error);
    }
}

async function markEverythingAsRead() {
    try {
        await markAllAsRead.mutateAsync();
    } catch (error) {
        console.error("Unable to mark notifications as read:", error);
    }
}

function handleDocumentClick(event: MouseEvent) {
    const target = event.target;
    if (
        target instanceof Node &&
        panel.value &&
        !panel.value.contains(target)
    ) {
        isOpen.value = false;
    }
}

onMounted(() => document.addEventListener("click", handleDocumentClick));
onBeforeUnmount(() => {
    document.removeEventListener("click", handleDocumentClick);
    stopListening?.();
});
</script>

<template>
    <div ref="panel" class="relative">
        <button
            type="button"
            class="relative inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            aria-label="Notifications"
            :aria-expanded="isOpen"
            @click="togglePanel"
        >
            <svg
                viewBox="0 0 24 24"
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"
                />
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute right-1 top-1 flex min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold leading-4 text-white"
            >
                {{ unreadCount > 99 ? "99+" : unreadCount }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <section
                v-if="isOpen"
                class="absolute right-0 z-50 mt-3 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                aria-label="Notification list"
            >
                <header
                    class="flex items-center justify-between border-b border-slate-100 px-4 py-3"
                >
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">
                            Notifications
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ unreadCount }} unread
                        </p>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        type="button"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-700 disabled:opacity-50"
                        :disabled="markAllAsRead.isPending.value"
                        @click="markEverythingAsRead"
                    >
                        Mark all read
                    </button>
                </header>

                <div class="max-h-[26rem] overflow-y-auto">
                    <div
                        v-if="isPending"
                        class="px-4 py-10 text-center text-sm text-slate-500"
                    >
                        Loading notifications…
                    </div>
                    <div v-else-if="isError" class="px-4 py-8 text-center">
                        <p class="text-sm text-rose-600">
                            Unable to load notifications.
                        </p>
                        <button
                            type="button"
                            class="mt-2 text-xs font-medium text-indigo-600"
                            @click="refetch()"
                        >
                            Try again
                        </button>
                    </div>
                    <div
                        v-else-if="notifications.length === 0"
                        class="px-4 py-10 text-center"
                    >
                        <p class="text-sm font-medium text-slate-700">
                            You're all caught up
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            New updates will appear here.
                        </p>
                    </div>

                    <button
                        v-for="notification in notifications"
                        :key="notification.id"
                        type="button"
                        class="flex w-full gap-3 border-b border-slate-100 bg-indigo-50/50 px-4 py-3 text-left transition hover:bg-indigo-50 disabled:cursor-wait disabled:opacity-60"
                        :disabled="markAsRead.isPending.value"
                        @click="openNotification(notification)"
                    >
                        <span
                            class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-indigo-500"
                        />
                        <span class="min-w-0 flex-1">
                            <span
                                class="block text-sm font-semibold text-slate-800"
                            >
                                {{ notification.data.title ?? "Notification" }}
                            </span>
                            <span
                                class="mt-1 block text-xs leading-5 text-slate-600"
                            >
                                {{
                                    notification.data.message ??
                                    "You have a new update."
                                }}
                            </span>
                            <span
                                class="mt-1.5 block text-[11px] text-slate-400"
                            >
                                {{ formatDate(notification.created_at) }}
                            </span>
                        </span>
                    </button>
                </div>
            </section>
        </Transition>
    </div>
</template>
