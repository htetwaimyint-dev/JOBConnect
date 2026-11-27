import Echo from "laravel-echo";
import Pusher from "pusher-js";
import api from "@/services/api";
import type { AppNotification } from "@/features/notifications/types/notification.types";

declare global {
    interface Window {
        Pusher: typeof Pusher;
    }
}

window.Pusher = Pusher;

export interface NotificationCreatedPayload {
    notification: AppNotification;
}

type NotificationHandler = (payload: NotificationCreatedPayload) => void;

let echo: Echo<"reverb"> | null = null;
let echoToken: string | null = null;

function getCurrentToken(): string | null {
    return localStorage.getItem("token");
}

function disconnectCurrentEcho(): void {
    echo?.disconnect();
    echo = null;
    echoToken = null;
}

function getEcho(): Echo<"reverb"> | null {
    const token = getCurrentToken();

    // Private-channel authorization requires the current user's bearer token.
    if (!token) {
        disconnectCurrentEcho();
        return null;
    }

    // Recreate the connection after logout/login or when the API token is refreshed.
    // Otherwise Echo can keep authorizing private channels with a stale token.
    if (echo && echoToken !== token) {
        disconnectCurrentEcho();
    }

    if (echo) return echo;

    const key = import.meta.env.VITE_REVERB_APP_KEY;
    const wsHost = import.meta.env.VITE_REVERB_HOST;

    if (!key || !wsHost) {
        console.warn(
            "Reverb is not configured. Notification API polling will be used.",
        );
        return null;
    }

    const apiBaseUrl = (api.defaults.baseURL ?? "").replace(/\/$/, "");

    if (!apiBaseUrl) {
        console.error(
            "API base URL is missing; Reverb private-channel authorization cannot be configured.",
        );
        return null;
    }

    const scheme = import.meta.env.VITE_REVERB_SCHEME ?? "https";
    const port = Number(
        import.meta.env.VITE_REVERB_PORT ?? (scheme === "https" ? 443 : 80),
    );

    echo = new Echo<"reverb">({
        broadcaster: "reverb",
        key,
        wsHost,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === "https",
        enabledTransports: ["ws", "wss"],
        authEndpoint: `${apiBaseUrl}/broadcasting/auth`,
        auth: {
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        },
    });

    echoToken = token;
    return echo;
}

export function listenForUserNotifications(
    userId: number | string,
    handler: NotificationHandler,
): () => void {
    const client = getEcho();
    if (!client) return () => undefined;

    const channelName = `users.${userId}`;
    client.private(channelName).listen(".notification.created", handler);

    return () => {
        client.leave(channelName);
    };
}

export function disconnectNotificationEcho(): void {
    disconnectCurrentEcho();
}
