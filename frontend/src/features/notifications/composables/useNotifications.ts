import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import notificationRepository from "../repositories/notificationRepository";

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
            refetchInterval: 30_000,
            refetchOnWindowFocus: true,
        });
    }

    function getUnreadCount() {
        return useQuery({
            queryKey: notificationKeys.unreadCount(),
            queryFn: async () => {
                const response = await notificationRepository.getUnreadCount();
                return response.data.data.unread_count;
            },
            refetchInterval: 30_000,
            refetchOnWindowFocus: true,
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
        markAsRead,
        markAllAsRead,
    };
}
