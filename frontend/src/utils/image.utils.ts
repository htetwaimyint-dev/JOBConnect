const STORAGE_BASE_URL = "http://localhost:8000";

export const getImageUrl = (
    path: string | null | undefined,
): string | null => {
    if (!path) return null;

    // Already a full URL
    if (path.startsWith("http://") || path.startsWith("https://")) {
        return path;
    }

    // Convert Laravel storage path to full URL
    return `${STORAGE_BASE_URL}/storage/${path.replace(/^\/+/, "")}`;
};
