import type { JobListParams } from "../types/job.types";

export const jobKeys = {
    all: ["jobs"] as const,

    lists: () => [...jobKeys.all, "list"] as const,

    list: (params: JobListParams | undefined) =>
        [...jobKeys.lists(), params] as const,

    details: () => [...jobKeys.all, "detail"] as const,

    employerDetail: (id: number | string) =>
        [...jobKeys.details(), "employer", String(id)] as const,

    publicDetail: (id: number | string) =>
        [...jobKeys.details(), "public", String(id)] as const,
};
