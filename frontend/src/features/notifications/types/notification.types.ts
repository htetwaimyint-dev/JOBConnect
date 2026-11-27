export interface AppNotification {
    id: string;
    type: string;
    data: {
        type?: string;
        title?: string;
        message?: string;
        application_id?: number;
        job_id?: number;
        interview_id?: number;
        [key: string]: unknown;
    };
    read_at: string | null;
    created_at: string | null;
}

export interface NotificationPagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface NotificationListData {
    notifications: AppNotification[];
    meta: NotificationPagination;
}
