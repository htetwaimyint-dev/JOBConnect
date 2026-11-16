
import type { JobListParams } from "../types/job.types";

export const jobKeys = {
    all: ["jobs"] as const,

    lists: () => [...jobKeys.all, "list"] as const,

    list: (params: JobListParams) =>
        [...jobKeys.lists(), params] as const,

    details: () => [...jobKeys.all, "detail"] as const,

    detail: (id: number) =>
        [...jobKeys.details(), id] as const,
};
