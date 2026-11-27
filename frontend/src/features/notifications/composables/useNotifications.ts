import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import notificationRepository from "../repositories/notificationRepository";
import { listenForUserNotifications } from "@/services/echo";
import type {
    NotificationListData,
    NotificationUnreadSummary,
} from "../types/notification.types";

const RECOVERY_POLL_MS = 60_000;

export const notificationKeys = {
    all: ["notifications"] as const,
    list: () => [...notificationKeys.all, "list"] as const,
    unreadCount: () => [...notificationKeys.all, "unread-count"] as const,
};

export function useNotifications() {
    const queryClient = useQueryClient();

    function getNotifications(enabled: MaybeRefOrGetter<boolean> = true) {
        const isEnabled = computed(() => toValue(enabled));

        return useQuery({
            queryKey: notificationKeys.list(),
            queryFn: async () => {
                const response =
                    await notificationRepository.getNotifications();
                return response.data.data;
            },
            enabled: isEnabled,
            staleTime: 0,
            refetchInterval: RECOVERY_POLL_MS,
            refetchOnMount: "always",
            refetchOnWindowFocus: true,
            refetchOnReconnect: true,
        });
    }

    function getUnreadCount() {
        return useQuery({
            queryKey: notificationKeys.unreadCount(),
            queryFn: async () => {
                const response = await notificationRepository.getUnreadCount();
                return response.data.data;
            },
            staleTime: 0,
            refetchInterval: RECOVERY_POLL_MS,
            refetchOnMount: "always",
            refetchOnWindowFocus: true,
            refetchOnReconnect: true,
        });
    }

    function listenForNotifications(userId: number | string) {
        const receivedIds = new Set<string>();

        return listenForUserNotifications(userId, ({ notification }) => {
            if (receivedIds.has(notification.id)) return;
            receivedIds.add(notification.id);

            queryClient.setQueryData<NotificationUnreadSummary>(
                notificationKeys.unreadCount(),
                (current) => ({
                    unread_count: (current?.unread_count ?? 0) + 1,
                    user_id: current?.user_id ?? Number(userId),
                }),
            );

            queryClient.setQueryData<NotificationListData>(
                notificationKeys.list(),
                (current) => {
                    if (!current) return current;
                    if (
                        current.notifications.some(
                            (item) => item.id === notification.id,
                        )
                    ) {
                        return current;
                    }

                    return {
                        ...current,
                        notifications: [
                            notification,
                            ...current.notifications,
                        ].slice(0, current.meta.per_page),
                        meta: {
                            ...current.meta,
                            total: current.meta.total + 1,
                        },
                    };
                },
            );
        });
    }

    const markAsRead = useMutation({
        mutationFn: (notificationId: string) =>
            notificationRepository.markAsRead(notificationId),
        onSuccess: async () => {
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: notificationKeys.list(),
                }),
                queryClient.invalidateQueries({
                    queryKey: notificationKeys.unreadCount(),
                }),
            ]);
        },
    });

    const markAllAsRead = useMutation({
        mutationFn: () => notificationRepository.markAllAsRead(),
        onSuccess: async () => {
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: notificationKeys.list(),
                }),
                queryClient.invalidateQueries({
                    queryKey: notificationKeys.unreadCount(),
                }),
            ]);
        },
    });

    return {
        getNotifications,
        getUnreadCount,
        listenForNotifications,
        markAsRead,
        markAllAsRead,
    };
}
