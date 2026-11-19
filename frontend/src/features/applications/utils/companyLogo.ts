
export const getCompanyLogoUrl = (logo: string | null): string => {
    if (!logo) {
        return "";
    }

    if (logo.startsWith("http")) {
        return logo;
    }

    return `http://127.0.0.1:8000/storage/${logo.replace(/^\/+/, "")}`;
};
