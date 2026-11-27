import api from "@/services/api";
import type {
    AppNotification,
    NotificationListData,
    NotificationUnreadSummary,
} from "../types/notification.types";

interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

const notificationRepository = {
    getNotifications(perPage = 10) {
        return api.get<ApiResponse<NotificationListData>>("/notifications", {
            params: { per_page: perPage },
        });
    },

    getUnreadCount() {
        return api.get<ApiResponse<NotificationUnreadSummary>>(
            "/notifications/unread-count",
        );
    },

    markAsRead(notificationId: string) {
        return api.patch<ApiResponse<AppNotification>>(
            `/notifications/${notificationId}/read`,
        );
    },

    markAllAsRead() {
        return api.patch<ApiResponse<{ updated_count: number }>>(
            "/notifications/read-all",
        );
    },
};

export default notificationRepository;
