import type { JobOfferStatus } from "../types/jobOffer.types";

export function formatStatus(status: JobOfferStatus): string {
    return {
        pending: "Pending",
        accepted: "Accepted",
        rejected: "Declined",
        expired: "Expired",
        withdrawn: "Withdrawn",
    }[status];
}

export function statusClass(status: JobOfferStatus): string {
    return {
        pending: "bg-amber-50 text-amber-700 border-amber-200",
        accepted: "bg-emerald-50 text-emerald-700 border-emerald-200",
        rejected: "bg-rose-50 text-rose-700 border-rose-200",
        expired: "bg-slate-100 text-slate-600 border-slate-200",
        withdrawn: "bg-slate-100 text-slate-500 border-slate-200",
    }[status];
}

export function statusDotClass(status: JobOfferStatus): string {
    return {
        pending: "bg-amber-500",
        accepted: "bg-emerald-500",
        rejected: "bg-rose-500",
        expired: "bg-slate-400",
        withdrawn: "bg-slate-400",
    }[status];
}

export function formatEmploymentType(type?: string | null): string {
    if (!type) return "";

    return (
        (
            {
                full_time: "Full-time",
                part_time: "Part-time",
                contract: "Contract",
                internship: "Internship",
            } as Record<string, string>
        )[type] ?? type.replaceAll("_", " ")
    );
}

export function formatWorkplaceType(type?: string | null): string {
    return (
        (
            {
                onsite: "On-site",
                remote: "Remote",
                hybrid: "Hybrid",
            } as Record<string, string>
        )[type ?? ""] ??
        type ??
        ""
    );
}

export function formatDate(value?: string | null): string {
    if (!value) return "—";

    const date = new Date(value.replace(" ", "T"));

    if (Number.isNaN(date.getTime())) return value;

    return new Intl.DateTimeFormat(undefined, {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(date);
}

export function formatMoney(
    value: number | string | null,
    currencyLabel = "",
): string {
    if (value === null || value === "") return "Not specified";

    const amount = Number(value);

    if (!Number.isFinite(amount)) return String(value);

    return `${currencyLabel ? `${currencyLabel} ` : ""}${new Intl.NumberFormat().format(amount)}`;
}

export function getInitials(name?: string | null): string {
    return (name ?? "?")
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join("")
        .toUpperCase();
}
