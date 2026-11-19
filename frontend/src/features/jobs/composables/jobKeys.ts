import type { MaybeRefOrGetter } from "vue";
import type { JobListParams } from "../types/job.types";

export const jobKeys = {
    all: ["jobs"] as const,

    lists: () => [...jobKeys.all, "list"] as const,

    // Public job list
    list: (params: JobListParams | undefined) =>
        [...jobKeys.lists(), "public", params] as const,

    // Employer's own job list
    employerList: (params: JobListParams | undefined) =>
        [...jobKeys.lists(), "employer", params] as const,

    details: () => [...jobKeys.all, "detail"] as const,

    detail: (id: MaybeRefOrGetter<number | string>) =>
        [...jobKeys.details(), id] as const,
};